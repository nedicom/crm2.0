<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class YandexmapController extends Controller
{
    /**
     * скачать xls for yandex bussiness 
     */

    public function create(Request $request)
    {
        $main_keywords = [];
        if ($request->file('yandexcsv')) {
            $csvAsArray = [];
            $content = File::get($request->file('yandexcsv'));
            $lines = explode(PHP_EOL, $content);
            array_pop($lines);
            foreach ($lines as $line) {
                $csvAsArray[] = str_getcsv($line);
            }
            foreach ($csvAsArray as $key => $csv) {
                $main_keywords[$key][0] = $csv[0];
                array_shift($csv);
                $main_keywords[$key][1] = $csv[0];
                array_shift($csv);
                foreach ($csv as $data) {
                    if ($data != "") {
                        $main_keywords[$key][2][] = $data;
                    }
                }
            }
        }


        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'        => 'text/xls',
            'Content-Disposition' => 'attachment; filename=yandexmap.xls',
            'Expires'             => '0',
            'Pragma'              => 'public'
        ];

        $city = $request->city;

        $tableheader = [
            "Категория",
            "Название",
            "Идентификатор",
            "Описание",
            "Короткое описание",
            "Цена",
            "Фото",
            "Популярный товар",
            "В наличии",
            "Количество",
            "Единицы измерения",
            "Ссылка"
        ];

        $list = [0 => $tableheader];

        $i = 1;

        foreach ($main_keywords as $category) {

            $summary = new Summary();
            $list[] = $summary->set_data(
                $category[0],
                null,
                $i,
                1000,
                $category[1],
                "да",
                $city
            );

            $i++;

            foreach ($category[2] as $name) {
                $summary = new Summary();
                $list[] = $summary->set_data(
                    $category[0],
                    $name,
                    $i,
                    1000,
                    $category[1],
                    "нет",
                    $city
                );

                $i++;
            };
        };

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

class Summary
{
    // Properties
    public $category;
    public $name;
    public $id;
    public $summary;
    public $description;
    public $price;
    public $img;
    public $popular;
    public $in_stock;
    public $city;
    public $arr;
    // Methods
    function set_data($category, $name, $id, $price, $img, $popular, $city)
    {
        $this->description = "description";
        $sum = $name ? $name . " - " : null;
        $this->summary = $sum . $category . ", " . $city . ". Цена указана за консультацию. Уточняйте возможность получить юридическую консультацию перед началом работы онлайн или бесплатно.";
        $this->description = $sum . $category . ", " . $city . ". Цена указана за консультацию. Уточняйте возможность получить юридическую консультацию перед началом работы онлайн или бесплатно.";
        $name = $name ? $name . ' - ' . $category : $category;
        $arr = [$category, $name, $id, $this->summary, $this->description, $price, $img, $popular, 'да', 1, 'шт.'];
        return ($arr);
    }
}
