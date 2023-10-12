<?php

namespace App\Repository;

use App\Models\User;
use Illuminate\Http\Request;
use App\Scopes\UserActiveScope;

class UserRepository
{
    /**
     * Поиск пользователей
     * @param Request $request
     * @return mixed
     */
    public function search(Request $request)
    {
        $query = User::withoutGlobalScope(UserActiveScope::class)->orderByDesc('id');
        $query = (!empty($value = $request->get('name'))) ? $query->where('name', 'LIKE', '%' . $value . '%') : $query;
        $query = (!empty($value = $request->get('email'))) ? $query->where('email', 'LIKE', '%' . $value . '%') : $query;
        $query = (!empty($value = $request->get('status'))) ? $query->where('status', $value) : $query;
        $query = (!empty($value = $request->get('role'))) ? $query->where('role', $value) : $query;

        return $query;
    }
}
