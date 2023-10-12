<?php

namespace App\Http\Controllers;

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
        $summ = $req -> input('summ');
        $payment->summ = $summ;
        $payment->creator_id = Auth::id();

        $serviceid = $req->input('service');
        $payment->service = $serviceid;
        $service = DB::table('services')->find($serviceid);
        $price = $service->price; // цена услуги стандартная

        if(!empty($req->input('sellsumm'))) { // если взял предоплату
          $sellsumm = $req->input('sellsumm');                       // поступившая предоплата
          $payment->predoplatasumm = $sellsumm; // запись в БД
          $sootnoshnie = $sellsumm/$summ; // расчитываем соотношение цены за которую продали к предоплате
        } else {
          $sellsumm = $summ; // если предоплата не указана выполняется условия когда цена продажи равна сумме поступивших денег
          $sootnoshnie = 1; // соотношение не расчитывается
          $payment->predoplatasumm = $summ; // запись в БД
        }

        if ($price < $sellsumm) {
            $totalprice = $price / $sootnoshnie;
            $payment -> SallerSalary = $totalprice/100*13; // доход продавца от цены услуги (производной)
            $payment -> modifySeller = ($summ - $totalprice)/100*17; // увеличение дохода продавца от разницы в платеже минус цены услуги
            $payment -> AttaractionerSalary = $totalprice/100*20; // доход привлеченца
            $payment -> modifyAttraction = ($summ - $totalprice)/100*33; // увеличение дохода привлеченца
            $payment -> DeveloperSalary = $summ/100*17; // доход развивателя
        }

        if($price > $sellsumm){
          $payment -> SallerSalary = $summ/100*5;
          $payment -> AttaractionerSalary = $summ/100*10;
          $payment -> DeveloperSalary = $summ/100*10;
          $payment -> modifyAttraction = 0;
          $payment -> modifySeller = 0;
        }

        if($price == $sellsumm){
          $payment -> SallerSalary = $summ/100*13;
          $payment -> AttaractionerSalary = $summ/100*20;
          $payment -> DeveloperSalary = $summ/100*17;
          $payment -> modifyAttraction = 0;
          $payment -> modifySeller = 0;
        }

        $payment -> calculation = $req -> input('calculation');
        $payment -> client = $req -> input('client');
        if($req -> input('clientidinput')){$payment -> clientid = $req -> input('clientidinput');};

        $payment -> nameOfAttractioner = $req -> input('nameOfAttractioner');
        $payment -> nameOfSeller = $req -> input('nameOfSeller');
        $payment -> directionDevelopment = $req -> input('directionDevelopment');
        $payment -> firmearning = ($summ - $payment -> SallerSalary - $payment -> AttaractionerSalary
         - $payment -> DeveloperSalary - $payment -> modifyAttraction - $payment -> modifySeller);

        $payment->save();

        // Привязываем задачи
        if ($req->has('taskID')) {
            $this->service->assignTasks($payment, $req->input('taskID'));
        }

        return redirect() -> route('payments') -> with('success', 'Все в порядке, платеж добавлен');
    }

    public function showpayments(Request $req)
    {
        $nameOfAttractioner = null;
        $nameOfSeller = null;
        $directionDevelopment = null;
        $calculation = null;
        $month=Carbon::now()->format('m');
        $year=Carbon::now()->format('Y');
        $months = [1 => 'январь',2  => 'февраль',3  => 'март',4  => 'апрель',5  => 'май',6  => 'июнь',7  => 'июль',
            8  => 'август',9  => 'сентябрь',10 => 'октябрь',11 => 'ноябрь',12 => 'декабрь'];

        if ((Auth::user()->role) == 'admin'){//для авторизированных
            if (!empty($req->nameOfAttractioner)){$nameOfAttractioner='nameOfAttractioner';}
            if (!empty($req->nameOfSeller)){$nameOfSeller='nameOfSeller';}
            if (!empty($req->directionDevelopment)){$directionDevelopment='directionDevelopment';}
            if (!empty($req->calculation)){$calculation='calculation';}
            if (!empty($req->month)){$month=$req->month;}
            if (!empty($req->year)){$year=$req->year;}

            return view ('payments', ['data' => Payments::with('serviceFunc', 'AttractionerFunc', 'sellerFunc', 'developmentFunc')
                ->where($nameOfAttractioner, $req->nameOfAttractioner)
                ->where($nameOfSeller, $req->nameOfSeller)
                ->where($directionDevelopment, $req->directionDevelopment)
                ->where($calculation, $req->calculation)
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->get()], [
                    'months' => $months,
                    'month' => $month,
                    'datalawyers' => User::active()->get(),
                    'dataservices' => Services::all(),
                    'dataclients' => ClientsModel::all(),
                ]);
        } else { //для не авторизированных
            if (!empty($req->nameOfAttractioner)){$nameOfAttractioner='nameOfAttractioner';}
            if (!empty($req->nameOfSeller)){$nameOfSeller='nameOfSeller';}
            if (!empty($req->directionDevelopment)){$directionDevelopment='directionDevelopment';}
            if (!empty($req->month)){$month=$req->month;}
            if (!empty($req->year)){$year=$req->year;}

            return view ('payments', ['data' => Payments::with('serviceFunc', 'AttractionerFunc', 'sellerFunc', 'developmentFunc')
                ->where(function ($query) {
                    $currentuser = Auth::id();
                    $query
                        ->orWhere('nameOfAttractioner', $currentuser)
                        ->orWhere('nameOfSeller', $currentuser)
                        ->orWhere('directionDevelopment', $currentuser);
                })
                ->where($nameOfAttractioner, $req->nameOfAttractioner)
                ->where($nameOfSeller, $req->nameOfSeller)
                ->where($directionDevelopment, $req->directionDevelopment)
                ->where($calculation, $req->calculation)
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->get()], [
                    'months' => $months,
                    'month' => $month,
                    'datalawyers' => User::active()->get(),
                    'dataservices' => Services::all(),
                    'dataclients' => ClientsModel::all(),
                ]);
        }
    }

    /**
     * Summary of showPaymentById
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
            'dataservices' => Services::all(),
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
        $payment = Payments::find($id);
        $summ = $req->input('summ'); // поступивший платеж
        $payment->summ = $summ;

        $serviceid = $req->input('service');
        $payment->service = $serviceid;
        $service = DB::table('services')->find($serviceid);
        $price = $service->price;  // цена услуги стандартная

        if (!empty($req -> input('sellsumm'))) { // если взял предоплату
            $sellsumm = $req -> input('sellsumm');  // поступившая предоплата
            $payment->predoplatasumm = $sellsumm; // запись в БД
            $sootnoshnie = $sellsumm/$summ; // расчитываем соотношение цены за которую продали к предоплате
        } else {
            $sellsumm = $summ; // если предоплата не указана выполняется условия когда цена продажи равна сумме поступивших денег
            $sootnoshnie = 1; // соотношение не расчитывается
            $payment->predoplatasumm = $summ; // запись в БД
        }

        if ($price < $sellsumm) {
            $totalprice = $price / $sootnoshnie;
            $payment -> SallerSalary = $totalprice/100*13; // доход продавца от цены услуги (производной)
            $payment -> modifySeller = ($summ - $totalprice)/100*17; // увеличение дохода продавца от разницы в платеже минус цены услуги
            $payment -> AttaractionerSalary = $totalprice/100*20; // доход привлеченца
            $payment -> modifyAttraction = ($summ - $totalprice)/100*33; // увеличение дохода привлеченца
            $payment -> DeveloperSalary = $summ/100*17; // доход развивателя
        }

        if ($price > $sellsumm) {
            $payment -> SallerSalary = $summ/100*5;
            $payment -> AttaractionerSalary = $summ/100*10;
            $payment -> DeveloperSalary = $summ/100*10;
            $payment -> modifyAttraction = 0;
            $payment -> modifySeller = 0;
        }

        if ($price == $sellsumm) {
            $payment -> SallerSalary = $summ/100*13;
            $payment -> AttaractionerSalary = $summ/100*20;
            $payment -> DeveloperSalary = $summ/100*17;
            $payment -> modifyAttraction = 0;
            $payment -> modifySeller = 0;
        }

        $payment->calculation = $req->input('calculation');
        $payment->client = $req->client;
        $payment->nameOfAttractioner = $req->input('nameOfAttractioner');
        $payment->nameOfSeller = $req->input('nameOfSeller');
        $payment->directionDevelopment = $req->input('directionDevelopment');

        $payment->firmearning = ($summ - $payment->SallerSalary - $payment->AttaractionerSalary
            - $payment->DeveloperSalary - $payment->modifyAttraction - $payment->modifySeller);

        $payment->save();

        // Привязываем задачи
        $this->service->clearAssignTasks($payment);
        if ($req->has('taskID')) {
            $this->service->assignTasks($payment, $req->input('taskID'));
        }

        return redirect()->route('showPaymentById', $id)->with('success', 'Все в порядке, платеж обновлен');
    }

    public function PaymentDelete($id)
    {
        Payments::find($id)->delete();

        return redirect()->route('payments')->with('success', 'Все в порядке, платеж удален');
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
