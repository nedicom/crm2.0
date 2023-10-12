<?php

namespace App\Listeners\Task\TaskDeleted;

use App\Services\Calendar\GenerateCalendar as GenerateCalendarService;
use App\Events\Task\TaskDeleted;

class UpdateFileCalendar
{
    private GenerateCalendarService $service;

    public function __construct(GenerateCalendarService $service)
    {
        $this->service = $service;
    }

    public function handle(TaskDeleted $event): void
    {
        $this->service->createTaskCalendar($event->task, true);
    }
}
