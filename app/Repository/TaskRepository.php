<?php

namespace App\Repository;

use App\Models\Enums\Tasks\DateInterval;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Tasks;
use Illuminate\Database\Eloquent\Builder;

class TaskRepository
{
    /**
     * Скалярное значение кол-ва задач по статусу для авторизованного пользователя
     * @return int
     */
    public function countByStatusForAuth(string $status = Tasks::STATUS_IN_WORK): int
    {
        return DB::table('tasks')->where('lawyer', '=', Auth::id())->where('status', '=', $status)->count();
    }

    /**
     * Выборка задач по поиску
     * @return Builder
     */
    public function search(Request $request)
    {
        // dd($request);
        $query = Tasks::select("*");
        if ($request->input('calendar') !== null && $request->input('calendar') !== DateInterval::AllTime->name)
            $query = $this->betweenDate($query, $request);
        if ($request->input('checkedlawyer')) {
            $query->where(function ($q) use ($request) {
                $q->where('lawyer', '=', $request->input('checkedlawyer'))
                    ->orWhere('postanovshik', '=', $request->input('checkedlawyer'));
            });
            $query->orderByRaw(
                "CASE WHEN lawyer = ? THEN 0 ELSE 1 END",
                [$request->input('checkedlawyer')]
            );
        }
        if ($request->input('type')) $query->where('type', '=', $request->input('type'));

        if ($request->input('tasktime')) {
            switch ($request->input('tasktime')) {
                case 'lesshour':
                    $query->where('duration', '<', 60);
                    break;
                case 'morehour':
                    $query->where('duration', '>=', 60);
                    break;
            }
        }

        $query->orderBy('date');
        return $query;
    }

    /**
     * Фильтр по интервалу даты
     * @param Builder $query
     * @param Request $request
     * @return Builder
     */
    private function betweenDate(Builder $query, Request $request): Builder
    {
        $month = $request->input('months');

        // Если months не передан, используем текущий месяц
        if ($month === null) {
            $month = Carbon::now()->month - 1;
        }

        return match ($request->input('calendar')) {
            DateInterval::Yesterday->name => $query->whereBetween('date', [Carbon::yesterday()->startOfDay(), Carbon::yesterday()->endOfDay()]),
            DateInterval::Today->name => $query->whereBetween('date', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()]),
            DateInterval::Tomorrow->name => $query->whereBetween('date', [Carbon::tomorrow()->startOfDay(), Carbon::tomorrow()->endOfDay()]),
            DateInterval::Day->name => $query->whereBetween('date', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()]),
            DateInterval::Week->name => $query->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]),
            DateInterval::Month->name => $query->whereBetween('date', [
                Carbon::now()->month($month + 1)->startOfMonth(),
                Carbon::now()->month($month + 1)->endOfMonth()
            ]),
        };
    }

    /**
     * Список задач по search запросу имени клиента
     * @param string  $clientName Имя клиента
     * @return mixed
     */
    public function getByClientQuery(string $clientName)
    {
        $query = DB::table('tasks')
            ->select(['id', 'name', 'client', 'created_at'])
            ->where('client', 'LIKE', '%' . $clientName . '%')
            ->get();

        return $query;
    }
}
