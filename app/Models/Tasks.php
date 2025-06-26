<?php

namespace App\Models;

use App\Models\Leads;
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
 * @property boolean $lawyer_agree Согласовано нач-ом юр. отдела
 * @property boolean $sales_agree Согласовано нач-ом отдела продаж
 * @property string $calendar_uid
 * @property boolean $new
 * @property int $deal_id
 * @property int $lead_id
 * @property int $service_id
 *
 * @property Services|null $service
 * @property User $performer Исполнитель задачи
 */
class Tasks extends Model
{
    const STATUS_WAITING = 'ожидает';
    const STATUS_OVERDUE = 'просрочена';
    const STATUS_IN_WORK = 'в работе';
    const STATUS_COMPLETE = 'выполнена';
    const STATUS_FAIL = 'провалена';

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
            get: fn($value) => [
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
        $task->new = (Auth::user()->id == $request->lawyer) ? static::STATE_OLD : static::STATE_NEW;
        //$task->new = static::STATE_NEW;
        $task->postanovshik = Auth::user()->id;
        $task->status = static::STATUS_WAITING;
        $task->setDuration($request->input('duration'));
        $task->setAgreed($request);

        return $task;
    }

    /**
     * Создание задачи для Лида
     * @param TasksRequest $request
     * @return Tasks
     */
    public static function newFromLead(TasksRequest $request): self
    {
        //dd($request);
        $task = new self();
        $task->fill($request->except(['nameoftask', 'lead_id', 'lead_phone', '_token', 'casettype', 'leadname', 'client']));
        $task->description = $request->casettype . ': ' . $request->description;
        $task->name = $request->leadname;
        $task->client = $request->lead_phone;
        $task->soispolintel = 41;
        $task->lead_id = $request->lead_id;
        $task->type = $request->type;
        $task->new = static::STATE_NEW;
        $task->postanovshik = Auth::user()->id;
        $task->status = static::STATUS_WAITING;
        $task->duration = 30;
        $task->type_duration = 'new';
        $task->setAgreed($request);

        $lead = Leads::find($request->lead_id);
        if ($request->type == \App\Models\Enums\Tasks\Type::Consultation->value) {
            $lead->status = \App\Models\Enums\Tasks\Type::Consultation->value;
        }
        if ($request->type == \App\Models\Enums\Tasks\Type::Ring->value) {
            $lead->status = \App\Models\Enums\Tasks\Type::Ring->value;
        }
        $lead->saveOrFail();


        return $task;
    }

    /**
     * @param TasksRequest $request
     * @return void
     */
    public function edit(TasksRequest $request): void
    {
        $this->fill($request->except(['nameoftask', 'clientidinput', 'deals', 'payID', 'payClient', 'lawyer_agree', 'sales_agree', '_token', 'status']));
        $this->name = $request->nameoftask;
        $this->clientid = $request->clientidinput;
        $this->deal_id = ($request->deals !== null) ? $request->deals : null;
        $this->setDuration($request->input('duration'));
        $this->setAgreed($request);
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
     * Согласование начальников отделов
     * @param TasksRequest $request
     * @return void
     */
    public function setAgreed(TasksRequest $request): void
    {
        if ($request->lawyer_agree !== null) $this->lawyer_agree = ($request->lawyer_agree) ? 1 : 0;
        if ($request->sales_agree !== null) $this->sales_agree = ($request->sales_agree) ? 1 : 0;
    }

    /** Проверка просрочена ли задача при включенной согласованности начальником юр. отдела
     * @return bool
     */
    public function isOverdueAtDepartment(): bool
    {
        return $this->lawyer_agree == 1
            && Carbon::parse($this->date['value'])->addMinutes($this->duration) <= Carbon::parse()->toDateTimeString()
            && Auth::user()->role == User::ROLE_USER;
    }

    /** Проверка при включенной согласованности начальником юр. отдела
     * @return bool
     */
    public function isAtDepartment(): bool
    {
        return $this->lawyer_agree == 1 && Auth::user()->role == User::ROLE_USER;
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

    public function performer()
    {
        return $this->belongsTo(User::class, 'lawyer', 'id')->withDefault([
            'name' => 'Пользователь удален',
            'avatar' => 'https://crm.nedicom.ru/avatars/ahCEbgke0YxG2JYZYGb6usGJrbID6lMmIegaKZbq.jpg'
        ]);
    }
}
