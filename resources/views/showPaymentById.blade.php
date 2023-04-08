@extends('layouts.app')

@section('title')
  платеж
@endsection

@section('leftmenuone')
  <li class="nav-item text-center p-3">
    <a class="text-white text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#editpaymentModal">Редактировать платеж</a>
  </li>
@endsection

@section('main')

<h2 class="px-3">Платеж</h2>

  <div class="col-md-6 my-3 mx-3">
        <div class="card border-light">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                    <th scope="col">{{$data -> created_at}}</th>
                    <th scope="col"><h5 class="mb-2 text-muted text-truncate">{{$data -> client}}</h5></th>
                    <th scope="col"></th>
                </tr>
              </thead>

              <tbody>
                <tr>
                  <td>оплачено</td>
                  <td><span class="badge py-3 px-4
                  @if ($data -> calculation == 'ГЕНБАНК') bg-primary
                  @elseif ($data -> calculation == 'РНКБ') bg-info
                  @elseif ($data -> calculation == 'НАЛИЧНЫЕ') bg-secondary
                  @elseif ($data -> calculation == 'СБЕР') bg-success
                  @else bg-light
                  @endif">{{$data -> calculation}}</span>
                  </td>
                  <td><span class="badge  bg-primary py-3 px-4">{{$data->summ}}</span></td>
                  <td><span class="badge  bg-primary py-3 px-4"></span></td>
                </tr>


                <tr>
                  <td>за сколько продана услуга</td>
                  <td><span></span></td>
                  <td><span class="badge bg-primary py-3 px-4">{{$data -> predoplatasumm}}</span></td>
                  <td></td>
                </tr>


                <tr>
                  <td> услуга</td>
                  <td><span>{{$data -> serviceFunc -> name}}</span></td>
                  <td><span class="badge bg-primary py-3 px-4">{{$data -> serviceFunc -> price}}</span></td>
                  <td></td>
                </tr>

                <tr>
                  <td>привлек</td>
                  <td><p class="mt-3 fw-semibold text-muted">{{$data -> AttractionerFunc -> name}}</p></td>
                  <td><span class="badge bg-success py-3 px-4">{{$data -> AttaractionerSalary}}</span></td>
                  <td><span class="badge bg-success py-3 px-4">{{$data -> modifyAttraction}}</span></td>
                </tr>

                <tr>
                  <td>продал</td>
                  <td><p class="mt-3 fw-semibold text-muted">{{$data -> sellerFunc -> name}}</p></td>
                  <td><span class="badge bg-success py-3 px-4">{{$data -> SallerSalary}}</span></td>
                  <td><span class="badge bg-success py-3 px-4">{{$data -> modifySeller}}</span></td>
                </tr>

                <tr>
                  <td>развил направление</td>
                  <td><p class="mt-3 fw-semibold text-muted">{{$data -> developmentFunc -> name}}</p></td>
                  <td><span class="badge bg-success py-3 px-4">{{$data -> DeveloperSalary}}</span></td>
                </tr>

              </tbody>
            </table>
          </div>
          <div class="text-center">
            <div class="mt-3 row d-flex justify-content-center">
                <div class="mt-3 row d-flex justify-content-center">
                    <div class="col-2 mb-3">
                      <a class="btn btn-light w-100" href="#" data-bs-toggle="modal" data-bs-target="#editpaymentModal">
                      <i class="bi-pen"></i></a>
                    </div>
                    <div class="col-2 mb-3">
                      <a class="btn btn-light w-100" href="{{ route ('PaymentDelete', $data->id) }}">
                      <i class="bi-trash"></i></a>
                    </div>
                  </div>
            </div>
          </div>

        </div>

@endsection
