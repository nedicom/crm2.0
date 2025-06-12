<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvitoChat extends Model
{
    // Явно указать имя таблицы, если не совпадает с именем модели во множественном числе
    protected $table = 'avito_chats';

    // Разрешённые для массового заполнения поля
    protected $fillable = [
        'chat_id',
        'gpt_prompt',
        'is_gpt_active',
    ];

    // Автоматическое преобразование типов полей
    protected $casts = [
        'is_gpt_active' => 'boolean',
    ];
}
