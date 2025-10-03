<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prompt extends Model
{
    // Указываем, что модель использует таблицу 'texts' (по умолчанию Laravel берет имя во множественном числе)
    protected $table = 'prompt';

    // Разрешаем массовое заполнение поля content
    protected $fillable = ['prompt'];

    // Поля created_at и updated_at автоматически поддерживаются Eloquent, указывать их не нужно
}
