<?php

namespace App\Services\TG;

use App\Models\Cities;
use App\Models\User;
use App\Models\Tasks;

class LeadTg
{
    /**
     * Отправляем новый лид в ТГ группу
     * @return void
     */
    public static function SendleadTg($lead)
    {
        //чекаем сущестование полей. Внимание, laravel выдает string "null", is_null не сработает
        $city = $lead->city_id === "null" ? 'не определен' : Cities::find($lead->city_id)->city;
        $responsible = $lead->responsible === "null" ? 'не определен' : User::find($lead->responsible)->name;
        $casettype = $lead->casettype === "null" ? 'Не выбрано' : $lead->casettype;
        $source = $lead->source === "null" ? 'не знаю источник' : $lead->source;
        $description = $lead->description === "null" ? 'Описание отсутствует' : $lead->description;

        //содержание сообщения
        $value = "Новый лид\nГород - " . $city . "\nТип дела - " . $casettype . "\nОтветсвенный - " . $responsible . "\Источник - " . $source . "\n" . $description . "\nhttps://crm.nedicom.ru/leads/" . $lead->id;
        $text = urlencode($value);

        //идентификаторы
        $token = env('TG_NEWLEAD_TOKEN');
        $group_name = env('TG_NEWLEAD_GROUP');

        //запускаем
        file_get_contents("https://api.telegram.org/bot$token/sendMessage?chat_id=$group_name&text=$text");
    }
}
