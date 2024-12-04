<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Leads;

class CsvController extends Controller
{
    /**
     * отправка данных из crm в метрику
     * @param Leads $leads
     */

    private function getInfoYa($url, $token, $data, $boundary)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host:api-metrika.yandex.net', 'Authorization: OAuth ' . $token, "Content-Type: multipart/form-data; boundary=------------------------$boundary", "Content-Length: " . strlen($data)));

        $response = [];
        $response['html']     = curl_exec($ch);
        $response['err']      = curl_errno($ch);
        $response['errmsg']   = curl_error($ch);
        $response['header']   = curl_getinfo($ch);

        curl_close($ch);

        return $response;
    }

    public function leads()
    {
        $token = "y0_AgAAAAAPGhtRAAzhMQAAAAEaxRB_AAAEQ_XYWPNHy5srO2Eq0mSoTaVgPA";
        $counterId = 24900584;
        $url = "https://api-metrika.yandex.net/cdp/api/v1/counter/" . $counterId . "/data/simple_orders?merge_mode=SAVE&delimiter_type=COMMA";
        $boundary = "7zDUQOAIAE9hEWoV";
        $filename = 'data.csv';

        $list = Leads::latest()
            ->where('created_at', '>', Carbon::now()->subDays(21))
            ->where('phone', '!=', '')
            ->get(['id', 'created_at', 'phone', 'status', 'service'])->map(function ($list) {
                if ($list->status == "конвертирован") {
                    $list->status = "PAID";
                    $list->service = "5000";
                } elseif ($list->status == "удален") {
                    $list->status = "CANCELLED";
                    $list->service = "";
                } else {
                    $list->status = "IN_PROGRESS";
                    $list->service = "100";
                };                
                $list->new_date =  date_create_from_format("Y-m-d H:i:s", $list->created_at)->format('d.m.Y H:i');
                $list->phone = preg_replace("/[^0-9]/", '', $list->phone);
                substr($list->phone, 0, 1) == "8" ? $list->phone = "7" . ltrim($list->phone, '8') : null;
                substr($list->phone, 0, 1) != "7" ? $list->phone = "7" . ltrim($list->phone, '8') : null;                    
                return $list;
            })->filter(function ($item) {
                if(strlen($item->phone) == 11){return $item;};
            })->toArray();
        $orders = "id,create_date_time,client_uniq_id,client_ids,emails,phones,order_status,revenue,cost,goals,currency" . PHP_EOL;
        foreach ($list as $lead) {
            $orders .= $lead['id'] . "," . $lead['new_date'] . ",,,," . $lead['phone'] . "," . $lead['status'] . ",," . $lead['service'] . ",,RUB" . PHP_EOL;
        }

        $data = "--------------------------$boundary\x0D\x0A";
        $data .= "Content-Disposition: form-data; name=\"file\"; filename=\"$filename\"\x0D\x0A";
        $data .= "Content-Type: text/csv\x0D\x0A\x0D\x0A";
        $data .= $orders;
        //$data .= $orders . "\x0A\x0D\x0A";
        $data .= "--------------------------$boundary--";
        //dd($data);
        $yaInfo = $this->getInfoYa($url, $token, $data, $boundary);
        print_r(json_decode($yaInfo["html"], true));
    }

    /**
     * скачать лиды в csv
     * @param Leads $leads
     */
    public function downloadleads()
    {
        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=leads.csv',
            'Expires'             => '0',
            'Pragma'              => 'public'
        ];

        $list = Leads::all()->toArray();

        # add headers for each column in the CSV download
        array_unshift($list, array_keys($list[0]));

        $callback = function () use ($list) {
            $FH = fopen('php://output', 'w');
            foreach ($list as $row) {
                fputcsv($FH, $row);
            }
            fclose($FH);
        };
        return response()->stream($callback, 200, $headers);
    }
}