<?php

namespace App\Helpers;

use App\Models\Enums\Leads\Status;
use Illuminate\Support\Facades\Request;

class LeadHelper
{
    public static function statusList(): string
    {
        $html  = "<select class='form-select' name='checkedstatus' id='checkedstatus'>";
        $html .= "<option disabled value=''>статус</option>";
        foreach (Status::cases() as $case) {
            $selected = (Request::input('checkedstatus') == $case->value) ? 'selected' : '';
            $html .= "<option value='{$case->value}' $selected>{$case->value}</option>";
        }
        $html .= "</select>";

        return $html;
    }
}
