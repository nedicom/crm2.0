<?php
namespace App\Models\Enums\Clients;

enum Type: string
{    
    case Notchoose = 'Не выбрано';
    case Earth = 'Земельный';
    case Inheritance = 'Наследственный';
    case WorldWide = 'Международный';
    case Tax = 'Налоговый';
    case War = 'Военный';
    case Pension = 'Пенсионный';
    case Family = 'Семейный';
    case Home = 'Жилищный';
    case Auto = 'Автоюрист';
    case Energy = 'Крымэнерго';
    case Migration = 'Миграционный';
    case Customers = 'Потребительский';
    case Dontknow = 'Другой';
}
