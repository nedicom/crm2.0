<?php

namespace App\Models;

use App\Http\Requests\TasksRequest;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $id
 * @property string $client
 * @property string $name
 * @property string $lawyer
 * @property \DateTime $date
 * @property string $status
 * @property double $duration
 * @property string $type_duration
 * @property int $created_at
 * @property int $updated_at
 * @property int $clientid
 * @property string $hrftodcm
 * @property string $tag
 * @property int $soispolintel
 * @property int $postanovshik
 * @property string $description
 * @property \DateTime $donetime
 * @property string $type
 * @property string $calendar_uid
 * @property boolean $new
 * @property int $deal_id
 * @property int $lead_id
 * @property int $service_id
 *
 * @property Services|null $service
 */
class Tasks extends Model
{
    const STATUS_WAITING = 'ожидает';
    const STATUS_OVERDUE = 'просрочена';
    const STATUS_IN_WORK = 'в работе';
    const STATUS_COMPLETE = 'выполнена';

    const STATE_NEW = 1;
    const STATE_OLD = 0;

    const TYPE_DURATION_OLD = 'old';
    const TYPE_DURATION_NEW = 'new';

    use HasFactory;

    protected $guarded = [];

    protected function date(): Attribute
    {
        $weekMap = [1 => 'Понедельник', 2 => 'Вторник', 3 => 'Среда', 4 => 'Четерг', 5 => 'Пятница', 6 => 'Суббота', 7 => 'Воскресенье'];

        return Attribute::make(
            get: fn ($value) => [
                'value' => Carbon::parse($value)->format('Y-m-d H:i'),
                'rawValue' => Carbon::parse($value),
                'day' => $weekMap[Carbon::parse($value)->dayOfWeekIso],
                'month' => Carbon::parse($value)->format('j'),
                'currentMonth' => Carbon::parse($value)->locale('ru_RU')->monthName,
                'currentTime' => Carbon::parse($value)->format('H:i'),
                'currentDay' => Carbon::parse($value)->format('j'),
                'currentHour' => Carbon::parse($value)->format('H'),

            ],
        );
    }

    /**
     * Создание задачи для Клиента
     * @param TasksRequest $request
     * @return Tasks
     */
    public static function new(TasksRequest $request): self
    {
        $task = new self();
        $task->fill($request->except(['nameoftask', 'clientidinput', 'deals', 'payID', 'payClient', '_token']));
        $task->name = $request->nameoftask;
        $task->clientid = $request->clientidinput;
        $task->deal_id = ($request->deals !== null) ? $request->deals : null;
        $task->new = static::STATE_NEW;
        $task->postanovshik = Auth::user()->id;
        $task->status = static::STATUS_WAITING;
        $task->setDuration($request->input('duration'));

        return $task;
    }

    /**
     * Создание задачи для Лида
     * @param TasksRequest $request
     * @return Tasks
     */
    public static function newFromLead(TasksRequest $request): self
    {
        $task = new self();
        $task->fill($request->except(['nameoftask', 'lead_id', '_token']));
        $task->name = $request->nameoftask;
        $task->lead_id = $request->lead_id;
        $task->new = static::STATE_NEW;
        $task->postanovshik = Auth::user()->id;
        $task->status = static::STATUS_WAITING;
        $task->setDuration($request->input('duration'));

        return $task;
    }

    /**
     * @param TasksRequest $request
     * @return void
     */
    public function edit(TasksRequest $request): void
    {
        $this->fill($request->except(['nameoftask', 'clientidinput', 'deals', 'payID', 'payClient', '_token']));
        $this->name = $request->nameoftask;
        $this->clientid = $request->clientidinput;
        $this->deal_id = ($request->deals !== null) ? $request->deals : null;
        $this->setDuration($request->input('duration'));
    }

    /** Устанавливаем значение продоолжительности
     * @param array $duration
     * @return void
     */
    public function setDuration(array $duration): void
    {
        $hours = (!empty($duration['hours'])) ? $duration['hours'] : 0;
        $minutes = (!empty($duration['minutes'])) ? $duration['minutes'] : 0;
        $this->duration = ($hours * 60) + $minutes;
        $this->type_duration = static::TYPE_DURATION_NEW;
    }

    /**
     * Inversion relation Deal
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deal()
    {
        return $this->belongsTo(Deal::class, 'deal_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Services::class, 'service_id');
    }

    /**
     * Inversion relation Client
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function clientsModel()
    {
        return $this->belongsTo(ClientsModel::class, 'clientid', 'id');
    }

    public function payments()
    {
        return $this->belongsToMany(Payments::class, 'task_payment_assigns', 'task_id', 'payment_id');
    }
}
