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

    public function sendMessage($chatId, $message)
    {
        // Пример отправки сообщения через cURL
        $token = $this->getToken();
        $ch = curl_init('https://api.avito.ru/messenger/v1/accounts/me/messages');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'chat_id' => $chatId,
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
        ])->get('https://api.avito.ru/messenger/v2/accounts/'.config('services.avito.client_id').'/chats');

        if (!$response->successful()) {
            throw new Exception('Ошибка при получении списка чатов: ' . $response->body());
        }

        // В ответе обычно есть поле с массивом чатов, например 'conversations' или 'data'
        return $response->json();
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
