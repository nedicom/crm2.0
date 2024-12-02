<?php

namespace App\Http\Controllers;

use App\Models\Leads;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

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

        $list = Leads::latest()->take(15)->get(['created_at as create_date_time', 'phone as phones', 'status as order_status', 'service as revenue']);
        $list->map(function ($list) {
            //dd($list->create_date_time);
            $list->create_date_time =  date_create_from_format("Y-m-d H:i:s", $list->create_date_time)->format('d.m.Y H:i');
            return $list;
        });

        $list = $list->toArray();


        # add headers for each column in the CSV download
        array_unshift($list, array_keys($list[0]));

        $callback = function () use ($list) {

            $FH = fopen('php://output', 'w');
            foreach ($list as $row) {
                fputcsv($FH, $row);
            }

            /*$response = Http::withoutVerifying()
                ->withOptions(["verify" => false])->post('https://webhook.site/26dc0d32-c3fa-45a0-b0ca-aa82a5595607', [
                    'name' => 'Steve',
                    'role' => 'Network Administrator',
                ]);
                

            $curl = curl_init("https://webhook.site/26dc0d32-c3fa-45a0-b0ca-aa82a5595607");

            curl_setopt($curl, CURLOPT_FILE, $FH);
            curl_setopt($curl, CURLOPT_HEADER, 0);

            
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, array('file' => curl_file_create(realpath('data.csv'))));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/csv"));
            //curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/csv", "Authorization: OAuth $token"));
            

            $result = curl_exec($curl);
            
            echo $result;

            curl_close($curl);*/

            fclose($FH);
        };

        $callback();

        //return response()->stream($callback, 200, $headers);



        $counter = "24900584";            // номер счетчика
        $token = "y0_AgAAAAAPGhtRAAzhMQAAAAEaxRB_AAAEQ_XYWPNHy5srO2Eq0mSoTaVgPA";              // OAuth-токен (https://oauth.yandex.ru/client/5e1219b00d32468b9f8e960d6ec0f6ca)

        //$curl = curl_init("https://api-metrika.yandex.net/cdp/api/v1/counter/$counter/data/simple_orders?merge_mode=SAVE&delimiter_type=COMMA");




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
