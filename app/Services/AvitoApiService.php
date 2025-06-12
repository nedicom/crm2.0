<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\Log;

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

        $payload = [
            'message' => [
                'text' => $message,
            ],
            'type' => 'text',
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        if ($result === false) {
            return 'error';
        }
        return 'sended';
    }

    // Получить сообщения по id чата
    public function getMessages($chatId, $userId)
    {
        // Пример отправки сообщения через cURL
        $token = $this->getToken();

        $url = "https://api.avito.ru/messenger/v3/accounts/{$userId}/chats/{$chatId}/messages";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        if ($result === false) {
            curl_close($ch);
            return []; // Возвращаем пустой массив при ошибке
        }
        curl_close($ch);

        $data = json_decode($result, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return []; // Ошибка парсинга JSON
        }

        // Предположим, что сообщения лежат в $data['messages']
        return $data ?? [];
    }

    /**
     * Получить список чатов (бесед) из мессенджера Авито
     */
    public function getChats()
    {
        $token = $this->getToken();

        if (!$token) {            
            return null;
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ])
        ->withoutVerifying() // Отключаем проверку SSL
        ->get('https://api.avito.ru/messenger/v2/accounts/320878714/chats');

        if (!$response->successful()) {
            throw new Exception('Ошибка при получении списка чатов: ' . $response->body());
        }

        $data = json_decode($response, true);

        if (!empty($data['chats'])) {
            return $data['chats'];
        }
        return [];
    }

    public function registerWebhook(string $webhookUrl): array
    {
        $token = $this->getToken();

        if (!$token) {
            return [];
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
        try {
            $response = Http::asForm()
            ->withoutVerifying() // Отключаем проверку SSL
            ->post('https://api.avito.ru/token/', [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.avito.client_id'),
                'client_secret' => config('services.avito.client_secret'),
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }
            Log::error('Ошибка при получении токена Avito: ' . $response->body());

            return null;
        } catch (\Exception $e) {
            // Можно проверить на cURL error 60, если нужно
            if (strpos($e->getMessage(), 'cURL error 60') !== false) {
                // Можно логировать или обработать отдельно
                return null;
            }

            // Для других ошибок пробросить или обработать
            throw $e;
        }
    }
}
