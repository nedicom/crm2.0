<?php

namespace App\Http\Controllers;

use App\Models\Leads;
use Illuminate\Support\Facades\Log;

/** Класс генерации .doc файлов */
class CsvController extends Controller
{
    /**
     * Генерация документа для яндекс метрики оффлайн конверсии
     * @param Leads $leads
     */

    public function leads()
    {
        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=leads.csv',
            'Expires'             => '0',
            'Pragma'              => 'public'
        ];

        $list = Leads::latest()->take(15)->get(['created_at as create_date_time', 'phone as phones', 'status as order_status', 'service as revenue'])->toArray();;

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



        $counter = "24900584";            // номер счетчика
        $token = "y0_AgAAAAAPGhtRAAzhMQAAAAEaxRB_AAAEQ_XYWPNHy5srO2Eq0mSoTaVgPA";              // OAuth-токен (https://oauth.yandex.ru/client/5e1219b00d32468b9f8e960d6ec0f6ca)

        $curl = curl_init("https://api-metrika.yandex.net/cdp/api/v1/counter/$counter/data/simple_orders?merge_mode=SAVE&delimiter_type=COMMA");

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, array('file' => curl_file_create(realpath('data.csv'))));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/csv", "Authorization: OAuth $token"));

        $result = curl_exec($curl);

        echo $result;

        curl_close($curl);



        //return response()->stream($callback, 200, $headers);


        /*public function certificateCompletion(LeadsModel $client)
    {
        try {
            $templateFile = storage_path("app/public/dogovor/template_certificate_completion.docx");
            // Генерация документа
            $generateFile = ($this->service)($templateFile, $client);

            return response()->download($generateFile,"client_{$client->id}_certificate_completion.docx", [
                'Content-Type' => 'application/docx',
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }*/
    }

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
