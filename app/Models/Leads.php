<?php

namespace App\Models;

use App\Services\MyCalls\ValueObject\RingDTO;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tasks;
use Illuminate\Http\Client\Request;
use App\Models\ValueObject\SimpleLeadDTO;
use App\Models\Enums\Leads\Status;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\hasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property string $source
 * @property string $description
 * @property string $phone
 * @property int $lawyer
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property int $responsible
 * @property string $comment
 * @property int $service
 * @property string $status
 * @property string $action
 * @property string $failurereason
 * @property string $successreason
 * @property string $ring_recording_url Ссылка на запись разговора
 */
class Leads extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'description',
        'source',
        'service',
        'lawyer',
        'responsible',
        'status',
        'client_id'
    ];

    /**
     * Создание лида по API сервиса Мои звонки
     * @param RingDTO $valueObject
     * @param string $clientName
     * @param string $source
     * @return self
     */
    public static function newFromServiceMyCalls(RingDTO $valueObject, string $clientName, string $source): self
    {
        $lead = new self();
        $lead->name = $clientName;
        $lead->phone = $valueObject->getClientPhone();
        $lead->description = ($valueObject->getAnswered() === 0) ? 'Звонок не отвечен' : '';
        $lead->source = $source;
        $lead->service = 2; // ID услуги
        $lead->lawyer = 41; // ID юриста
        $lead->responsible = 41;
        $lead->status = Status::Generated->value;
        $lead->ring_recording_url = $valueObject->getRecordingUrl();

        return $lead;
    }

    public function userFunc()
    {
        return $this->belongsTo(User::class, 'lawyer')->select(['id', 'name', 'avatar']);
    }

    public function responsibleFunc()
    {
        return $this->belongsTo(User::class, 'responsible')->select(['id', 'name', 'avatar']);
    }

    public function servicesFunc()
    {
        return $this->belongsTo(Services::class, 'service');
    }

    public function tasks(): hasMany
    {
        return $this->hasMany(Tasks::class, 'lead_id', 'id');
    }


    public function lazytasks(): hasMany
    {
        return $this->hasMany(Tasks::class, 'lead_id', 'id')
        ->where(
            function ($query) {
                return $query
                    ->where('status', Tasks::STATUS_WAITING)
                    ->orWhere('status', Tasks::STATUS_OVERDUE)
                    ->orWhere('status', Tasks::STATUS_IN_WORK);
            }
        )
        ->select(['id', 'status', 'lead_id'])
        ;
    }

    public function lazyphone(): hasMany
    {
        return $this->hasMany(Tasks::class, 'lead_id', 'id')
            ->where('type', \App\Models\Enums\Tasks\Type::Ring->value)
            ->where(
                function ($query) {
                    return $query
                        ->where('status', Tasks::STATUS_WAITING)
                        ->orWhere('status', Tasks::STATUS_OVERDUE)
                        ->orWhere('status', Tasks::STATUS_IN_WORK);
                }
            )
            ->select(['id', 'status', 'lead_id'])
        ;
    }

    public function lazycons(): hasMany
    {
        return $this->hasMany(Tasks::class, 'lead_id', 'id')
            ->where('type', \App\Models\Enums\Tasks\Type::Consultation->value)
            ->where(
                function ($query) {
                    return $query
                        ->where('status', Tasks::STATUS_WAITING)
                        ->orWhere('status', Tasks::STATUS_OVERDUE)
                        ->orWhere('status', Tasks::STATUS_IN_WORK);
                }
            )
            ->select(['id', 'status', 'lead_id'])
        ;
    }

    public function resptasks(): HasManyThrough
    {
        return $this->HasManyThrough(
            User::class,
            Tasks::class,
            'lead_id', // Внешний ключ в таблице `Tasks` ...
            'id', // Внешний ключ в таблице `User` ...
            'id', // Локальный ключ в таблице `User` ...
            'lawyer' // Локальный ключ в таблице `Tasks` ...);
        )
            ->select(['avatar', 'users.name', 'type'])
        ;
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(Cities::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['findNumber'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('phone', 'like', '%' . $search . '%');
            });
        });
        $query->when($filters['findName'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        });

        $query->when($filters['lawyer'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('lawyer', intval($search));
            });
        });

        $query->when($filters['casettype'] ?? null, function ($query, $search) {            
            $query->where(function ($query) use ($search) {
                $query->where('casettype', $search);
            });
        });

        $query->when($filters['responsible'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('responsible', intval($search));
            });
        });

        $query->when($filters['city'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('city_id', intval($search));
            });
        });

        $query->when($filters['startdate'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->whereDate('created_at', '>=', date($search));
            });
        });

        $query->when($filters['enddate'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->whereDate('created_at', '<=', date($search));
            });
        });
    }
}
