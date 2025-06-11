<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AvitoApiService;
use App\Services\Gpt\GptService;
use App\Models\AvitoMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AvitoBotController extends Controller
{

    public function getmessage(Request $request)
    {
        // Получаем все данные из запроса
        $data = $request->all();

        // Преобразуем массив в JSON для удобного хранения
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Записываем JSON в файл в хранилище Laravel (например, в storage/app/request_log.json)
        //Storage::put('request_log.json', $json);

        try {
            // Извлекаем необходимые поля с проверкой наличия
            $chatId = $request->input('payload.value.chat_id');
            $messageText = $request->input('payload.value.content.text');
            $authorId = $request->input('payload.value.author_id');
            $createdTimestamp = $request->input('payload.value.created');

            // Проверяем обязательные поля
            if (!$chatId || !$messageText) {
                Log::error('Error saving Avito message: empty request - ' . $chatId);
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
                'sender_id' => $authorId,
                'sent_at' => $createdAt,
            ]);

            // даем ответ
            if ((string)$authorId == '320878714') {
                $array_conversation = AvitoMessage::where('chat_id', $chatId)
                    ->orderBy('sent_at', 'asc')
                    ->get();

                $answer = GptService::Answer($array_conversation);
                Storage::put('request_log.json', $answer);
                /*
                
                $postData = [
                    'chat_id' => $chatId,
                    'message' => $answer,
                ];

                $newRequest = new Request($postData);
                $this->postmessage($newRequest);
                */
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
                'message' => 'Failed to save message.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function postmessage(Request $request)
    {
        $data = $request->all();
        // Сохраняем сообщение
        AvitoMessage::create([
            'chat_id' => $data['chat_id'] ?? null,
            'message' => $data['message'] ?? null,
        ]);
        // Пример ответа через сервис
        $ok = app(AvitoApiService::class)->sendMessage(320878714, $data['chat_id'], $data['message']);
        return response()->json(['status' => $ok]);
    }

    public function avitoChats()
    {
        $chats = DB::table('avito_messages')
            ->select('chat_id', DB::raw('MAX(sent_at) as last_message_at'))
            ->groupBy('chat_id')
            ->orderByDesc('last_message_at')
            ->get();
        return view('avito/avito_chats', compact('chats'));
    }

    public function avitoChat($chat_id)
    {
        $messages = AvitoMessage::where('chat_id', $chat_id)
            ->orderBy('sent_at', 'asc')
            ->get();

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
