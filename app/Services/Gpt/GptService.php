<?php


namespace App\Services\Gpt;

class GptService
{
    public static function Answer($ask, $array_conversation)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://iam.api.cloud.yandex.net/iam/v1/tokens');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"yandexPassportOauthToken": "' . env('YANDEX_GPT_API_KEY') . '"}');

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            return 'Извините, я сейчас немного занят, но позже напишу Вам обязательно.';
        }
        $response_data = json_decode($result, true);
        curl_close($ch);
        // testing for fetch - return $result;

        $data = [
            'modelUri' => 'gpt://' . env('YANDEX_CT_ID') . '/yandexgpt',
            'completionOptions' =>
            [
                "stream" => false,
                "temperature" => 0.6,
                "maxTokens" => "2000",
                "reasoningOptions" => [
                    "mode" => "DISABLED"
                ]
            ],
            'messages' => [
                [
                    'role' => 'system',
                    'text' => 'Ты — специалист, который продает клиенту юриидические услуги на Авито в чат боте.
                    Не говори об этом собеседнику. Твоя задача предложить вовлечение пользователя в беседу, чтобы потом 
                    попросить его номер телефона.'
                ],
            ]
        ];

        $mesquantity = count($array_conversation);

        if ($mesquantity > 1) {
            foreach ($array_conversation as $val) {
                if (array_key_first($val) == 'user_message') {
                    $data['messages'][] =
                        [
                            'role' => 'user',
                            'text' => $val['user_message']
                        ];
                }
                if (array_key_first($val) == 'ai_message') {
                    $data['messages'][] =
                        [
                            'role' => 'assistant',
                            'text' => $val['ai_message']
                        ];
                }
            }
        }

        $data['messages'][] =
            [
                'role' => 'user',
                'text' => $ask,
            ];



        $json_data = json_encode($data);

        $url = 'https://llm.api.cloud.yandex.net/foundationModels/v1/completion';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer ' . $response_data['iamToken'];
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            return 'Извините, ошибка взаимодействия с сервером. Мы скоро ее починим. Возможно завтра.';
        }
        $response_data = json_decode($result, true);

        $generated_text = $response_data['result']['alternatives'][0]['message']['text'];
        curl_close($ch);

        if ($generated_text) {
            if (str_contains($generated_text, 'интеллект')) {
                return 'Простите, но Ваш вопрос представляет сложность. Нужно немного больше времени.';
            }
            return $generated_text;
        } else {
            return 'Простите, я сейчас немного занят';
        }
    }
}
