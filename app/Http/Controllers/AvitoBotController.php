<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AvitoApiService;
use App\Models\AvitoMessage;

class AvitoBotController extends Controller
{

    public function getmessage(Request $request)
    {
        $data = $request->all();

        // Пример извлечения данных из структуры webhook
        $chatId = $data['chat_id'] ?? null;
        $messageText = $data['message']['content']['text'] ?? null;
        $authorId = $data['message']['author_id'] ?? null;
        $createdAt = isset($data['message']['created']) ? date('Y-m-d H:i:s', $data['message']['created']) : null;

        if ($chatId && $messageText) {
            AvitoMessage::create([
                'chat_id' => $chatId,
                'message' => $messageText,
                'author_id' => $authorId,
                'created_at_message' => $createdAt,
            ]);
        }

        return response('Webhook received', 200);
    }
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

    public function registerWebhook()
    {
        $avitoService = app(\App\Services\AvitoApiService::class);

        try {
            $result = $avitoService->registerWebhook('https://nedicom.ru/api/avito/getmessage');
            if (isset($result['success']) && $result['success']) {
                return response()->json(['message' => 'Webhook успешно зарегистрирован', 'data' => $result]);
            } else {
                return response()->json(['error' => 'Ошибка регистрации вебхука', 'details' => $result], 400);
            }
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
