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
            case $user::ROLE_LEAD_HANDLER:
                $role = 'Лидменеджер';
                break;
            case $user::ROLE_USER_SERVICE_CLIENTS:
                $role = 'Юрист по работе с клиентами';
                break;
            default:
                $role = 'Пользователь';
        }

        return $role;
    }

    public static function nameStatus(User $user): string
    {
        $status = $user->isActive() ? 'Активен' : ($user->isWait() ? 'Ожидает' : 'Выключен');

        return $status;
    }

    /** Форматирование номера телефона */
    public static function formatPhone(array $phones): array
    {
        return preg_replace(['/^(\+|[0-8]+|\s)+/', '/([\s|\(|\)|-]+)+/'], '', $phones);
    }
}
