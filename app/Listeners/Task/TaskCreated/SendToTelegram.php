<?php

namespace App\Listeners\Task\TaskCreated;

use App\Events\Task\TaskCreated;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;

class SendToTelegram
{
    public Nutgram $bot;

    /**
     * @param Nutgram $bot
     */
    public function __construct(Nutgram $bot)
    {
        $this->bot = $bot;
    }

    public function handle(TaskCreated $event): void
    {
        $task = $event->task;

        $owner = User::find($task->postanovshik);
        $contractor = User::find($task->lawyer);
        $contractor2 = User::find($task->soispolintel);

        $message = view('telegram.task.created', [
            'task' => $task,
            'user' => $owner,
        ])->render();

        if ($contractor?->tg_id) {
            try {
                $this->bot->sendMessage($message, [
                    'chat_id' => $contractor->tg_id,
                    'parse_mode' => ParseMode::HTML,
                    'disable_web_page_preview' => true,
                ]);
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }

        if ($contractor2?->tg_id) {
            try {
                $this->bot->sendMessage($message, [
                    'chat_id' => $contractor2->tg_id,
                    'parse_mode' => ParseMode::HTML,
                    'disable_web_page_preview' => true,
                ]);
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }
}
