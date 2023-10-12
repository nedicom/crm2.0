<?php

namespace App\Http\Controllers;

use App\Events\Task\TaskCompleted;
use App\Events\Task\TaskCreated;
use App\Events\Task\TaskDeleted;
use App\Events\Task\TaskUpdated;
use App\Models\ClientsModel;
use Illuminate\Http\Request;
use App\Http\Requests\TasksRequest;
use App\Models\Tasks;
use App\Models\User;
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
            // Присваевываем добавленные платежи
            $this->service->assignPayments($task, $request->input('payID'));
        }
        // Events
        TaskCreated::dispatch($task);

        return redirect()->back()->with('success', 'Все в порядке, задача добавлена.');
    }

    /**
     * Создание задачи с раздела лидов
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
        $task = Tasks::find($request);

        if ($task->lawyer == Auth::user()->id) {
           $task->new = $task::STATE_OLD;
           $task->save();
        }

        return view('tasks/taskById', [
            'data' => Tasks::find($request)], [
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
        // Events
        if ($task->status === $task::STATUS_COMPLETE) {
            $task->donetime = Carbon::now();
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

        return redirect()->route('tasks')->with('success', 'Все в порядке, задача удалена');
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
