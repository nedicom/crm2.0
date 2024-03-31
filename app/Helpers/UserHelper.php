<?php

namespace App\Helpers;

use App\Models\User;

class UserHelper
{
    public static function nameRole(User $user): string
    {
        switch ($user->role) {
            case $user::ROLE_ADMIN:
                $role = 'Администратор';
                break;
            case $user::ROLE_MODERATOR:
                $role = 'Модератор';
                break;
            case $user::ROLE_HEAD_LAWYER:
                $role = 'Начальник юр. отдела';
                break;
            case $user::ROLE_HEAD_SALES:
                $role = 'Начальник отдела продаж';
                break;
            default:
                $role = 'Пользователь';
        }

        return $role;
    }

    public static function nameStatus(User $user): string
    {
        $status = ($user->isWait()) ? 'Ожидает' : 'Активен';

        return $status;
    }

    /** Форматирование номера телефона */
    public static function formatPhone(array $phones): array
    {
        return preg_replace(['/^(\+|[0-8]+|\s)+/', '/([\s|\(|\)|-]+)+/'], '', $phones);
    }
}
