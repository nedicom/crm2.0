<?php

namespace App\Models\Enums\Clients;

enum Rating: string
{
    case Positive = 'Положительный';
    case Neutral = 'Нейтральный';
    case Negative = 'Отрицательный';
}
