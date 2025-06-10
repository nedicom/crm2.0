<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvitoMessage extends Model
{
    /**
     * Таблица, связанная с моделью.
     *
     * @var string
     */
    protected $table = 'avito_messages';

    /**
     * Атрибуты, разрешённые для массового заполнения.
     *
     * @var array
     */
    protected $fillable = [
        'chat_id',
        'message',
        'sender_id',
        'sent_at',
    ];

    /**
     * Типы атрибутов.
     *
     * @var array
     */
    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * Отключаем автоматические временные метки, если не нужны.
     * Если хотите использовать стандартные created_at и updated_at, удалите эту строку.
     *
     * @var bool
     */
    public $timestamps = false;
}
