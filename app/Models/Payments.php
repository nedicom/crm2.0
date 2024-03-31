<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $id
 * @property int $summ
 * @property string $client
 * @property string $calculation счет, куда поступил платеж
 * @property int $service
 * @property int $nameOfAttractioner привлек клиента
 * @property int $modifyAttraction
 * @property int $nameOfSeller продал услугу
 * @property int $modifySeller
 * @property int $directionDevelopment
 * @property int $recruiting
 * @property int $companyAdmission
 * @property int $created_at
 * @property int $updated_at
 * @property int $SallerSalary
 * @property int $AttaractionerSalary
 * @property int $DeveloperSalary
 * @property int $clientid
 * @property int $predoplatasumm
 * @property int $firmearning
 * @property int $creator_id
 */
class Payments extends Model
{
    use HasFactory;

    /**
     * Обновление параметров
     * @param Request $request
     * @param int $summ
     * @return void
     */
    public function edit(Request $request, int $summ, int $serviceId, $new = false): void
    {
        $this->summ = $summ;
        if ($new) $this->creator_id = Auth::id();
        $this->service = $serviceId;
        $this->calculation = $request->input('calculation');
        $this->client = $request->input('client');
        if($request->input('clientidinput')) $this->clientid = $request->input('clientidinput');

        $this->nameOfAttractioner = $request->input('nameOfAttractioner');
        $this->nameOfSeller = $request->input('nameOfSeller');
        $this->directionDevelopment = $request->input('directionDevelopment');
        $this->firmearning = ($summ - $this->SallerSalary - $this->AttaractionerSalary
            - $this->DeveloperSalary - $this->modifyAttraction - $this->modifySeller);
    }

    /**
     * Рассчитываем значения параметров, если цена ниже предоплаты
     * @param int $priceService цена услуги
     * @param int $ratioPrice соотношение цены
     * @param int $summ сумма
     * @return void
     */
    public function calculateByPriceLessPrepayment(int $priceService, int $ratioPrice, int $summ): void
    {
        $totalprice = $priceService / $ratioPrice;
        $this->SallerSalary = $totalprice / 100 * 13; // доход продавца от цены услуги (производной)
        $this->modifySeller = ($summ - $totalprice) / 100 * 17; // увеличение дохода продавца от разницы в платеже минус цены услуги
        $this->AttaractionerSalary = $totalprice / 100 * 20; // доход привлеченца
        $this->modifyAttraction = ($summ - $totalprice) / 100 * 33; // увеличение дохода привлеченца
        $this->DeveloperSalary = $summ / 100 * 17; // доход развивателя
    }

    /**
     * Рассчитываем значения параметров, если цена больше предоплаты
     * @param int $summ
     * @return void
     */
    public function calculateByPriceLargerPrepayment(int $summ): void
    {
        $this->SallerSalary = $summ / 100 * 5;
        $this->AttaractionerSalary = $summ / 100 * 10;
        $this->DeveloperSalary = $summ / 100 * 10;
        $this->modifyAttraction = 0;
        $this->modifySeller = 0;
    }

    /**
     * Рассчитываем значения параметров, если цена равна предоплате
     * @param int $summ
     * @return void
     */
    public function calculateByPriceComparePrepayment(int $summ): void
    {
        $this->SallerSalary = $summ / 100 * 13;
        $this->AttaractionerSalary = $summ / 100 * 20;
        $this->DeveloperSalary = $summ / 100 * 17;
        $this->modifyAttraction = 0;
        $this->modifySeller = 0;
    }

    public function isPriceLessPrepayment(int $priceService, int $ratioPrice): bool
    {
        return $priceService < $ratioPrice;
    }

    public function isPriceLargerPrepayment(int $priceService, int $ratioPrice): bool
    {
        return $priceService > $ratioPrice;
    }

    public function isPriceComparePrepayment(int $priceService, int $ratioPrice): bool
    {
        return $priceService == $ratioPrice;
    }

    public function serviceFunc()
    {
        return $this->belongsTo(Services::class, 'service');
    }

    public function AttractionerFunc()
    {
        return $this->belongsTo(User::class, 'nameOfAttractioner');
    }

    public function sellerFunc()
    {
        return $this->belongsTo(User::class, 'nameOfSeller');
    }

    public function developmentFunc()
    {
        return $this->belongsTo(User::class, 'directionDevelopment');
    }

    public function tasks()
    {
        return $this->belongsToMany(Tasks::class, 'task_payment_assigns', 'payment_id', 'task_id');
    }
}
