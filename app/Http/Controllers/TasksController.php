<?php

namespace App\Http\Controllers;

use App\Events\Task\TaskCompleted;
use App\Events\Task\TaskCreated;
use App\Events\Task\TaskDeleted;
use App\Events\Task\TaskUpdated;
use App\Models\ClientsModel;
use Illuminate\Http\Request;
use App\Http\Requests\TasksRequest;
use App\Models\Enums\Leads\Status;
use App\Models\Tasks;
use App\Models\User;
use App\Models\Leads;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Repository\TaskRepository;
use App\Services\TaskService;
use Illuminate\Support\Facades\View;

class TasksController extends Controller
{
    private $repository;
    private $service;

    public function __construct(TaskRepository $repository, TaskService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return view('tasks/tasks', [
            'data' => $this->repository->search($request)->get(),
            'datalawyers' => User::active()->get(),
        ]);
    }

    /**
     * Создание задачи
     * @param TasksRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function store(TasksRequest $request)
    {
        $task = Tasks::new($request);
        $task->saveOrFail();

        if ($request->has('payID')) {
            // Присваиваем добавленные платежи
            $this->service->assignPayments($task, $request->input('payID'));
        }
        // Events
        TaskCreated::dispatch($task);

        return redirect()->back()->with('success', 'Все в порядке, задача добавлена.');
    }

    /**
     * Создание задачи с раздела Лидов
     */
    public function storeByLead(TasksRequest $request)
    {
        $task = Tasks::newFromLead($request);
        $task->saveOrFail();
        // Events
        TaskCreated::dispatch($task);

        return redirect()->back()->with('success', 'Все в порядке, задача добавлена.');
    }

    /**
     * Закрепление тега
     * @param Request $request
     * @return void
     */
    public function tag(Request $request)
    {
        if ($request->get('id')) {
            $id = $request->get('id');
            $task = Tasks::find($id);
            $task->tag = $request->get('value');
            $task->save();
        }
    }

    /**
     * Детальная страница задачи
     * @param $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function showTaskById($request)
    {
        /** @var Tasks $task */
        $task = Tasks::where('id', $request)->with('payments')->first();

        if ($task->lawyer == Auth::user()->id) {
            $task->new = $task::STATE_OLD;
            $task->save();
        }

        return view('tasks/taskById', [
            'data' => $task
        ], [
            'datalawyers' => User::active()->get(),
        ]);
    }

    /**
     * Обновление задачи
     * @param int $id
     * @param TasksRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editTaskById(int $id, TasksRequest $request)
    {
        /** @var Tasks $task */
        $task = Tasks::find($id);
        $task->edit($request);
        $task->save();

        // Привязываем платежи
        $this->service->clearAssignPayments($task);
        if ($request->has('payID')) {
            $this->service->assignPayments($task, $request->input('payID'));
        }

        // Меняем статус лида
        if ($task->lead_id) {
            $lead = Leads::find($task->lead_id);
            $lead->status = Status::In_Working->value;
            $lead->save();
        }
//dd($request->status);
        // Events
        if($request->status != $task->status){
            if ($request->status == $task::STATUS_COMPLETE) {
                $task->status = $task::STATUS_COMPLETE;
                $task->donetime = Carbon::now();
            }  
            else{
                $task->status = $request->status;
            }   
   
            $task->save();
            // Events
            TaskCompleted::dispatch($task);   
        }
        
        //Events
        TaskUpdated::dispatch($task);

        return redirect()->route('showTaskById', $id)->with('success', 'Все в порядке, событие обновлено');
    }

    /**
     * Удаление задачи
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(int $id)
    {
        $task = Tasks::find($id);
        // Events
        TaskDeleted::dispatch($task);
        $task->delete();
        if ($task->lead_id) {
            $lead = Leads::with('lazytasks')->find($task->lead_id);
            if (!$lead->lazytasks->count()) {
                $lead->status = Status::Lazy->value;
                $lead->save();
            } 
        } 

        return redirect()->route('home')->with('success', 'Все в порядке, задача удалена');
    }

    /**
     * Задача провалена
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fail(int $id)
    {
        $task = Tasks::find($id);
        // Events
        $task->status = $task::STATUS_FAIL;
        $task->save();
        if ($task->lead_id) {
            $lead = Leads::with('lazytasks')->find($task->lead_id);
            if (!$lead->lazytasks->count()) {
                $lead->status = Status::Lazy->value;
                $lead->save();
            } 
        }       
        return redirect()->back()->with('success', 'Задача провалена');
    }

    public function complete(int $id)
    {
        $task = Tasks::find($id);
        // Events
        $task->donetime = Carbon::now();
        $task->status = $task::STATUS_COMPLETE;
        $task->save();
        if ($task->lead_id) {
            $lead = Leads::with('lazytasks')->find($task->lead_id);
            if (!$lead->lazytasks->count()) {
                $lead->status = Status::Lazy->value;
                $lead->save();
            } 
        } 
        // Events
        TaskCompleted::dispatch($task);

        if($task->lead_id){
            return redirect()->route('showLeadById', $task->lead_id)->with('success', 'Все в порядке, задача выполнена');
        }
        if($task->clientid){
            return redirect()->route('showClientById', $task->clientid)->with('success', 'Все в порядке, задача выполнена');
        }
        return redirect()->route('showTaskById', $id)->with('success', 'Все в порядке, задача выполнена');
    }

    /**
     * Получить список дел у клиента и сгенерировать выпадающий список
     * @param Request $request
     * @return string|null
     */
    public function getDealsByClient(Request $request): ?string
    {
        $client = ClientsModel::find($request->input('clientId'));
        $taskId = $request->input('taskId');
        $task = ($taskId !== null) ? Tasks::find($taskId) : null;
        $deals = $client->deals();
        $html = null;

        if ($deals->count() > 0) {
            $html = view('deal/_select_list', compact('deals', 'task'));
            return $html;
        }

        return $html;
    }

    /**
     * Подгрузка списка задач по Ajax запросу
     */
    public function getAjaxList(Request $request)
    {
        if ($request->has('query')) {
            $query = $this->repository->getByClientQuery($request->input('query'));
            $output = View::make('inc/modal/_part-edit-payment', compact('query'))->render();

            return $output;
        }

        return null;
    }
}
