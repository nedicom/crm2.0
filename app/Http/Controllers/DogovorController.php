<?php

namespace App\Http\Controllers;

use App\Models\Dogovor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ClientsModel;
use App\Models\Services;
use App\Models\Cities;
use Illuminate\Support\Facades\File;
use App\Services\ContractsService;
use Illuminate\Support\Facades\Auth;

class DogovorController extends Controller
{
    private $service;

    public function __construct(ContractsService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $query = Dogovor::orderByDesc('created_at')
            ->where('created_at', '>', Carbon::now()->subDays(365))
            ->with('userFunc')
            ->with('clientFunc')
            ->with('city');

        // Поиск по фамилии клиента
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->whereHas('clientFunc', function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%");
            });
        }

        return view('dogovor/dogovor', [
            'data' => $query->limit(21)->get([
                'lawyer_id',
                'name',
                'allstoimost',
                'created_at',
                'url',
                'client_id',
                'subject',
                'city_id'
            ])
        ], [
            //'avg' => $avg,
            'dataservice' => Services::all(),
            'datalawyers' => User::all(),
            'dataclients' => ClientsModel::all(),
            'currentuser' => Auth::user(),
            'cities'  => Cities::all(),
        ]);
    }

    public function showdogovorById($id)
    {
        return view('dogovor/showdogovorById', [
            'data' => Dogovor::with('userFunc', 'clientFunc', 'city')->find($id)
        ], [
            'datalawyers' => User::all(),
            'cities'  => Cities::all(),
        ]);
    }

    public function store(Request $request)
    {
        $today = Carbon::now()->toDateString(); // Дата без времени

        if ($request->input('ispolnitelinput') == 'ipmina') {
            $name = 'ИП Мина ' . $request->input('name');
        } else {
            $name = 'Адвокат Мина ' . $request->input('name');
        }
        $contractUrl = 'dogovors/' . $name . ' - ' . $today . '.docx';

        try {
            $contacts = $this->service->new($today, $contractUrl, $request);
            if (!empty($contacts)) {
                $file = $this->service->attachFile($contractUrl, $today, $request);
            }
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('contracts.index')
            ->withHeaders([
                'Content-Type' => 'application/octet-stream',
                'Accept-Ranges' => 'bytes',
                'Content-Length' => File::size($file),
                'Content-Disposition' => "attachment; filename=" . $name . ".docx",
            ])
            ->with('success', "Все в порядке, договор $contacts->name добавлен");
    }
}
