@extends('layouts.app')

@section('title') Клиент @endsection

@section('head')
<script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" href="/resources/datetimepicker/jquery.datetimepicker.css">
@endsection

@section('footerscript')
<script src="/resources/datetimepicker/jquery.datetimepicker.full.js"></script>
@endsection

@section('leftmenuone')
<li class="nav-item text-center p-3">
    <a class="text-white text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#editModal">Редактировать клиента</a>
</li>
@endsection

@section('main')
<h2 class="px-3">Клиент</h2>
<div class='col-md-6 col-xxl-3 my-3'>
    <div class='card border-light'>
        <div class='d-inline-flex justify-content-end px-2'>
            @if ($data->status == 1) <i class="bi bi-circle-fill" style="color: #0acf97;"></i> @else <i class="bi bi-circle-fill text-secondary"></i> @endif
        </div>
        <div class="text-center">
            <h5 class="mb-2 px-3 text-muted">{{$data->name}}</h5>
            <p class="mb-0 text-muted">{{$data->phone}}</p>
            <p class="mb-0 text-muted">идентификатор - {{$data->id}}</p>
            <p class="mb-0 text-muted">{{$data->email}}</p>
            <p class="mb-0 text-muted">закреплен за: </br>{{$data->userFunc->name}}</p>
            <p class="mb-0 text-muted">Код telegram: @if (auth()->user()->role == 'admin' || auth()->user()->id == $data->lawyer) {{$data->tgid}} @else скрыто @endif</p>
            <hr class="bg-dark-lighten my-3">
            <div class="mt-3 px-3 row d-flex justify-content-center">
                <div class="col-4 mb-3">
                    <a class="btn btn-light w-100" href="#" data-bs-toggle="modal" data-bs-target="#editModal">
                        <i class="bi-pen"></i>
                    </a>
                </div>
                <div class="col-4 mb-3">
                    <a class="btn btn-light w-100 nameToForm" href="#" data-bs-toggle="modal" data-bs-target="#taskModal" data-client="{{$data->name}}" data-value-id="{{$data->id}}" data-user-id="{{ Auth::id() }}" data-type="{{ \App\Models\Enums\Tasks\Type::Task->value }}">
                        <i class="bi-clipboard-plus"></i>
                    </a>
                </div>
                <div class="px-1 col-4">
                    <a class="btn btn-light w-100 addDeal" href="#" data-client-id="{{$data->id}}" data-bs-toggle="modal" data-bs-target="#dealModal" title="Добавить дело">
                        <i class="bi-clipboard-plus"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class='col-md-9 col-xxl-9 my-3'>
    <div class='card border-light'>
        <div class="text-center">
            <h6 class="mb-2 px-3 text-muted">Задачи не закрепленные к делам<span>({{ $data->tasksFunc->count() }})</span></h6>
            <hr class="bg-dark-lighten my-3">
            <div class="mx-3 row">
                <p class="mt-3 px-1 text-center col-2">поставлена</p>
                <p class="mt-3 px-1 text-center col-2">статус</p>
                <p class="mt-3 px-1 text-center col-1">оплачена
                </p>
                <p class="mt-3 px-1 text-start col-6">название</p>
                <p class="mt-3 px-1 text-start col-1">юрист</p>
            </div>
            @foreach (($data->tasksFunc) as $task)
            <div class="mx-3 row">
                <p class="mt-3 px-1 text-center col-2">{{$task->created_at->format('d.m.Y')}}</p>
                <p class="mt-3 px-1 text-center col-2">{{$task->status}}</p>
                <p class="mt-3 px-1 text-center col-1">
                    @foreach (($data->paymsThroughTask) as $paytask)
                    @if($task->id == $paytask->task_id)
                    <i class="bi bi-clipboard-heart" style="font-size: 2rem; color: cornflowerblue;" data-bs-toggle="tooltip" data-bs-title="оплачена"></i>
                    @endif
                    @endforeach
                </p>
                <p class="mt-3 px-1 text-start col-6">
                <a  href="/tasks/{{$task->id}}" target="_blank">{{$task->name}}</a>
                <p class="mt-3 px-1 text-start col-1">
                    <span class="mt-3 text-end">
                        <img src="{{$task->performer->avatar}}" class="rounded-circle" style="width:30px;height:30px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{$task->performer->name}}">
                    </span>
                    
                </p>
                </p>
            </div>
            @endforeach

            @if ($countTaskD = $data->tasksFunc()->whereNotNull('deal_id')->count() > 0)
            <hr class="bg-dark-lighten my-3">
            <h6 style="text-align: center;">Задачи закрепленные к делам<span>({{ $countTaskD }})</span></h6>
            <div class="">
                @foreach ($data->tasksFunc()->whereNotNull('deal_id')->get() as $task)
                <div class="mx-3 row">

                    <p class="mt-3 px-1 text-center col-2">{{$task->created_at->format('d.m.Y')}}</p>

                    <p class="mt-3 px-1 text-center col-2">{{$task->status}}</p>

                    <p class="mt-3 px-1 text-center col-1">
                        @foreach (($data->paymsThroughTask) as $paytask)
                            @if($task->id == $paytask->task_id)
                            <i class="bi bi-clipboard-heart" style="font-size: 2rem; color: cornflowerblue;" data-bs-toggle="tooltip" data-bs-title="оплачена"></i>
                            @endif
                        @endforeach
                    </p>

                    <p class="mt-3 px-1 text-start col-6">
                        <a href="/tasks/{{$task->id}}" target="_blank">{{$task->name}}</a> |
                            <a href="{{ route("deal.show", ['id' => $task->deal->id]) }}" target="_blank">{{ $task->deal->name }}</a>

                    </p>

                    <p class="mt-3 px-1 text-center col-1">
                        <span class="mt-3 text-end">
                            <img src="{{ url($task->deal->user->avatar) }}" class="rounded-circle" style="width:30px;height:30px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $task->deal->user->name }}">
                        </span>
                    </p>

                </div>
                @endforeach
            </div>
            <hr class="bg-dark-lighten my-3">
            @endif
            <h6 class="mb-2 px-3 text-muted">платежи <span>({{$data -> serviceFunc -> count()}})</span></h6>
            <hr class="bg-dark-lighten my-3">
            <div class="row">
                <div class="col-8">
                    @foreach(($data -> serviceFunc) as $payments)
                    <div class="mx-3 d-flex justify-content-end">
                        <p class="mt-3 mx-3 text-start">{{$payments->name}}</p>
                    </div>
                    @endforeach
                </div>
                <div class="col-4">
                    @foreach(($data -> paymentsFunc) as $payment)
                    <div class="mx-3 d-flex justify-content-end">
                        <p class="mt-3 mx-3 text-start">{{$payment->created_at->month}} / {{$payment->created_at->day}}</p>
                        <p class="mt-3 mx-3 text-center col-2">{{$payment->summ}}</p>
                        <a class="mt-3 mx-3 text-start" href="/payments/{{$payment->id}}" target="_blank">{{$payment->name}}</a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальные окна -->
@include('inc/modal/addtask')
@include('inc/modal/add-deal')

<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>
@endsection