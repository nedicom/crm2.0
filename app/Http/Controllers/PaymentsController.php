<?php

namespace App\Http\Controllers;

use App\Helpers\PaymentHelper;
use App\Models\User;
use App\Models\Services;
use App\Models\ClientsModel;
use App\Models\Payments;
use Illuminate\Http\Request;
use App\Http\Requests\PaymentsRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Repository\PaymentRepository;
use App\Services\PaymentService;

class PaymentsController extends Controller
{
    private $repository;
    private $service;

    public function __construct(PaymentRepository $repository, PaymentService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Создание платежа
     * @param PaymentsRequest $req
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addpayment(PaymentsRequest $req)
    {
        $payment = new Payments();
        $summ = $req->input('summ');
        $serviceid = $req->input('service');
        $service = DB::table('services')->find($serviceid);
        $price = $service->price; // цена услуги стандартная

        if (!empty($req->input('sellsumm'))) { // если взял предоплату
            $sellsumm = $req->input('sellsumm');  // поступившая предоплата
            $payment->predoplatasumm = $sellsumm; // запись в БД
            $sootnoshnie = $sellsumm / $summ; // расчитываем соотношение цены за которую продали к предоплате
        } else {
            $sellsumm = $summ; // если предоплата не указана выполняется условия когда цена продажи равна сумме поступивших денег
            $payment->predoplatasumm = $summ; // запись в БД
            $sootnoshnie = 1; // соотношение не расчитывается
        }

        if ($payment->isPriceLessPrepayment($price, $sellsumm))
            $payment->calculateByPriceLessPrepayment($price, $sootnoshnie, $summ);

        if ($payment->isPriceLargerPrepayment($price, $sellsumm))
            $payment->calculateByPriceLargerPrepayment($summ);

        if ($payment->isPriceComparePrepayment($price, $sellsumm))
            $payment->calculateByPriceComparePrepayment($summ);

        $payment->edit($req, $summ, $serviceid, true);
        $payment->save();
        // Привязываем задачи
        if ($req->has('taskID')) {
            $this->service->assignTasks($payment, $req->input('taskID'));
        }

        return redirect()->route('payments', [
            'month' => Carbon::now()->format('m'),
            'year'  => Carbon::now()->format('Y'),
        ])->with('success', 'Все в порядке, платеж добавлен');
    }

    /**
     * Просмотр платежей
     * @param Request $req
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function showpayments(Request $req)
    {
        /** @var User $user */
        $user = Auth::user();
        $month = Carbon::now()->format('m');
        $months = PaymentHelper::monthsList();

        if ($user->isHeadSales()) { //для роли админа
            return view ('payments', [
                    'data' => $this->repository->searchByAdmin($req)->get()
                ], [
                    'months' => $months,
                    'month' => $month,
                    'datalawyers' => User::active()->get(),
                    'dataservices' => Services::all()->sortBy('name'),
                    'dataclients' => ClientsModel::all(),
                ]);
        } else { // для остальных ролей
            return view ('payments', [
                'data' => $this->repository->searchByOwner($req)->get()
                ], [
                    'months' => $months,
                    'month' => $month,
                    'datalawyers' => User::active()->get(),
                    'dataservices' => Services::all()->sortBy('name'),
                    'dataclients' => ClientsModel::all(),
                ]);
        }
    }

    /**
     * Просмотр платежа
     * @param mixed $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function showPaymentById($id)
    {
        if (Payments::find($id)->creator_id) {
            $creator = User::find(Payments::find($id)->creator_id)->name;
        } else {
            $creator = "создатель платежа не указан";
        }

        return view ('showPaymentById', [
            'data' => Payments::with('serviceFunc', 'AttractionerFunc', 'sellerFunc', 'developmentFunc')->find($id)
        ],[
            'creator' => $creator,
            'datalawyers' => User::active()->get(),
            'dataservices' => Services::all()->sortBy('name'),
            'dataclients' => ClientsModel::all()
        ]);
    }

    /**
     * Обновление платежа
     * @param $id
     * @param PaymentsRequest $req
     * @return \Illuminate\Http\RedirectResponse
     */
    public function PaymentUpdateSubmit($id, PaymentsRequest $req)
    {
        /** @var Payments $payment */
        $payment = Payments::find($id);
        $summ = $req->input('summ'); // поступивший платеж
        $serviceid = $req->input('service');
        $service = DB::table('services')->find($serviceid);
        $price = $service->price;  // цена услуги стандартная

        if (!empty($req->input('sellsumm'))) { // если взял предоплату
            $sellsumm = $req->input('sellsumm');  // поступившая предоплата
            $payment->predoplatasumm = $sellsumm; // запись в БД
            $sootnoshnie = $sellsumm / $summ; // расчитываем соотношение цены за которую продали к предоплате
        } else {
            $sellsumm = $summ; // если предоплата не указана выполняется условия когда цена продажи равна сумме поступивших денег
            $payment->predoplatasumm = $summ; // запись в БД
            $sootnoshnie = 1; // соотношение не расчитывается
        }

        if ($payment->isPriceLessPrepayment($price, $sellsumm))
            $payment->calculateByPriceLessPrepayment($price, $sootnoshnie, $summ);

        if ($payment->isPriceLargerPrepayment($price, $sellsumm))
            $payment->calculateByPriceLargerPrepayment($summ);

        if ($payment->isPriceComparePrepayment($price, $sellsumm))
            $payment->calculateByPriceComparePrepayment($summ);

        $payment->edit($req, $summ, $serviceid);
        $payment->save();

        // Привязываем задачи
        $this->service->clearAssignTasks($payment);
        if ($req->has('taskID')) {
            $this->service->assignTasks($payment, $req->input('taskID'));
        }

        return redirect()->route('payments', [
            'month' => Carbon::now()->format('m'),
            'year'  => Carbon::now()->format('Y'),
        ])->with('success', 'Все в порядке, платеж обновлен');
    }

    /**
     * Удаление платежа
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function PaymentDelete($id)
    {
        Payments::find($id)->delete();

        return redirect()->route('payments', [
            'month' => Carbon::now()->format('m'),
            'year'  => Carbon::now()->format('Y'),
        ])->with('success', 'Все в порядке, платеж удален');
    }

    /**
     * Подгрузка списка платежей по Ajax запросу
     */
    public function getAjaxList(Request $request)
    {
        if ($request->has('query')) {
            $query = $this->repository->getByClientQuery($request->input('query'));

            $output = '<ul class="list-group">';
            foreach ($query as $value) {
                $output .= '<li class="list-group-item paymentList paymentIndex"
                    data-payment-id="'. $value->id .'"><a href="#" class="text-decoration-none"><span class="name-client">'.
                    $value->client . '</span> - ' . $value->service_name . ' - ' .$value->created_at .'</a></li>';
            }
            $output .= '</ul>';

            return $output;
        }

        return null;
    }
}
