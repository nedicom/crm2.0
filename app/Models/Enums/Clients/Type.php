<?php
namespace App\Models\Enums\Clients;

enum Type: string
{    
    case Notchoose = 'Не выбрано';
    case Earth = 'Земельный';
    case Inheritance = 'Наследственный';
    case Home = 'Жилищный';
    case LandLord = 'По недвижимости';
    case WorldWide = 'Международный';
    case Tax = 'Налоговый';
    case War = 'Военный';
    case Pension = 'Пенсионный';
    case Family = 'Семейный';
    case Debt = 'Долговой';       
    case Auto = 'Автоюрист';
    case Energy = 'Крымэнерго';
    case Migration = 'Миграционный';
    case Customers = 'Потребительский';
    case Bussines = 'Сопровождение';
    case Dontknow = 'Другой';
}
