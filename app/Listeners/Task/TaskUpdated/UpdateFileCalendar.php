<?php

namespace App\Listeners\Task\TaskUpdated;

use App\Services\Calendar\GenerateCalendar as GenerateCalendarService;
use App\Events\Task\TaskUpdated;

class UpdateFileCalendar
{
    private GenerateCalendarService $service;

    public function __construct(GenerateCalendarService $service)
    {
        $this->service = $service;
    }

    public function handle(TaskUpdated $event): void
    {
        $this->service->createTaskCalendar($event->task);
    }
}
