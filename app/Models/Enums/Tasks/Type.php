<?php

namespace App\Models\Enums\Tasks;

enum Type: string
{
    case Task = 'задача';
    case Meeting = 'заседание';
    case Questioning = 'допрос';
    case Ring = 'звонок';
    case Consultation = 'консультация';
    case Control = 'контроль';
    case Sending = 'отправка';
}
