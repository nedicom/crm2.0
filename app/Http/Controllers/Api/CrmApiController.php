<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientsModel;

class CrmApiController extends Controller
{
    public function clientSummary($email)
    {
        // Находим клиента
        $email = urldecode($email);

        $client = ClientsModel::where('email', $email)
            ->with(['paymentsForClient' => function ($q) {
                $q->select('id', 'summ', 'clientid', 'created_at')
                    ->orderBy('created_at', 'desc');
            }])
            ->with(['tasksForClient' => function ($q) {
                $q->select('id', 'name', 'status', 'clientid', 'created_at', 'status', 'donetime', 'hrftodcm')
                    ->orderBy('created_at', 'desc');
            }])
            ->first(['id', 'name', 'email']);
        /* 
        $client = ClientsModel::where('email', $email)
            ->with(['paymentsForClient', 'tasksForClient'])
            ->first();
        */
        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Клиент не найден'
            ], 404);
        } else {
            $client->payments_count = $client->paymentsForClient->count();
            $client->tasks_count = $client->tasksForClient->count();
        }

        // Возвращаем минимум данных
        return response()->json([
            'success' => true,
            'data' => $client,
        ]);
    }
}
