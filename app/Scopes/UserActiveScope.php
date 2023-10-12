<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserActiveScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        return $builder->where('status', User::STATUS_ACTIVE);
    }
}
