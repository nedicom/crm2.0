<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AvitoApiService;
use App\Services\Gpt\GptService;
use App\Models\AvitoChat;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AvitoBotController extends Controller
{

    public function getmessage(Request $request)
    {
        // Получаем все данные из запроса
        $data = $request->all();

        try {
            // Извлекаем необходимые поля с проверкой наличия
            $chatId = $request->input('payload.value.chat_id');
            $messageText = $request->input('payload.value.content.text');
            $authorId = $request->input('payload.value.author_id');

            // Проверяем обязательные поля
            if (!$chatId || !$messageText) {
                Log::error('Error saving Avito message: empty request - ' . $chatId);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Missing required fields: chat_id or message text.'
                ], 422);
            }

            // даем ответ
            if ((string)$authorId != '320878714') {

                $array_conversation = app(AvitoApiService::class)->getMessages($chatId, 320878714);
                //Storage::put('request_log.json', json_encode($array_conversation));
                $answer = GptService::Answer($array_conversation);

                $postData = [
                    'chat_id' => $chatId,
                    'message' => $answer,
                ];
                /*
                $postData = [
                    'chat_id' => $chatId,
                    'message' =>'перезвоню',
                ];
                */
                $newRequest = new Request($postData);
                $this->postmessage($newRequest);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Message saved successfully.'
            ], 200);
        } catch (\Exception $e) {
            // Логируем ошибку
            Log::error('Error saving Avito message: ' . $e->getMessage(), ['data' => $data]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function postmessage(Request $request)
    {
        $data = $request->all();
        // Пример ответа через сервис
        $ok = app(AvitoApiService::class)->sendMessage(320878714, $data['chat_id'], $data['message']);
        return response()->json(['status' => $ok]);
    }

    public function avitoChats()
    {
        $chats = app(AvitoApiService::class)->getChats();
        foreach ($chats as $chat) {
            AvitoChat::firstOrCreate(
                ['chat_id' => $chat['id']],
                [
                    'gpt_prompt' => '',
                    'is_gpt_active' => true,
                ]
            );
        }
        return view('avito/avito_chats', compact('chats'));
    }

    public function avitoChat($chat_id)
    {
        $messages = [];

        return view('avito/avito_chat', compact('messages', 'chat_id'));
    }

    public function registerWebhook()
    {
        $avitoService = app(\App\Services\AvitoApiService::class);

        try {
            $result = $avitoService->registerWebhook('https://crm.nedicom.ru/api/avito/getmessage');
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
