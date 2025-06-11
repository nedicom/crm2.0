<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class AvitoApiService
{
    protected $client_id;
    protected $client_secret;

    public function __construct()
    {

        $this->client_id = config('services.avito.client_id');
        $this->client_secret = config('services.avito.client_secret');
    }

    public function sendMessage($userId, $chatId, $message)
    {
        // Пример отправки сообщения через cURL
        $token = $this->getToken();

        $url = "https://api.avito.ru/messenger/v1/accounts/{$userId}/chats/{$chatId}/messages";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'text' => $message,
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * Получить список чатов (бесед) из мессенджера Авито
     */
    public function getChats(): array
    {
        $token = $this->getToken();

        if (!$token) {
            throw new Exception('Не удалось получить access token от Авито');
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ])->get('https://api.avito.ru/messenger/v2/accounts/320878714/chats');

        if (!$response->successful()) {
            throw new Exception('Ошибка при получении списка чатов: ' . $response->body());
        }

        // В ответе обычно есть поле с массивом чатов, например 'conversations' или 'data'
       // return $response->json();


        $data = json_decode($response, true);

        if (!empty($data['chats'])) {
            foreach ($data['chats'] as $chat) {
                echo "ID чата: " . $chat['id'] . PHP_EOL;

                // Контекст объявления
                if (!empty($chat['context']['value'])) {
                    $item = $chat['context']['value'];
                    echo "Объявление: " . $item['title'] . " (ID: " . $item['id'] . ")" . PHP_EOL;
                    echo "Город: " . $item['location']['title'] . PHP_EOL;
                    echo "Цена: " . $item['price_string'] . PHP_EOL;
                    echo "Ссылка: " . $item['url'] . PHP_EOL;
                }

                // Участники чата
                if (!empty($chat['users'])) {
                    echo "Участники:" . PHP_EOL;
                    foreach ($chat['users'] as $user) {
                        echo " - " . $user['name'] . " (user_id: " . $user['id'] . ")" . PHP_EOL;
                    }
                }

                // Последнее сообщение
                if (!empty($chat['last_message'])) {
                    $msg = $chat['last_message'];
                    echo "Последнее сообщение: " . ($msg['content']['text'] ?? '[нет текста]') . PHP_EOL;
                    echo "Автор: user_id " . $msg['author_id'] . PHP_EOL;
                    echo "Время: " . date('Y-m-d H:i:s', $msg['created']) . PHP_EOL;
                }

                echo str_repeat('-', 40) . PHP_EOL;
            }
        } else {
            echo "Чаты не найдены";
        }
    }

    public function registerWebhook(string $webhookUrl): array
    {
        $token = $this->getToken();

        if (!$token) {
            throw new Exception('Не удалось получить access token от Авито');
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Content-Type' => 'application/json',
        ])->post('https://api.avito.ru/messenger/v3/webhook', [
            'url' => $webhookUrl,
        ]);

        if (!$response->successful()) {
            throw new Exception('Ошибка регистрации webhook: ' . $response->body());
        }

        return $response->json();
    }

    public function getToken(): ?string
    {
        $response = Http::asForm()->post('https://api.avito.ru/token/', [
            'grant_type' => 'client_credentials',
            'client_id' => config('services.avito.client_id'),
            'client_secret' => config('services.avito.client_secret'),
        ]);

        if ($response->successful()) {
            return $response->json('access_token');
        }

        return null;
    }
}
