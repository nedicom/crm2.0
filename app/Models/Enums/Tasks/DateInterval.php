<?php

namespace App\Models\Enums\Tasks;

enum DateInterval: string
{
    case Yesterday = 'Вчера';
    case Today = 'Сегодня';
    case Tomorrow = 'Завтра';
    case Day = 'День';
    case Week = 'Неделя';
    case Month = 'Месяц';
    case AllTime = 'Все время';
}
