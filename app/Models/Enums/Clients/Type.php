<?php
namespace App\Models\Enums\Clients;

enum Type: string
{    
    case Notchoose = 'Не выбрано';
    case Earth = 'Земельный';
    case War = 'Военный';
    case Pension = 'Пенсионный';
    case Family = 'Семейный';
    case Home = 'Жилищный';
    case Auto = 'Автоюрист';
    case Energy = 'Крымэнерго';
    case Dontknow = 'Другой';
}
