<?php

namespace App\Listeners\Task\TaskCompleted;

use App\Events\Task\TaskCompleted;
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

    public function handle(TaskCompleted $event): void
    {
        $task = $event->task;

        $contractor = User::find($task->lawyer);
        $owner = User::find($task->postanovshik);

        if ($owner?->tg_id) {
            $message = view('telegram.task.completed', [
                'task' => $task,
                'user' => $contractor,
            ])->render();

            try {
                $this->bot->sendMessage($message, [
                    'chat_id' => $owner->tg_id,
                    'parse_mode' => ParseMode::HTML,
                ]);
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }
}
