<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $id
 * @property int $client_id
 * @property int $lead_id
 * @property string $name
 * @property string $lawyer_id
 * @property \DateTime $date
 * @property string $subject
 * @property int $created_at
 * @property int $updated_at
 * @property string $allstoimost
 * @property string $preduslugi
 * @property string $predoplata
 * @property string $url
 *
 * @property ClientsModel $clientFunc
 * @property User $userFunc
 */
class Dogovor extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @param Request $request
     * @param $date
     * @param string $filePath
     * @return static
     */
    public static function new(Request $request, $date, string $filePath): self
    {
        $contract = new self();
        $contract->fill($request->except(['_token', 'clientidinput', 'adress', 'client', 'phone']));
        $contract->client_id = $request->input('clientidinput');

        $client = ClientsModel::find($request->input('clientidinput'));
        if ($client->lead_id) {
            $contract->lead_id = $client->lead_id;
        }
        $contract->lawyer_id = Auth::id();
        $contract->date = $date;
        $contract->url = $filePath;

        return $contract;
    }

    public function userFunc()
    {
        return $this->belongsTo(User::class, 'lawyer_id', 'id');
    }

    public function clientFunc()
    {
        return $this->belongsTo(ClientsModel::class, 'client_id', 'id');
    }
}
