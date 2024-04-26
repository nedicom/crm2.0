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

    protected $fillable = [
        'lead_id', 'name', 'phone', 'description', 'address', 'email', 'source', 'rating', 'lawyer', 'status'
    ];

    /**
     * @param ClientsRequest $request
     * @return static
     */
    public static function new(ClientsRequest $request): self
    {
        $client = new self();
        //dd($request->input('status'));
        $client->fill($request->except(['_token']));
            if (is_null($request->input('email'))) { $client->email = 'empty@empty.ru'; }
            if (is_null($request->input('address'))) { $client->address = 'адрес не указан'; }            
        $client->tgid = rand(0, 1000000);
            $replacePhone = ['+7', ' ', '(', ')' , '-'];
            $client->phone = str_replace($replacePhone, '', $request->input('phone'));
        
        return $client;
    }

    /**
     * @param ClientsRequest $request
     * @return void
     */
    public function edit(ClientsRequest $request): void
    {
        $this->fill($request->except('_token', 'status'));        
        //if (!is_null($request->input('email'))) { $this->email = $request->input('email'); }
        //if (!is_null($request->input('address'))) { $this->address = $request->input('address'); }
        if (!$request->input('status')) {
            $this->status = static::STATUS_INACTIVE;
        }
        else{
            $this->status = static::STATUS_ACTIVE;
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

    public function paymsThroughTask()
    {
        return $this->hasManyThrough(TaskPaymentAssign::class, Tasks::class,  
            'clientid', // Foreign key on the environments table...
            'task_id', // Foreign key on the deployments table...
            'id', // Local key on the projects table...
            'id' // Local key on the environments table...
        );
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
