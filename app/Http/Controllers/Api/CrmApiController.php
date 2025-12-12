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
        $client = ClientsModel::where('email', $email)->first();
        
        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Клиент не найден'
            ], 404);
        }
        
        // Возвращаем минимум данных
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
            ]
        ]);
    }
}