<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AvitoApiService;
use App\Models\AvitoMessage;

class AvitoBotController extends Controller
{
    public function postmessage(Request $request, AvitoApiService $avitoApiService)
    {
        $data = $request->all();
        // Сохраняем сообщение
        AvitoMessage::create([
            'chat_id' => $data['chat_id'] ?? null,
            'message' => $data['message'] ?? null,
        ]);
        // Пример ответа через сервис
        $avitoApiService->sendMessage($data['chat_id'], 'Спасибо за сообщение!');
        return response()->json(['status' => 'ok']);
    }

    public function registerWebhook(string $webhookUrl)
    {
        $avitoService = app(\App\Services\AvitoApiService::class);

        try {
            $result = $avitoService->registerWebhook('https://nedicom.ru/avito/webhook');
            return response()->json($result);
            // Логика после успешной регистрации
        } catch (\Exception $e) {
            // Обработка ошибки
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showChats()
    {
        $avitoService = app(\App\Services\AvitoApiService::class);

        try {
            $chats = $avitoService->getChats();
            return response()->json($chats);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
