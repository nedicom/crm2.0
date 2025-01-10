<?php

namespace App\Helpers;

use App\Models\Enums\Clients\Type;
use Illuminate\Support\Facades\Request;

class ClientHelper
{
    public static function typeList($data): string
    {
        $html  = "<label for='casettype'><small>Тип дела</small></label>";
        $html   .= "<select class='form-select' name='casettype' id='casettype'>";
        $html .= "<option disabled>тип дела</option>";
        foreach (Type::cases() as $case) {
            $selected = ($data == $case->value) ? 'selected' : '';
            $html .= "<option value='{$case->value}' $selected>{$case->value}</option>";
        }
        $html .= "</select>";

        return $html;
    }
}