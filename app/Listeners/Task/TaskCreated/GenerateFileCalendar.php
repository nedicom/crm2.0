<?php

namespace App\Listeners\Task\TaskCreated;

use App\Services\Calendar\GenerateCalendar as GenerateCalendarService;
use App\Events\Task\TaskCreated;

class GenerateFileCalendar
{
    private GenerateCalendarService $service;

    public function __construct(GenerateCalendarService $service)
    {
        $this->service = $service;
    }

    public function handle(TaskCreated $event): void
    {
        $this->service->createTaskCalendar($event->task);
    }
}
