<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AvitoApiService;
use App\Models\AvitoMessage;
use Illuminate\Support\Facades\Log;

class AvitoBotController extends Controller
{

    public function getmessage(Request $request)
    {
        // Получаем все данные из запроса
        $data = $request->all();

        try {
            // Извлекаем необходимые поля с проверкой наличия
            $chatId = $data['chat_id'] ?? null;
            $messageText = $data['message']['content']['text'] ?? null;
            $authorId = $data['message']['author_id'] ?? null;
            $createdTimestamp = $data['message']['created'] ?? null;
dd(1);
            // Проверяем обязательные поля
            if (!$chatId || !$messageText) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Missing required fields: chat_id or message text.'
                ], 422);
            }

            // Преобразуем timestamp в формат даты, если есть
            $createdAt = $createdTimestamp ? date('Y-m-d H:i:s', $createdTimestamp) : now();

            // Создаем запись в базе
            AvitoMessage::create([
                'chat_id' => $chatId,
                'message' => $messageText,
                'author_id' => $authorId,
                'created_at_message' => $createdAt,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Message saved successfully.'
            ], 200);
        } catch (\Exception $e) {
            // Логируем ошибку
            Log::error('Error saving Avito message: ' . $e->getMessage(), ['data' => $data]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save message.',
                'error' => $e->getMessage()
            ], 500);
        }
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
