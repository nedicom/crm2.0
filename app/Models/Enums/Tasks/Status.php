<?php

namespace App\Models\Enums\Tasks;

enum Status: string
{
    case WAITING = 'ожидает';
    case OVERDUE = 'просрочена';
    case IN_WORK = 'в работе';
    case COMPLETE = 'выполнена';
}
