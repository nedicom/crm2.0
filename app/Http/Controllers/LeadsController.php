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

    public function addleadFromRequest(LeadsRequest $req)
    {
        $lead = new Leads();
        $lead->name = 'test';
        $lead->source = 'test';
        $lead->description = 'test';
        $lead->phone = 79788838978;
        $lead->lawyer = 18;
        $lead->responsible = 18;
        $lead->service = 11;
        $lead->status = 'поступил';

        $lead->save();

        LeadTg::SendleadTg($lead);

        return 'its ok';
    }

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

        LeadTg::SendleadTg($lead);

        return redirect()->route('leads')->with('success', 'Все в порядке, лид добавлен');
    }

    public function leadsfilter($leadpath, Request $req)
    {
        $today_date = Carbon::now()->subMonths(3)->toDateTimeString();

        if ($leadpath) {
            session([
                'number' => $req->findNumber,
                'name' => $req->findName,
                'lawyer' => $req->lawyer,
                'responsible' => $req->responsible,
                'casettype' => $req->casettype,
            ]);

            switch ($leadpath) {
                case 'all':
                    $leads = Leads::filter($req->all())->orderBy('created_at', 'desc')
                        ->with('userFunc')->with('responsibleFunc')->with('city')
                        ->simplePaginate(100);
                    break;

                case 'new':
                    $leads = Leads::filter($req->all())->where(function ($q) {
                        $q
                            ->Where('leads.status', '=', 'поступил')
                            ->orWhere('leads.status', '=', 'сгенерирован');
                    })
                        ->orderBy('created_at', 'desc')
                        ->with('userFunc')->with('responsibleFunc')->with('city')
                        ->select(['id', 'name', 'source', 'casettype', 'description', 'phone', 'lawyer', 'created_at', 'updated_at', 'responsible', 'service', 'status', 'state', 'city_id'])
                        ->simplePaginate(100);
                    break;

                case 'phone':
                    $leads = Leads::filter($req->all())->has('lazyphone')
                        ->orderBy('created_at', 'desc')
                        ->with('lazyphone')->with('userFunc')->with('responsibleFunc')->with('city')
                        ->select(['id', 'name', 'source', 'casettype', 'description', 'phone', 'lawyer', 'created_at', 'updated_at', 'responsible', 'service', 'status', 'state', 'city_id'])
                        ->simplePaginate(100);
                    break;

                case 'consleads':
                    $leads = Leads::filter($req->all())->has('lazycons')
                        ->orderBy('created_at', 'desc')
                        ->with('lazycons')
                        ->with('userFunc')->with('responsibleFunc')->with('city')->with('tasks')
                        ->select(['id', 'name', 'source', 'casettype', 'description', 'phone', 'lawyer', 'created_at', 'updated_at', 'responsible', 'service', 'status', 'state', 'city_id'])
                        ->simplePaginate(100);
                    break;

                case 'defeatleads':
                    $leads =  Leads::filter($req->all())->where('leads.status', '=', 'удален')->orderBy('id', 'desc')
                        ->with('userFunc')->with('responsibleFunc')->with('city')
                        ->select(['id', 'name', 'source', 'casettype', 'description', 'phone', 'lawyer', 'created_at', 'updated_at', 'responsible', 'service', 'status', 'state', 'city_id'])
                        ->simplePaginate(100);
                    break;

                case 'withoutcaseleads':
                    $leads =  Leads::filter($req->all())->where('status', Status::Lazy->value)
                        ->orderBy('id', 'desc')
                        ->whereDate('created_at', '>=', $today_date)
                        ->with('userFunc')->with('responsibleFunc')->with('city')
                        ->simplePaginate(100);
                    break;

                case 'winleads':
                    $leads =  Leads::filter($req->all())->where('leads.status', '=', 'конвертирован')->orderBy('id', 'desc')->whereDate('created_at', '>=', $today_date)->with('userFunc')->with('city')
                        ->select(['id', 'name', 'source', 'casettype', 'description', 'phone', 'lawyer', 'created_at', 'updated_at', 'responsible', 'service', 'status', 'state', 'city_id'])
                        ->simplePaginate(100);
                    break;

                case 'failleads':
                    $leads =  Leads::filter($req->all())->where('leads.status', Status::Defeat->value)->orderBy('id', 'desc')->whereDate('created_at', '>=', $today_date)
                        ->with('userFunc')->with('city')
                        ->select(['id', 'name', 'source', 'casettype', 'description', 'phone', 'lawyer', 'created_at', 'updated_at', 'responsible', 'service', 'status', 'state', 'city_id'])
                        ->simplePaginate(100);
                    break;
            }
        }

        $allleadscount = Leads::filter($req->all())->count();

        $newleadscount = Leads::filter($req->all())->where(function ($q) {
            $q
                ->Where('leads.status', '=', 'поступил')
                ->orWhere('leads.status', '=', 'сгенерирован');
        })->count();

        $phoneleads = Leads::filter($req->all())->has('lazyphone')
            ->orderBy('created_at', 'desc')
            ->with('lazyphone')->with('userFunc')->with('responsibleFunc')->with('city')
            ->select(['id', 'name', 'source', 'casettype', 'description', 'phone', 'lawyer', 'created_at', 'updated_at', 'responsible', 'service', 'status', 'state', 'city_id'])
            ->count();

        $consleads = Leads::filter($req->all())->has('lazycons')
            ->orderBy('created_at', 'desc')
            ->with('lazycons')
            ->with('userFunc')->with('responsibleFunc')->with('city')->with('tasks')
            ->select(['id', 'name', 'source', 'casettype', 'description', 'phone', 'lawyer', 'created_at', 'updated_at', 'responsible', 'service', 'status', 'state', 'city_id'])
            ->count();

        $defeatleads = Leads::filter($req->all())->where('leads.status', '=', 'удален')->orderBy('id', 'desc')
            ->with('userFunc')->with('responsibleFunc')->with('city')
            ->select(['id', 'name', 'source', 'casettype', 'description', 'phone', 'lawyer', 'created_at', 'updated_at', 'responsible', 'service', 'status', 'state', 'city_id'])
            ->count();

        $withoutcaseleads = Leads::filter($req->all())->where('status', Status::Lazy->value)
            ->orderBy('id', 'desc')
            ->whereDate('created_at', '>=', $today_date)
            ->with('userFunc')->with('responsibleFunc')->with('city')
            ->count();

        $winleads = Leads::filter($req->all())->where('leads.status', '=', 'конвертирован')->orderBy('id', 'desc')->whereDate('created_at', '>=', $today_date)->with('userFunc')->with('city')
            ->select(['id', 'name', 'source', 'casettype', 'description', 'phone', 'lawyer', 'created_at', 'updated_at', 'responsible', 'service', 'status', 'state', 'city_id'])
            ->count();

        $failleads = Leads::filter($req->all())->where('leads.status', Status::Defeat->value)->orderBy('id', 'desc')->whereDate('created_at', '>=', $today_date)
            ->with('userFunc')->with('city')
            ->select(['id', 'name', 'source', 'casettype', 'description', 'phone', 'lawyer', 'created_at', 'updated_at', 'responsible', 'service', 'status', 'state', 'city_id'])
            ->count();

        return view(
            'leads/leadfilter',
            [
                'allleadscount' => $allleadscount,
                'newleadscount' => $newleadscount,
                'phoneleads' => $phoneleads,
                'consleads' => $consleads,
                'defeatleads' => $defeatleads,
                'withoutcaseleads' => $withoutcaseleads,
                'winleads' => $winleads,
                'failleads' => $failleads,

                'leads' => $leads,

                'route' => $leadpath,

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
