<?php

namespace App\Models;

use App\Services\MyCalls\ValueObject\RingDTO;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tasks;
use Illuminate\Http\Client\Request;
use App\Models\ValueObject\SimpleLeadDTO;
use App\Models\Enums\Leads\Status;

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
        'name', 'phone', 'description', 'source',
        'service', 'lawyer', 'responsible', 'status', 'client_id'
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
        return $this->belongsTo(User::class, 'lawyer');
    }

    public function responsibleFunc()
    {
        return $this->belongsTo(User::class, 'responsible');
    }

    public function servicesFunc()
    {
        return $this->belongsTo(Services::class, 'service');
    }

    public function tasks()
    {
        return $this->hasMany(Tasks::class, 'lead_id', 'id');
    }
}
