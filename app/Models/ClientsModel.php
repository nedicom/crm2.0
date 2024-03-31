<?php

namespace App\Models;

use App\Http\Requests\ClientsRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string $source
 * @property string $description
 * @property int $created_at
 * @property int $updated_at
 * @property boolean $status
 * @property int $lawyer
 * @property string $email
 * @property string $address
 * @property string $rating
 * @property string $change_status_at Дата переключения статуса в неактивное зн-ие
 * @property int $tgid
 *
 * @property User $userFunc
 */
class ClientsModel extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    use HasFactory;

    protected $guarded = [];

    /**
     * @param ClientsRequest $request
     * @return static
     */
    public static function new(ClientsRequest $request): self
    {
        $client = new self();
        $client->fill($request->except(['_token', 'email', 'address']));
        if (!is_null($request->input('email'))) { $client->email = $request->input('email'); }
        if (!is_null($request->input('address'))) { $client->address = $request->input('address'); }
        $client->tgid = rand(0, 1000000);
        if (!$request->input('status')) $client->change_status_at = Carbon::now()->toDateTimeString();

        return $client;
    }

    /**
     * @param ClientsRequest $request
     * @return void
     */
    public function edit(ClientsRequest $request): void
    {
        $this->fill($request->except('_token', 'email', 'address'));
        if (!is_null($request->input('email'))) { $this->email = $request->input('email'); }
        if (!is_null($request->input('address'))) { $this->address = $request->input('address'); }
        if ($request->input('status') == 'выполнена') {
            $this->status = static::STATUS_INACTIVE;
        }
        if (!$request->input('status')) $this->change_status_at = Carbon::now()->toDateTimeString();
    }

    /**
     * Обновление клиента из формы создания Договора
     * @param Request $request
     * @return void
     */
    public function editFromClient(Request $request)
    {
        if (!is_null($request->input('adress'))) { $this->address = $request->input('adress'); }
        if (!is_null($request->input('client'))) { $this->name = $request->input('client'); }
        if (!is_null($request->input('phone'))) { $this->phone = $request->input('phone'); }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function serviceFunc()
    {
        return $this->hasManyThrough(
            Services::class,
            Payments::class,
            'clientid',
            'id',
            'id',
            'service'
        );
    }

    /**
     * Relation Payments
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paymentsFunc()
    {
        return $this->hasMany(Payments::class, 'clientid' , 'id');
    }

    /**
     * Relation User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userFunc()
    {
        return $this->belongsTo(User::class, 'lawyer');
    }

    /**
     * Relations Tasks[]
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasksFunc()
    {
        return $this->hasMany(Tasks::class, 'clientid' , 'id');
    }

    /**
     * Relations Tasks
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function task()
    {
        return $this->hasOne(Tasks::class, 'clientid', 'id');
    }

    /**
     * Relation Deals[]
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deals()
    {
        return $this->hasMany(Deal::class, 'client_id', 'id');
    }

    /**
     * Relation Dogovor[]
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contracts()
    {
        return $this->hasMany(Dogovor::class, 'client_id', 'id');
    }
}
