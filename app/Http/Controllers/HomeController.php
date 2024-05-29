<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ClientsModel;
use App\Models\Leads;
use App\Models\Tasks;
use App\Models\Payments;
use App\Models\Dogovor;
use App\Models\User;
use Carbon\Carbon;


class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $req)
    {
        DB::statement("SET lc_time_names = 'ru_RU'");
        
        $tasks = Tasks::where('lawyer', Auth::user()->id)->get();
        foreach ($tasks as $el) {
            if (Carbon::now()->gte([$el->date][0]['value']) && $el->status != 'выполнена') {
                $el->status = 'просрочена';
                $el->save();
            }
        }

        if ($req->input('lawyer')) {
            $crtuser = $req->input('lawyer');
        } else {
            $crtuser = Auth::user()->id;
        }

        if ($req->input('date')) {
            if ($req->input('date') == 'month'){
                $where = 'whereMonth';
            } else {
                $where = 'whereDate';
            }
        } else {
            $where = 'whereDate';
        }

        return view('home', [
            'user' => Auth::user(),
            'crtuser' => User::where('id', $crtuser)->first(),
            'data' => [
                'clients' => ClientsModel::with('userFunc')
                    ->where('lawyer', $crtuser)
                    ->count(),
                'leads' => Leads::with('userFunc')
                    ->where('lawyer', $crtuser)
                    ->count(),
                'tasks' => Tasks::with('userFunc')
                    ->where('lawyer', $crtuser)
                    ->count(),
                'datalawyers' =>  User::all()
            ],
        ], [
            'all' => [
                'allclients' => ClientsModel:: where('lawyer', $crtuser)
                    ->$where('created_at', (Carbon::today()))
                    ->get(),
                'alltasks' => Tasks::where('lawyer', $crtuser)
                    ->$where('created_at', (Carbon::today()))
                    ->get(),
                'alltaskstoday' => Tasks::where('lawyer', $crtuser)
                    ->$where('date', (Carbon::today()))
                    ->get(),
                'alltaskstime' => Tasks::where('lawyer', $crtuser)
                    ->where('status', 'просрочена')
                    ->where('donetime', '=', null)                    
                    ->select(
                        'tasks.id', 'tasks.name', 'tasks.tag', 'tasks.client', 'tasks.type',
                        DB::raw("DATE_FORMAT(tasks.created_at, '%d %M %Y в %H:%m') as created"),
                        DB::raw("DATE_FORMAT(tasks.date, '%d %M %Y в %H:%m') as donedate"),
                    ) 
                    ->get(),
                'alltaskspostanovshik' => Tasks::where('postanovshik', $crtuser)
                    ->where('status', 'просрочена')
                    ->where('donetime', '=', null)                    
                    ->select(
                        'tasks.id', 'tasks.name', 'tasks.tag', 'tasks.client', 'tasks.type',
                        DB::raw("DATE_FORMAT(tasks.created_at, '%d %M %Y в %H:%m') as created"),
                        DB::raw("DATE_FORMAT(tasks.date, '%d %M %Y в %H:%m') as donedate"),
                    )                    
                    ->get(),
                'alltaskssoispolnitel' => Tasks::where('soispolintel', $crtuser)
                    ->where('status', 'просрочена')
                    ->get(),
                'alltasksnew' => Tasks::where('lawyer', $crtuser)
                    ->where('new', 1)
                    ->where('donetime', '=', null)                    
                    ->select(
                        'tasks.id', 'tasks.name', 'tasks.tag', 'tasks.client', 'tasks.type',
                        DB::raw("DATE_FORMAT(tasks.created_at, '%d %M %Y в %H:%m') as created"),
                        DB::raw("DATE_FORMAT(tasks.date, '%d %M %Y в %H:%m') as donedate"),
                    )                        
                    ->get(),
                'alltasksnewsoispolnitel' => Tasks::where('soispolintel', $crtuser)
                    ->where('new', 1)
                    ->get(),
                'alltasksnewpostanovshik' => Tasks::where('postanovshik', $crtuser)
                    ->where('new', 1)
                    ->where('donetime', '=', null) 
                    ->select(
                        'tasks.id', 'tasks.name', 'tasks.tag', 'tasks.client', 'tasks.type',
                        DB::raw("DATE_FORMAT(tasks.created_at, '%d %M %Y в %H:%m') as created"),
                        DB::raw("DATE_FORMAT(tasks.date, '%d %M %Y в %H:%m') as donedate"),
                    )  
                    ->get(),
                'allpayments' => Payments::where('nameOfAttractioner', $crtuser)
                    ->$where('created_at', (Carbon::today()))
                    ->get(),
                'alldogovors' => Dogovor::where('lawyer_id', $crtuser)
                    ->$where('created_at', (Carbon::today()))
                    ->get(),
                'allleads' => Leads::where('lawyer', $crtuser)
                    ->$where('created_at', (Carbon::today()))
                    ->get(),
                'allleadsoverdue' => Leads::where('lawyer', $crtuser)
                    ->$where('created_at', '<', (Carbon::today()))
                    ->where('status', 'поступил')
                    ->get(),
            ]
        ]);
    }
}
