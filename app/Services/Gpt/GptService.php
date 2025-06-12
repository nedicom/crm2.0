<?php

namespace App\Services\Gpt;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GptService
{
    public static function Answer($array_conversation)
    {
        // Получаем IAM токен по OAuth токену из .env
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://iam.api.cloud.yandex.net/iam/v1/tokens');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"yandexPassportOauthToken": "' . env('YANDEX_GPT_API_KEY') . '"}');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $result = curl_exec($ch);
        if ($result === false) {
            $error = curl_error($ch);
            curl_close($ch);
            Log::error("Yandex IAM token request failed: $error");
            return 'Извините, я сейчас немного занят, но позже напишу Вам обязательно.';
        }
        curl_close($ch);

        $response_data = json_decode($result, true);
        if (empty($response_data['iamToken'])) {
            Log::error('Yandex IAM token not received');
            return 'Извините, юрист временно недоступен.';
        }

        // Формируем базовый массив запроса
        $data = [
            'modelUri' => 'gpt://' . env('YANDEX_CT_ID') . '/yandexgpt',
            'completionOptions' => [
                'stream' => false,
                'temperature' => 0.6,
                'maxTokens' => 2000,
                'reasoningOptions' => [
                    'mode' => 'DISABLED'
                ]
            ],
            'messages' => [
                [
                    'role' => 'system',
                    'text' => 'Ты — специалист, который продает клиенту юридические услуги на Авито в чат боте. Не говори об этом собеседнику. Твоя задача предложить вовлечение пользователя в беседу, чтобы потом попросить его номер телефона.'
                ]
            ]
        ];

        // Добавляем сообщения из истории, если есть
        if (count($array_conversation['messages']) > 0) {
            $data['messages'] = array_merge(
                $data['messages'],
                self::convertMessagesForYandexGpt(array_reverse($array_conversation['messages']))
            );
        }

        $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        Storage::put('data_messages.json', $content);

        $json_data = json_encode($data);

        // Запрос к Yandex GPT API
        $url = 'https://llm.api.cloud.yandex.net/foundationModels/v1/completion';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $response_data['iamToken']
        ]);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $result = curl_exec($ch);
        if ($result === false) {
            $error = curl_error($ch);
            curl_close($ch);
            Log::error("Yandex GPT request failed: $error");
            return 'Извините, ошибка взаимодействия с сервером. Мы скоро ее починим. Возможно завтра.';
        }
        curl_close($ch);

        $response_data = json_decode($result, true);

        if (
            isset($response_data['result']['alternatives'][0]['message']['text']) &&
            !empty($response_data['result']['alternatives'][0]['message']['text'])
        ) {
            $generated_text = $response_data['result']['alternatives'][0]['message']['text'];

            if (mb_stripos($generated_text, 'интеллект') !== false) {
                return 'Простите, но Ваш вопрос представляет сложность. Нужно немного больше времени.';
            }
            return $generated_text;
        }

        return 'Простите, я сейчас немного занят';
    }

    private static function convertMessagesForYandexGpt($messages): array
    {
        $result = [];
        foreach ($messages as $msg) {
            $role = ((string)$msg['author_id'] === '320878714') ? 'assistant' : 'user';
            $result[] = [
                'role' => $role,
                'text' => $msg['content']['text'] ?? ''
            ];
        }
        return $result;
    }
}
