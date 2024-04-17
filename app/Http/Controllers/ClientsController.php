<?php

namespace App\Http\Controllers;

use App\Models\Enums\Clients\Rating;
use Illuminate\Http\Request;
use App\Http\Requests\ClientsRequest;
use App\Models\ClientsModel;
use App\Models\User;
use App\Models\Tasks;
use App\Models\Leads;
use App\Models\Source;
use App\Models\Services;
use App\Models\Enums\Leads\Status;
use App\Repository\ClientRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class ClientsController extends Controller
{
    private $repository;

    public function __construct(ClientRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Список клиентов
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $lawyerId = (!empty($request->checkedlawyer)) ? $request->checkedlawyer : null;
        /** @var User $user */
        $user = Auth::user();
        // Фильтр по статусам задач
        if (!empty($request->statustask)) {
            $statusTask = $request->statustask;

            return view('clients/clients', [
                'data' => $this->repository->getByStatusTasks($lawyerId, $statusTask),
            ], [
                'datalawyers' => User::active()->get(),
                'dataservices' => Services::all(),
                'datatasks' => Tasks::all(),
                'datasource' => Source::all(),
            ]);
        } else {
            return view('clients/clients', [
                'data' => $this->repository->getByClientByLawyer(
                    $request,
                    ($user->isAdmin() || $user->isModerator() || $user->isUserServiceClients())
                ),
            ], [
                'datalawyers' => User::active()->get(),
                'dataservices' => Services::all(),
                'datasource' => Source::all(),
            ]);
        }
    }

    public function show(int $id)
    {
        $client =  $this->repository->findById($id);

        return view('clients/clientbyid', [
            'data' => $client,
        ], [
            'datalawyers' => User::active()->get(),
            'datasource' => Source::all(),
        ]);
    }

    public function store(ClientsRequest $request)

    {

        $client = ClientsModel::new($request);

        //возвращаем без создания клиента, если номер телефона уже существует 
        if (ClientsModel::where('phone', 'like', '%' . $client->phone . '%')->first()) {
            return redirect()->route('clients', [
                'checkedlawyer' => Auth::user()->id,
                'status' => 1,
            ])->with('success', 'Клиент с таким номером телефона уже существует');
        }
        
        $client->saveOrFail();

        //добавляем в лиды с тем же телефоном id клиента
        if ($client->id) {
            Leads::where('phone', 'like', '%' . $client->phone . '%')
                ->update(['client_id' => $client->id, 'status' => Status::Converted->value]);

                //и id первого лида в клиента
                    if(Leads::where('phone', 'like', '%' . $client->phone . '%')->first()){
                        ClientsModel::where('id',$client->id)->update(['lead_id' => Leads::where('phone', 'like', '%' . $client->phone . '%')->first()->id]);
                    }
        }

        return redirect()->route('clients', [
            'checkedlawyer' => Auth::user()->id,
            'status' => 1,
        ])->with('success', 'Все в порядке, клиент добавлен');
    }

    public function update(int $id, ClientsRequest $request)
    {
        $client = ClientsModel::find($id);
        $client->edit($request);
        if (!$request->status) {
            $client->status = null;
        };
        $client->save();

        return redirect()->route('showClientById', $id)->with('success', 'Все в порядке, клиент обновлен');
    }

    public function delete(int $id)
    {
        $client = ClientsModel::find($id);
        $client->status = null;
        $client->save();

        return redirect()->route('clients', [
            'checkedlawyer' => Auth::user()->id,
            'status' => 1,
        ])->with('success', 'Все в порядке, клиент удален (не в работе)');
    }
}
