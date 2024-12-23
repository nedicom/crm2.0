<?php

namespace App\Services\TG;

class LeadTg
{
    /**
     * Отправляем новый лид в ТГ
     * @return void
     */
    public static function SendleadTg($value)
    {
        $token = env('TG_NEWLEAD_TOKEN');
        $group_name = env('TG_NEWLEAD_GROUP');
        $text = urlencode($value);
        file_get_contents("https://api.telegram.org/bot$token/sendMessage?chat_id=$group_name&text=$text");
    }
}
