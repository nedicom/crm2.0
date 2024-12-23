<?php

namespace App\Http\Controllers;

use App\Models\Leads;
use App\Models\User;
use App\Models\Services;
use App\Models\Source;
use Illuminate\Http\Request;
use App\Http\Requests\LeadsRequest;
use App\Models\Enums\Leads\Status;
use App\Models\ClientsModel;
use App\Models\Cities;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

use App\Services\TG\LeadTg;

class LeadsController extends Controller
{
    public function addlead(LeadsRequest $req)
    {
        $lead = new Leads();
        $lead->name = $req->input('name');
        $lead->source = $req->input('source');
        $lead->description = $req->input('description');
        $lead->phone = $req->input('phone');
        $lead->lawyer = $req->input('lawyer');
        $lead->responsible = $req->input('responsible');
        $lead->casettype = $req->input('casettype');
        $lead->city_id = $req->input('city');
        $lead->state = $req->input('state');
        $lead->service = 11;
        $lead->status = 'поступил';

        $lead->save();
        
        $city = isset($lead->city_id) ? Cities::find($lead->city_id)->city : 'не определен';
        $responsible = isset($lead->responsible) ? User::find($lead->responsible)->name : 'не определен';

        LeadTg::SendleadTg( "Новый лид\nГород - " . $city . "\nТип дела - " . $lead->casettype . 
        "\nОтветсвенный - " . $responsible . "\Источник - " . $lead->source . "\n" . $lead->description . "\nhttps://crm.nedicom.ru/leads/" . $lead->id);

        return redirect()->route('leads')->with('success', 'Все в порядке, лид добавлен');
    }

    public function showleads(Request $req)
    {       
        //$today_date = Carbon::now()->subMonths(12)->toDateTimeString();
        //$trash_date = Carbon::now()->subMonths(3)->toDateTimeString();

        $today_date = Carbon::now()->subDays(12)->toDateTimeString();
        $trash_date = Carbon::now()->subDays(3)->toDateTimeString();
        session([
            'number' => null,
            'name' => null,
            'lawyer' => null,
            'responsible' => null,
            'casettype' => null,
        ]);

        $query = Leads::query();
        $newquery = Leads::query();
        $phonequery = Leads::query();
        $consquery = Leads::query();
        $defeatquery = Leads::query();
        $withoutcasequery = Leads::query();
        $winquery = Leads::query();
        $failleadsquery = Leads::query();


        if ($req->findNumber ||  $req->findName ||  $req->lawyer ||  $req->responsible ||  $req->casettype ||  $req->startdate ||  $req->enddate ||  $req->city) {
            session([
                'number' => $req->findNumber,
                'name' => $req->findName,
                'lawyer' => $req->lawyer,
                'responsible' => $req->responsible,
                'casettype' => $req->casettype,
            ]);
            $query = Leads::filter($req->all());
            $newquery = Leads::filter($req->all());
            $phonequery = Leads::filter($req->all());
            $consquery = Leads::filter($req->all());
            $defeatquery = Leads::filter($req->all());
            $withoutcasequery = Leads::filter($req->all());
            $winquery = Leads::filter($req->all());
            $failleadsquery = Leads::filter($req->all());
        }

        return view(
            'leads/leads',
            [
                'allleads' => $query->orderBy('id', 'desc')
                    ->with('userFunc')->with('responsibleFunc')->with('city')
                    ->take(10)
                    ->get(),

                'newleads' => $newquery->where('leads.status', '=', 'поступил')->orWhere('leads.status', '=', 'сгенерирован')->orderBy('id', 'desc')
                    ->with('userFunc')->with('responsibleFunc')->with('city')
                    ->select(['id', 'name', 'source', 'casettype', 'description', 'phone', 'lawyer', 'created_at', 'updated_at', 'responsible', 'service', 'status', 'state', 'city_id'])
                    ->take(10)
                    ->get(),

                'phoneleads' => $phonequery->has('lazyphone')
                    ->orderBy('id', 'desc')
                    ->with('lazyphone')
                    ->with('userFunc')->with('responsibleFunc')->with('city')
                    ->select(['id', 'name', 'source', 'casettype', 'description', 'phone', 'lawyer', 'created_at', 'updated_at', 'responsible', 'service', 'status', 'state', 'city_id'])
                    ->take(10)
                    ->get(),

                'consleads' => $consquery->has('lazycons')
                    ->orderBy('id', 'desc')
                    ->with('lazycons')
                    ->with('userFunc')->with('responsibleFunc')->with('city')->with('tasks')
                    ->select(['id', 'name', 'source', 'casettype', 'description', 'phone', 'lawyer', 'created_at', 'updated_at', 'responsible', 'service', 'status', 'state', 'city_id'])
                    ->take(10)
                    ->get(),

                'defeatleads' =>  $defeatquery->where('leads.status', '=', 'удален')->orderBy('id', 'desc')
                    ->whereDate('created_at', '>=', $trash_date)->with('userFunc')->with('responsibleFunc')->with('city')
                    ->select(['id', 'name', 'source', 'casettype', 'description', 'phone', 'lawyer', 'created_at', 'updated_at', 'responsible', 'service', 'status', 'state', 'city_id'])
                    ->get(),

                'withoutcaseleads' => $withoutcasequery
                    ->where('status', Status::Lazy->value)
                    ->orderBy('id', 'desc')
                    ->whereDate('created_at', '>=', $today_date)
                    ->with('userFunc')->with('responsibleFunc')->with('city')
                    ->get(),

                'winleads' => $winquery->where('leads.status', '=', 'конвертирован')->orderBy('id', 'desc')->whereDate('created_at', '>=', $today_date)->with('userFunc')->with('city')
                    ->select(['id', 'name', 'source', 'casettype', 'description', 'phone', 'lawyer', 'created_at', 'updated_at', 'responsible', 'service', 'status', 'state', 'city_id'])
                    ->get(),

                'failleads' => $failleadsquery->where('leads.status', Status::Defeat->value)->orderBy('id', 'desc')->whereDate('created_at', '>=', $today_date)
                    ->with('userFunc')->with('city')
                    ->select(['id', 'name', 'source', 'casettype', 'description', 'phone', 'lawyer', 'created_at', 'updated_at', 'responsible', 'service', 'status', 'state', 'city_id'])
                    ->get(),

                'datasource'   => Source::all(),
                'dataservices' => Services::all(),
                'datasources' =>  Source::all('name'),
                'datalawyers'  => User::active()->get(),
                'cities'  => Cities::all(),
            ],
        );
    }

    public function showoldleads(Request $req)
    {
        $lawyer = null;
        $source = null;
        $responsible = null;
        $status = $req->input('checkedstatus');

        if (!empty($req->checkedlawyer)) {
            $lawyer = 'lawyer';
        }
        if (empty($req->checkedstatus)) {
            $deleting = 'удален';
        } else {
            $deleting = '';
        }
        if (!empty($req->checkedsources)) {
            $source = 'source';
        }
        if (!empty($req->checkedresponsible)) {
            $responsible = 'responsible';
        }

        return view(
            'leads/oldleads',
            ['data' => Leads::where($lawyer, $req->checkedlawyer)
                ->when($status, function ($query, $status) {
                    $query->where('status', $status);
                })
                ->where('status', '!=', $deleting)
                ->where($source, $req->checkedsources)
                ->where($responsible, $req->checkedresponsible)
                ->orderBy('created_at', 'desc')
                ->get()],
            [
                'datalawyers'  => User::active()->get(),
                'dataservices' => Services::all(),
                'datasources' =>  Source::all('name'),
                'datasource'   => Source::all(),
            ]
        );
    }

    public function leadanalitics()
    {
        return view(
            'leads/leadanalitics'
        );
    }

    public function showLeadById($id)
    {
        return view(
            'leads/showLeadById',
            ['data' => Leads::with(
                'userFunc',
                'responsibleFunc',
                'servicesFunc'
            )->find($id)],
            [
                'datalawyers' => User::active()->get(),
                'dataservices' => Services::all(),
                'datasource' => Source::all(),
                'cities'  => Cities::all(),
            ]
        );
    }

    public function LeadUpdateSubmit($id, LeadsRequest $req)
    {
        $lead = Leads::find($id);
        $lead->name = $req->input('name');
        $lead->source = $req->input('source');
        $lead->description = $req->input('description');
        $lead->phone = $req->input('phone');
        $lead->lawyer = $req->input('lawyer');
        $lead->responsible = $req->input('responsible');
        $lead->casettype = $req->input('casettype');
        $lead->city_id = $req->input('city');
        $lead->state = $req->input('state');
        $lead->service = 11;
        $lead->status == Status::Entered->value ? $lead->status = Status::Lazy->value : null;
        $lead->save();

        return redirect()->route('showLeadById', $id)->with('success', 'Все в порядке, лид обновлен');
    }


    public function leadToWork($id, Request $req)
    {
        $lead = Leads::find($id);
        $lead->status = 'в работе';
        $lead->action = $req->input('action');
        $lead->save();

        return redirect()->route('showLeadById', $id)->with('success', 'Все в порядке, лид в работе');
    }

    public function leadToClient($id, Request $req)
    {
        $lead = Leads::find($id);
        $lead->status = 'конвертирован';
        $lead->successreason = $req->input('successreason');
        $lead->save();

        $client = new ClientsModel();
        $client->name = $lead->name;
        $client->phone = $lead->phone;
        $client->email = 'email';
        $client->source = $lead->source;
        $client->status = 1;
        $client->lawyer = $lead->lawyer;
        $client->save();

        $clientid = $client->id;

        return redirect()->route('showClientById', $clientid)->with('success', 'Поздравляем, лид стал клиентом');
    }

    public function leadDelete($id, Request $req)
    {
        $lead = Leads::find($id);
        $lead->status = 'удален';
        $lead->failurereason = $req->input('failurereason');
        $lead->save();

        return redirect()->route('leads')->with('success', 'Все в порядке, лид удален');
    }

    public function leadFail($id, Request $req)
    {
        $lead = Leads::find($id);
        $lead->status = 'провален';
        $lead->description = $lead->description . ' Причина провала - ' . $req->input('defeatreason');
        $lead->save();

        return redirect()->route('leads')->with('success', 'Лид перемещен в проваленные. Его можно будет обработать потом.');
    }
}
