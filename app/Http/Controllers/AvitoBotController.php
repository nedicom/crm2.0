<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AvitoApiService;
use App\Services\Gpt\GptService;
use App\Models\AvitoChat;
use App\Models\Prompt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class AvitoBotController extends Controller
{

    public function getmessage(Request $request)
    {
        try {

            // Извлекаем необходимые поля с проверкой наличия
            $chatId = $request->input('payload.value.chat_id');
            $messageText = $request->input('payload.value.content.text');
            $authorId = (string)$request->input('payload.value.author_id');
            $messageId = $request->input('payload.value.id') ?? null;

            //Log::info('getmessage called', ['chat_id' => $chatId, 'authorId' => $authorId, 'first' => 2, 'pay' => $request->input('payload.value'), 'time' => now()]);
            // Проверяем, обработано ли уже это сообщение
            if (Cache::has('avito_message_' . $messageId)) {
                Log::info("Duplicate webhook ignored for message_id: $messageId");
                return response()->json(['status' => 'success'], 200);
            }

            // Для простоты отметим, что сообщение обработано
            Cache::put('avito_message_' . $messageId, true, 300);

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

            $latestPrompt = Prompt::orderBy('id', 'desc')->first();

            $prompt = $latestPrompt && $latestPrompt->prompt
                ? $latestPrompt->prompt
                : 'Ты юрист, ответь на вопрос клиента.';

            if (!$chatExists) {
                // Добавляем новый чат с chat_id
                DB::table('avito_chats')->insert([
                    'chat_id' => $chatId,
                    'is_gpt_active' => 1,
                    'gpt_prompt' => $prompt,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // проверяем наличие записи
            $isGptActive = DB::table('avito_chats')
                ->where('chat_id', $chatId)
                ->value('is_gpt_active');


            $isGlobalGptActive = DB::table('gptsettings')
                ->where('id', 1)
                ->value('global_gpt_active');

            // Проверка, что GPT активен
            if ($isGptActive == 1 && $authorId !== '320878714' &&  $isGlobalGptActive) {

                sleep(2);

                $array_conversation = app(AvitoApiService::class)->getMessages($chatId, 320878714);
                // Преобразуем массив в JSON-строку
                $content = json_encode($array_conversation, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                // Записываем в файл (например, storage/app/data.json)
                Storage::put('1.json', $content);

                sleep(3);

                $answer = GptService::Answer($array_conversation, $prompt);
                //$answer = "добрый день";

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

        $promptForm = Prompt::orderBy('id', 'desc')->first();
        $is_gpt_active =  DB::table('gptsettings')
            ->where('id', 1)
            ->get();

        if (is_array($chats) && !empty($chats)) {
            foreach ($chats as &$chat) {
                // Получаем или создаём запись в базе для чата
                $chatModel = AvitoChat::firstOrCreate(
                    ['chat_id' => $chat['id']],
                    [
                        'gpt_prompt' => $promptForm ? $promptForm->prompt : null,
                        'is_gpt_active' => true,
                    ]
                );
                // Добавляем значение в массив чата для Blade
                $chat['is_gpt_active'] = $chatModel->is_gpt_active;
                $chat['gpt_prompt'] = $chatModel->gpt_prompt; // добавляем текст промпта из таблицы avito_chats
            }
            unset($chat);
        } else {
            // Обработка случая, когда $chats пустой или не массив
            $chats = []; // или другая логика обработки ошибки
        } // Разрываем ссылку        

        return view('avito/avito_chats', compact('chats', 'promptForm', 'is_gpt_active'));
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
