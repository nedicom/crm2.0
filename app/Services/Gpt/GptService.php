<?php

namespace App\Services\Gpt;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class GptService
{
    public static function Answer(array $array_conversation): string
    {
        try {
            // 1. Получаем IAM-токен
            $iamToken = self::getIamToken();
            if (empty($iamToken)) {
                throw new Exception('Failed to get IAM token');
            }

            // 2. Формируем запрос к Yandex GPT
            $requestData = self::prepareGptRequest($array_conversation);
            
            // 3. Отправляем запрос к Yandex GPT
            $gptResponse = self::callYandexGpt($iamToken, $requestData);
            
            // 4. Обрабатываем ответ
            return self::processGptResponse($gptResponse);
            
        } catch (Exception $e) {
            Log::error('GPT Service Error: ' . $e->getMessage());
            return 'Извините, в данный момент сервис недоступен. Попробуйте позже.';
        }
    }

    private static function getIamToken(): ?string
    {
        $ch = curl_init();
        try {
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://iam.api.cloud.yandex.net/iam/v1/tokens',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode([
                    'yandexPassportOauthToken' => env('YANDEX_GPT_API_KEY')
                ]),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            ]);

            $response = curl_exec($ch);
            if ($response === false) {
                throw new Exception('IAM request failed: ' . curl_error($ch));
            }

            $data = json_decode($response, true);
            return $data['iamToken'] ?? null;

        } finally {
            curl_close($ch);
        }
    }

    private static function prepareGptRequest(array $conversation): array
    {
        $data = [
            'modelUri' => 'gpt://' . env('YANDEX_CT_ID') . '/yandexgpt',
            'completionOptions' => [
                'stream' => false,
                'temperature' => 0.6,
                'maxTokens' => 2000,
            ],
            'messages' => [
                [
                    'role' => 'system',
                    'text' => 'Ты — специалист, который продает клиенту юридические услуги...'
                ]
            ]
        ];

        // Добавляем историю сообщений
        if (!empty($conversation['messages'])) {
            $data['messages'] = array_merge(
                $data['messages'],
                self::convertMessagesForYandexGpt(
                    array_reverse($conversation['messages'])
                )
            );
        }

        // Логирование для отладки
        Storage::put('gpt_request.json', json_encode($data, JSON_PRETTY_PRINT));

        return $data;
    }

    private static function callYandexGpt(string $iamToken, array $requestData): array
    {
        $ch = curl_init();
        try {
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://llm.api.cloud.yandex.net/foundationModels/v1/completion',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($requestData),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $iamToken
                ],
            ]);

            $response = curl_exec($ch);
            if ($response === false) {
                throw new Exception('GPT request failed: ' . curl_error($ch));
            }

            return json_decode($response, true) ?? [];

        } finally {
            curl_close($ch);
        }
    }

    private static function processGptResponse(array $response): string
    {
        if (empty($response['result']['alternatives'][0]['message']['text'])) {
            throw new Exception('Empty GPT response');
        }

        $text = $response['result']['alternatives'][0]['message']['text'];

        // Фильтр нежелательных ответов
        if (mb_stripos($text, 'интеллект') !== false) {
            return 'Простите, но Ваш вопрос требует дополнительной проверки.';
        }

        return $text;
    }

    private static function convertMessagesForYandexGpt(array $messages): array
    {
        return array_map(function ($msg) {
            return [
                'role' => ((string)$msg['author_id'] === '320878714') ? 'assistant' : 'user',
                'text' => $msg['content']['text'] ?? ''
            ];
        }, $messages);
    }
}