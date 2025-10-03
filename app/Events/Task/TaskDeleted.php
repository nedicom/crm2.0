<?php

namespace App\Events\Task;

use App\Models\Tasks;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Tasks $task;

    public function __construct(Tasks $task)
    {        
        $this->task = $task;
    }
}
