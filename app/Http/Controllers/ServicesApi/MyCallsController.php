<?php

namespace App\Http\Controllers\ServicesApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Leads;
use App\Models\Enums\Leads\Status;
use App\Services\MyCalls\ValueObject\RingDTO;
use App\Services\MyCalls\MyCallsService;
use App\Services\LeadService;

/**
 * Интеграция сервиса Мои звонки
 * @see https://www.moizvonki.ru/guide/api
 */
class MyCallsController extends Controller
{
    public function __construct(
        private MyCallsService $externalService,
        private LeadService $service
    ) {}

    /**
     * Подписка на события слежения звонков через Webhook
     * @return RedirectResponse|null
     */
    public function subscribeTrackingCalls(): ?RedirectResponse
    {
        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = $this->externalService->subscribe();
            if ($response->ok()) {
                return back()->with('success', 'Выполнена подписка на события сервиса Мои звонки.');
            }
        } catch (\Throwable $e) {
            Log::error('Не удалось подписаться на события сервиса Мои звонки. Сообщение ошибки: ' . $e->getMessage());
            return back()->with('error', 'Не удалось подписаться на события сервиса Мои звонки.');
        }

        return null;
    }

    /**
     * Отписка от событий слежения звонков через Webhook
     * @return RedirectResponse|null
     */
    public function unsubscribeTrackingCalls(): ?RedirectResponse
    {
        try {
            $response = $this->externalService->unsubscribe();
            if ($response->ok()) {
                return back()->with('success', 'Выполнена подписка на события сервиса Мои звонки.');
            }
        } catch (\Throwable $e) {
            Log::error('Не удалось подписаться на события сервиса Мои звонки. Сообщение ошибки: ' . $e->getMessage());
            return back()->with('error', 'Не удалось подписаться на события сервиса Мои звонки.');
        }

        return null;
    }

    /**
     * Хук события начала звонка
     */
    public function actionCallStart(Request $request)
    {
        Log::notice(json_encode($request->all(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Хук события окончания звонка
     */
    public function actionCallFinished(Request $request)
    {
        try {
            $event = $request->input('event');
            /** @var Leads $lead */
            $lead = Leads::where('phone', '=', $event['client_number'])->first();

            if ($event['direction'] === 0)  {
                if($lead === null || $lead->status !== Status::Generated->value) {
                    // Запись в логи
                    Log::build([
                        'driver' => 'single',
                        'path' => storage_path('logs/service_mycalls.log'),
                        'days' => 7,
                    ])->notice(json_encode($request->all(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

                    $simpleLead = new RingDTO(
                        $event['client_name'],
                        $event['client_number'],
                        $event['src_number'],
                        $event['direction'],
                        $event['answered'],
                        $event['recording']
                    );
                    // Создание Лида
                    $this->service->handleWebhookFinished($simpleLead);
                }
            }
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }
    }

    /** Загрузка файла с логами */
    public function downloadLogFile()
    {
        $file = storage_path('logs/service_mycalls.log');
        $headers = [
            'Content-Type: text/html; charset=utf-8',
        ];

        return Response::download($file,'logs_my_calls.txt', $headers);
    }
}
