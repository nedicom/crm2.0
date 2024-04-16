<?php

namespace App\Services;

use App\Models\Payments;
use App\Models\TaskPaymentAssign;

class PaymentService
{
    /**
     * Присваевываем добавленные задачи
     * @param Payments $payment
     * @param array $input
     * @return void
     */

    public function assignTasks(Payments $payment, array $input): void
    {
        foreach ($input as $key => $value) {
            if (!empty($value)) {
                TaskPaymentAssign::create(['payment_id' => $payment->id, 'task_id' => $value]);
            }
        }
    }

    /**
     * Удаляем присвоенные задачи
     * @param Payments $payment
     * @return void
     */
    public function clearAssignTasks(Payments $payment): void
    {
        $tasks = $payment->tasks();
        if ($tasks->count() > 0) {
            foreach ($tasks->get() as $task) {
                $task->pivot->delete();
            }
        }
    }
}
