<?php

namespace App\Http\Controllers;

class YandexmapController extends Controller
{
    /**
     * скачать xls for yandex bussiness 
     */

    public function create()
    {
        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'        => 'text/xls',
            'Content-Disposition' => 'attachment; filename=yandexmap.xls',
            'Expires'             => '0',
            'Pragma'              => 'public'
        ];

        $list = [0 => [1], 1=>[1], 2=>[1]];

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