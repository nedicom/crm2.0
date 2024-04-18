<?php

namespace App\Repository;

use App\Models\ClientsModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ClientRepository
{
    /**
     * Возвращение коллекции клиентов по статусу задач ('просрочена', 'в работе', 'ожидают')
     * @param int|null $lawyerID ID Юриста
     * @param string|null $statusTask Статус задачи
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByStatusTasks($lawyerID, ?string $statusTask)
    {
        $query = ClientsModel::whereHas('tasksFunc', function (Builder $query) use ($lawyerID, $statusTask) {
            $query->where('status', '=', $statusTask)
                ->where('lawyer', '=',  $lawyerID);
        }, '>=', 1)->get();

        return $query;
    }

    /**
     * Возвращение коллекции клиентов по имени клиента для юриста
     * @param Request $request
     * @param boolean $adminRole
     * @return \Illuminate\Support\Collection
     */
    public function getByClientByLawyer(Request $request, $adminRole = false)
    {
        $findclient = $request->findclient;
        //dd($request->findclient);
        if($request->findclient){
            $query = ClientsModel::where('name', 'LIKE', '%' . $findclient . '%')->orWhere('phone', 'LIKE', '%' . $findclient . '%');
        }
        else{
            $query = ClientsModel::where('name', 'LIKE', '%' . $findclient . '%');
        }
        if ($request->checkedlawyer && $adminRole) $query->where('lawyer', $request->checkedlawyer);
        // Если роль не админ, то список клиентов возвращать только для текущего пользователя
        if (!$adminRole) $query->where('lawyer', Auth::id());
        $query->where('status', $request->status);

        return $query->get();
    }

    /**
     * Найти объект по его id
     * @param int $id
     * @return Builder
     */
    public function findById(int $id)
    {
        $query = ClientsModel::with('userFunc', 'tasksFunc', 'serviceFunc', 'paymentsFunc', 'paymsThroughTask') 
        ->find($id);
        return $query;
    }
}
