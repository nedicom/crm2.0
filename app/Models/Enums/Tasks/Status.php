<?php

namespace App\Models\Enums\Tasks;

enum Status: string
{
    case IN_WORK = 'в работе';
    case OVERDUE = 'просрочена';
    case COMPLETE = 'выполнена';
    case WAITING = 'ожидает';
}
