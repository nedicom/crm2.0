<?php

namespace App\Repository;

use App\Models\Services;

class ServiceRepository
{
    /**
     * Коллекция всех услуг
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        $query = Services::select(['id', 'name', 'execution_time', 'type_execution_time'])
            ->orderBy('name', 'asc')
            ->limit(10)
            ->get();

        return $query;
    }

    /**
     * Поиск по именам услуг
     * @param string $query
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByNames(string $query)
    {
        $query = Services::select(['id', 'name', 'execution_time', 'type_execution_time'])
            ->where('name', 'LIKE', '%' . $query . '%')
            ->orderBy('name', 'ASC')
            ->get();

        return $query;
    }
}
