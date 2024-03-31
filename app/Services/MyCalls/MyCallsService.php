<?php

namespace App\Services\MyCalls;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/** Класс работы с API сервиса Мои звонки */
class MyCallsService
{
    /**
     * Подписка на события через Webhook
     * @return Response
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function subscribe(): Response
    {
        $jsonReqBody = json_encode([
            'user_name' => config('services.my_calls.username'),
            'api_key' => config('services.my_calls.api_key'),
            'action' => 'webhook.subscribe',
            'hooks' => [
                //'call.start' => 'https://example.com/mycalls/action/call-start',
                'call.finish' => 'https://crm.nedicom.ru/mycalls/action/call-finished',
            ],
        ]);
        /** @var Response $response */
        $response = Http::myCalls()->asForm()->post('', [
            'request_data' => $jsonReqBody,
        ]);

        return $response->throw();
    }

    /**
     * Отписка от событий через Webhook
     * @return Response
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function unsubscribe(): Response
    {
        $jsonReqBody = json_encode([
            'user_name' => config('services.my_calls.username'),
            'api_key' => config('services.my_calls.api_key'),
            'action' => 'webhook.unsubscribe',
            'hooks' => [
                //'call.start',
                'call.finish',
            ],
        ]);
        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::myCalls()->asForm()->post('', [
            'request_data' => $jsonReqBody,
        ]);

        return $response->throw();
    }
}
