<?php

namespace App\Services\Gpt;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GptService
{
    public static function Answer($array_conversation)
    {
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
        curl_close($ch);

        if ($result === false) {
            $error = curl_error($ch);
            curl_close($ch);
            Log::error("Yandex IAM token request failed: $error");
            return 'Извините, я сейчас немного занят, но позже напишу Вам обязательно.';
        }


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
                'maxTokens' => 600,
                'reasoningOptions' => [
                    'mode' => 'DISABLED'
                ]
            ],
            'messages' => [
                [
                    'role' => 'system',
                    'text' => 'Ты — мужчина специалист (юрист), который продает клиенту юридические услуги на Авито в чате. 
                    Ответь коротко на вопрос пользователя.'
                ]
            ]
        ];

        $messagesss = json_encode($array_conversation['messages'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        Storage::put('2.json', $messagesss);

        // Добавляем сообщения из истории, если есть
        if (count($array_conversation['messages']) > 0) {
            $result = [];
            foreach (array_reverse($array_conversation['messages']) as $msg) {
                $role = ((string)$msg['author_id'] === '320878714') ? 'assistant' : 'user';
                $result[] = [
                    'role' => $role,
                    'text' => $msg['content']['text'] ?? ''
                ];
            }
            Storage::put('5.json', json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $data['messages'] = array_merge(
                $data['messages'],
                $result
            );
            Storage::put('6.json', json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        Storage::put('3.json', $content);

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


        curl_close($ch);
        if ($result === false) {
            $error = curl_error($ch);
            curl_close($ch);
            Log::error("Yandex GPT request failed: $error");
            return 'Извините, ошибка взаимодействия с сервером. Мы скоро ее починим. Возможно завтра.';
        }


        $response_data = json_decode($result, true);

        if (
            isset($response_data['result']['alternatives'][0]['message']['text']) &&
            !empty($response_data['result']['alternatives'][0]['message']['text'])
        ) {
            Storage::put('7.json', json_encode($response_data['result'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $generated_text = $response_data['result']['alternatives'][0]['message']['text'];
            return $generated_text;
        }
        return 'Простите, я сейчас немного занят';
    }
}
