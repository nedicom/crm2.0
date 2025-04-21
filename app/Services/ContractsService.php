<?php

namespace App\Services;

use App\Models\ClientsModel;
use App\Models\Dogovor;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \ZipArchive;

class ContractsService
{
    public function new($today, $contractUrl, Request $request): ?Dogovor
    {
        return DB::transaction(function () use ($request, $today, $contractUrl) {
            $contract = Dogovor::new($request, $today, $contractUrl);
            $contract->saveOrFail();

            /** @var ClientsModel $client */
            $client = ClientsModel::find($request->input('clientidinput'));
            $client->editFromClient($request);
            $client->saveOrFail();

            return $contract;
        });
    }

    public function attachFile(string $contractUrl, $today, Request $request): string
    {
        $data = $request->only(['client', 'adress', 'phone', 'subject', 'allstoimost', 'preduslugi', 'predoplata']);
       
        if ($request->ispolnitelinput == 'ipmina') {
            $performer = env('COMPANY_NAME', 'Индивидуальный предприниматель Мина Ольга Викторовна');
            $psthxml = public_path('dogovor-template/ip/document.xml');
            $tmpFile = storage_path('app/public/dogovor/ip/soglashenie.docx');  
        } else {
            $performer = env('COMPANY_NAME', 'Адвокатский кабинет Мина Марк Анатольевич');
            $psthxml = public_path('dogovor-template/document.xml');
            $tmpFile = storage_path('app/public/dogovor/soglashenie.docx');  
        }

        $addressPerformer = env('COMPANY_ADDRESS', '295000, РФ, Респ. Крым, ул. Долгоруковская 5');
        $phonePerformer = env('COMPANY_PHONE', '+7978 8838 978');

        $requisitesContracts = [
            'field_calendar', 'field_ispolnitel', 'field_adresispolnitelya', 'field_kontaktyispolnitelya', 'field_fio',
            'field_addres', 'field_phone', 'field_uslugi', 'field_allstoimost', 'field_preduslugi', 'field_predoplata',
        ];
        $requisitesContractsVar = [
            $today, $performer, $addressPerformer, $phonePerformer,
        ];
        $requisitesContractsVar = array_merge(
            $requisitesContractsVar,
            $data,
        );  

        $zip = new ZipArchive; // пакуем в архив наши переменные

        if ($zip->open($tmpFile) === true) {              
            $handle = fopen($psthxml, "r");
            $content = fread($handle, filesize($psthxml));
            fclose($handle);
            $content = str_replace($requisitesContracts, $requisitesContractsVar, $content);
            dd($content);
            $zip->deleteName('word/document.xml');
            $zip->addFromString('word/document.xml',$content);
            $zip->close();
        }

        $file = ($tmpFile);	// заменили содержимое файла на сервере
        copy($file, $contractUrl);  // копируем обработанный договор в общую папку, для виндовс смотрим запрещенные символы в названии файла
        session()->flash('url', $contractUrl);

        if (!file_exists($file)) {
            throw new \DomainException('Ошибка загрузки файла');
        }

        return $file;
    }
}
