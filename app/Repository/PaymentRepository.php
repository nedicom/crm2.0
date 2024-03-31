<?php

namespace App\Repository;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Payments;
use Illuminate\Http\Request;

class PaymentRepository
{
    /**
     * Поиск платежей для авторизованныз пользователей
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function searchByAdmin(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $query = Payments::with('serviceFunc', 'AttractionerFunc', 'sellerFunc', 'developmentFunc');
        $query = $this->queryParams($query, $request);

        return $query;
    }

    /**
     * Поиск платежей для неавторизованныз пользователей
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function searchByOwner(Request $request)
    {
        $query = Payments::with('serviceFunc', 'AttractionerFunc', 'sellerFunc', 'developmentFunc')
            ->where(function ($query) {
                $currentuser = Auth::id();
                $query->orWhere('nameOfAttractioner', $currentuser)
                    ->orWhere('nameOfSeller', $currentuser)
                    ->orWhere('directionDevelopment', $currentuser);
            });
       $query = $this->queryParams($query, $request);

        return $query;
    }

    private function queryParams(\Illuminate\Database\Eloquent\Builder $query, Request $request)
    {
        if (!empty($request->nameOfAttractioner)) $query->where('nameOfAttractioner', $request->nameOfAttractioner);
        if (!empty($request->nameOfSeller)) $query->where('nameOfSeller', $request->nameOfSeller);
        if (!empty($request->directionDevelopment)) $query->where('directionDevelopment', $request->directionDevelopment);
        if (!empty($request->calculation)) $query->where('calculation', $request->calculation);
        if (!empty($request->month)) $query->whereMonth('created_at', $request->month);
        if (!empty($request->year)) $query->whereYear('created_at', $request->year);

        return $query;
    }

    /**
     * Список платажей по search запросу имени клиента
     * @param string  $clientName Имя клиента
     * @return mixed
     */
    public function getByClientQuery(string $clientName)
    {
        $query = DB::table('payments')
            ->select(['payments.id', 'payments.client', 'payments.created_at', 'services.name AS service_name'])
            ->join('services', 'payments.service', '=', 'services.id')
            ->where('payments.client', 'LIKE', '%' . $clientName . '%')
            ->get();

        return $query;
    }
}
