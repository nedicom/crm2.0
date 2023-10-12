  @extends('layouts.app')


  @section('title')
    платежи
  @endsection

    @section('leftmenuone')
      <li class="nav-item text-center p-3">
        <a class="text-white text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#paymentModal">Добавить платеж</a>
      </li>
    @endsection

@section('main')

    <div class="row mb-4">
    <div class="col-2">
      <div class="">
        <h4 class="text-uppercase">Платежи<i class="bi bi-credit-card text-info mx-2"></i></h4>
        <h5></h5>
      </div>
    </div>

    <div class="col-10 row">
       <div class="col-4">
       <div class="card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <h4><i class="bi bi-clipboard2-check" style="color: blue"></i></h4>
              <p class="mb-1"> Цена продажи равна цене услуги </p>
            </div>

            <table class="mt-2 text-center w-100">
              <thead class="">
                <th>Привлечение</th>
                <th>Продажа</th>
                <th>Развитие</th>
              </thead>
              <tbody class="fs-5">
                <tr>
                  <td>20 %</td>
                  <td>13 %</td>
                  <td>17 %</td>
                </tr>
                <tr style="font-size: .8rem !important;">
                <td colspan="3">от общей стоимости</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-4">
       <div class="card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <h4><i class="bi bi-clipboard2-plus" style="color: green"></i></h4>
              <p class="mb-1">Цена продажи больше цены услуги</p>
            </div>

            <table class="mt-2 text-center w-100">
              <thead class="">
                <th>Привлечение</th>
                <th>Продажа</th>
                <th>Развитие</th>
              </thead>
              <tbody class="fs-5">
                <tr>
                  <td>20 % + 33 %</td>
                  <td>13 % + 17 %</td>
                  <td>17%</td>
                </tr>
                <tr style="font-size: .8rem !important;">
                  <td colspan="2">от общей стоимости + от размера превышение</td>
                  <td>от общей стоимости</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>


      <div class="col-4">
       <div class="card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <h4><i class="bi bi-clipboard-x" style="color: red"></i></h4>
              <p class="mb-1">Цена продажи меньше цены услуги</p>
            </div>

            <table class="mt-2 text-center w-100">
              <thead class="">
                <th>Привлечение</th>
                <th>Продажа</th>
                <th>Развитие</th>
              </thead>
              <tbody class="fs-5">
                <tr>
                  <td>10 %</td>
                  <td>5 %</td>
                  <td>10 %</td>
                </tr>
                <tr style="font-size: .8rem !important;">
                <td colspan="3">от общей стоимости</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>


      </div>
    </div>

    <div class="row">
        <div class="col-12">
            <table class="table table-hover table-borderless align-middle caption-top" style="font-size: 12px; table-layout: fixed;">
                <thead class="fw-bold text-center">
                  <form action="{{route('payments')}}" method="get">
                  @csrf


                  <div class="d-flex">

                  <div class="d-flex">
                    <div class="px-3">
                        <select class="form-select form-select-sm" name="year">
                          <option value="2022">2022</option>
                          <option value="2023" selected>2023</option>
                        </select>
                    </div>

                    <div class="px-3">
                        <select class="form-select form-select-sm" name="month">
                          @foreach($months as $number => $name)
                            <option value="{{$number}}" @if ($number == (request()->get('month'))) selected @endif
                            @if ( request()->get('month') == '' && $number == $month ) selected @endif
                             >{{$name}}
                            </option>
                          @endforeach
                        </select>
                    </div>

                    <div class="px-3">
                        <select class="form-select form-select-sm" name="calculation">
                        <option value="">куда поступили</option>
                          <option value="РНКБ" @if ("РНКБ" == (request()->get('calculation'))) selected @endif>РНКБ</option>
                          <option value="СБЕР" @if ("СБЕР" == (request()->get('calculation'))) selected @endif>СБЕР</option>
                          <option value="ГЕНБАНК" @if ("ГЕНБАНК" == (request()->get('calculation'))) selected @endif>ГЕНБАНК</option>
                          <option value="НАЛИЧНЫЕ" @if ("НАЛИЧНЫЕ" == (request()->get('calculation'))) selected @endif>НАЛИЧНЫЕ</option>
                        </select>
                    </div>

                    <div class="px-3">
                        <select class="form-select form-select-sm" name="nameOfAttractioner">
                        <option value="">привлек</option>
                          @foreach($datalawyers as $el)
                          <option value="{{$el -> id}}" @if ($el -> id == (request()->get('nameOfAttractioner'))) selected @endif>{{$el -> name}}</option>
                          @endforeach
                        </select>
                    </div>

                    <div class="px-3">
                        <select class="form-select form-select-sm" name="nameOfSeller">
                        <option value="">продал</option>
                          @foreach($datalawyers as $el)
                          <option value="{{$el -> id}}" @if ($el -> id == (request()->get('nameOfSeller'))) selected @endif>{{$el -> name}}</option>
                          @endforeach
                        </select>
                    </div>

                    <div class="px-3">
                        <select class="form-select form-select-sm" name="directionDevelopment" id="directionDevelopment">
                          <option value="">направление</option>
                          @foreach($datalawyers as $el)
                          <option value="{{$el -> id}}" @if ($el -> id == (request()->get('directionDevelopment'))) selected @endif>{{$el -> name}}</option>
                          @endforeach
                        </select>
                    </div>

                  <div class="d-flex px-3">
                        <button type="submit" class="mx-1 btn btn-primary  btn-sm">Применить</button></form>
                        <a class="mx-1 btn btn-secondary btn-sm" href="payments" role="button">сбросить</a>
                  </div>

                  </div>


                <tr>
                    <th style="width: 2%">№</th>
                    <th scope="col">дата</th>
                    <th style="width: 10%">Клиент</th>
                    <th style="width: 8%">Услуга</th>
                    <th scope="col">Цена услуги</th>
                    <th scope="col">Оплачено</th>
                    <th scope="col">Куда поступили</th>
                    <th scope="col">

                    </th>
                    <th scope="col">Оплата + повышение</th>
                    <th scope="col">
                    </th>
                    <th scope="col">Оплата + повышение</th>
                    <th scope="col">
                    </th>
                    <th scope="col">Оплата</th>
                    @if (auth()->user()->role == 'admin')
                      <th scope="col">Доход компании</th>
                    @endif
                    <th style="width: 4%"></th>
                </tr>
                </thead>

                <tbody class="fw-light text-center">
                  @php
                    $total = 0; $totalattr = 0; $totalattrup = 0; $totalsell = 0; $totalsellup = 0; $totaldirect = 0; $totalfirmearning=0;
                    $number = 1;
                  @endphp

                  @foreach ($data as $el)
                      <tr>
                          <td>{{$number}}</td>
                          <td>{{$el -> created_at->format('j / m')}}</td>
                          <td class = "text-truncate" data-bs-toggle="tooltip" data-bs-title="{{$el -> client}}">
                            <a href = "clients/{{$el -> clientid}}" target="_blank">{{$el -> client}} </a>
                          </td>
                          @if (!empty($el->serviceFunc->name))
                              <td class="text-truncate" data-bs-toggle="tooltip" data-bs-title="{{ $el->serviceFunc->name }}">
                                {{ $el->serviceFunc->name }}
                              </td>
                          @endif
                          <td class="text-center">{{$el -> serviceFunc -> price}}</td>
                          <td class="fw-bold text-center">{{$el -> summ}}</td>
                          <td>
                          <span class="badge py-1 px-1
                            @if ($el -> calculation == 'ГЕНБАНК') bg-primary
                            @elseif ($el -> calculation == 'РНКБ') bg-info
                            @elseif ($el -> calculation == 'НАЛИЧНЫЕ') bg-secondary
                            @elseif ($el -> calculation == 'СБЕР') bg-success
                            @else bg-light
                            @endif
                          ">{{$el -> calculation}}</span></td>
                          <td>{{$el -> AttractionerFunc -> name}}</td>
                          <td class="fw-bold text-center">{{$el -> AttaractionerSalary}} + {{$el -> modifyAttraction}}</td>

                          <td>{{$el -> sellerFunc -> name}}</td>
                          <td class="fw-bold text-center">{{$el -> SallerSalary}} + {{$el -> modifySeller}}</td>
                          <td>{{$el -> developmentFunc -> name}}</td>
                          <td class="fw-bold text-center">{{$el -> DeveloperSalary}}</td>
                          @if (auth()->user()->role == 'admin')
                            <th scope="col">{{$el -> firmearning}}</th>
                          @endif
                          <td>
                            <a class="btn btn-light w-100" href="{{ route ('showPaymentById', $el->id) }}">
                              <i class="bi-three-dots"></i></a>
                          </td>
                      </tr>

                      @php
                        $number++;
                        $total = $total + ($el -> summ);
                        $totalattr= $totalattr + ($el -> AttaractionerSalary); $totalattrup = $totalattrup + ($el -> modifyAttraction);
                        $totalsell = $totalsell + ($el -> SallerSalary); $totalsellup = $totalsellup + ($el -> modifySeller);
                        $totaldirect = $totaldirect + ($el -> DeveloperSalary); $totalfirmearning = $totalfirmearning + ($el -> firmearning);
                      @endphp
                  @endforeach
                  <tfoot class="border-top">
                    <tr>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td class="fw-bold text-center">
                      {{$totalattr}} + {{$totalattrup}}</td>
                      <td></td>
                      <td class="fw-bold text-center">{{$totalsell}} + {{$totalsellup}}</td>
                      <td></td>
                      <td class="fw-bold text-center"></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td class="fw-bold text-center fs-6">итого:</td>
                      <td></td>
                      <td class="fw-bold text-center">{{$total}}</td>
                      <td></td>
                      <td></td>
                      @php
                      $totalattrall = $totalattr + $totalattrup; $totalsellrall = $totalsell + $totalsellup;
                      @endphp
                      <td class="fw-bold text-center">привлечение:</br></br>{{$totalattrall}}</td>
                      <td></td>
                      <td class="fw-bold text-center">продажа:</br></br>{{$totalsellrall}}</td>
                      <td></td>
                      <td class="fw-bold text-center">развитие:</br></br>{{$totaldirect}}</td>
                                      @if (auth()->user()->role == 'admin')
                                      <td class="fw-bold text-center">{{$totalfirmearning}}</td>
                                      @endif
                      <td></td>
                    </tr>
                  </tfoot>
                  </tbody>
            </table>

                                        <!-- end table-responsive -->



        </div> <!-- end col -->
    </div>

    <script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>

    {{-- end views for all payments--}}

@endsection
