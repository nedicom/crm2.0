<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AvitoApiService;
use App\Services\Gpt\GptService;
use App\Models\AvitoChat;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AvitoBotController extends Controller
{

    public function getmessage(Request $request)
    {
        try {
            // Извлекаем необходимые поля с проверкой наличия
            $chatId = $request->input('payload.value.chat_id');
            $messageText = $request->input('payload.value.content.text');
            $authorId = (string)$request->input('payload.value.author_id');

            if ($authorId === '320878714') {
                return response()->json(['status' => 'success'], 200);
            }

            // Проверяем обязательные поля
            if (!$chatId || !$messageText) {
                Log::error('Error saving Avito message: empty request');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Missing required fields: chat_id or message text.'
                ], 422);
            }

            // Проверяем, есть ли чат с таким chat_id
            $chatExists = DB::table('avito_chats')->where('chat_id', $chatId)->exists();

            if (!$chatExists) {
                // Добавляем новый чат с chat_id
                DB::table('avito_chats')->insert([
                    'chat_id' => $chatId,
                    'is_gpt_active' => 1, // или другое значение по умолчанию
                ]);
            }

            // проверяем наличие записи
            $isGptActive = DB::table('avito_chats')
                ->where('chat_id', $chatId)
                ->value('is_gpt_active');

            // Проверка, что GPT активен
            if ($isGptActive == 1 && $authorId !== '320878714') {
                $array_conversation = app(AvitoApiService::class)->getMessages($chatId, 320878714);
                // Преобразуем массив в JSON-строку
                $content = json_encode($array_conversation, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                // Записываем в файл (например, storage/app/data.json)
                Storage::put('1.json', $content);

                $answer = GptService::Answer($array_conversation);

                $filename = '4.json';

                // Проверяем, существует ли файл
                if (Storage::exists($filename)) {
                    // Читаем текущее содержимое файла
                    $existingContent = Storage::get($filename);
                } else {
                    $existingContent = '';
                }

                // Добавляем новый текст (например, с переводом строки)
                $newContent = $existingContent . "\n" . $answer;

                // Записываем обновлённое содержимое обратно в файл
                Storage::put($filename, $newContent);

                app(AvitoApiService::class)->sendMessage(320878714, $chatId, $answer);

                return response()->json(['status' => 'success'], 200);
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error('Exception in getmessage: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error'
            ], 500);
        }
    }

    public function avitoChats()
    {
        $chats = app(AvitoApiService::class)->getChats();

        if (is_array($chats) && !empty($chats)) {
            foreach ($chats as &$chat) {
                // Получаем или создаём запись в базе для чата
                $chatModel = AvitoChat::firstOrCreate(
                    ['chat_id' => $chat['id']],
                    [
                        'gpt_prompt' => '',
                        'is_gpt_active' => true,
                    ]
                );
                // Добавляем значение в массив чата для Blade
                $chat['is_gpt_active'] = $chatModel->is_gpt_active;
            }
            unset($chat);
        } else {
            // Обработка случая, когда $chats пустой или не массив
            $chats = []; // или другая логика обработки ошибки
        } // Разрываем ссылку

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

    // ajax request
    public function updateGptActive(Request $request)
    {
        $updated = DB::table('avito_chats')
            ->where('chat_id', $request->id)
            ->update(['is_gpt_active' => $request->is_gpt_active]);

        if ($updated) {
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }
}
