<?php

namespace App\Services\TG;

use App\Models\Cities;
use App\Models\User;

class LeadTg
{
    /**
     * Отправляем новый лид в ТГ группу
     * @return void
     */
    public static function SendleadTg($lead)
    {
        //чекаем сущестование полей. Внимание, laravel выдает string "null", is_null не сработает
        $lawyer = (!is_null(User::find($lead->lawyer))) ? User::find($lead->lawyer)->name : 'Авдокатский кабинет';
        $responsible = (!is_null(User::find($lead->responsible))) ? User::find($lead->responsible)->name : 'Авдокатский кабинет';
        $casettype = ($lead->casettype === "null" || !$lead->casettype) ? 'Не выбрано' : $lead->casettype;
        $source = $lead->source === "null" ? 'не знаю источник' : $lead->source;
        $description = $lead->description === "null" ? 'Описание отсутствует' : $lead->description;

        $value = "Новый лид\nТип дела - " . $casettype . "\Привлек - " . $lawyer .  "\nОтветственный - " . $responsible . "\nИсточник - " . $source . "\n" . $description . "\nhttps://crm.nedicom.ru/leads/" . $lead->id;
        $text = urlencode($value);

        //идентификаторы
        $token = env('TG_NEWLEAD_TOKEN');
        $group_name = env('TG_NEWLEAD_GROUP');

        //запускаем
        file_get_contents("https://api.telegram.org/bot$token/sendMessage?chat_id=$group_name&text=$text");
    }
}
