@extends('layouts.app')

@section('title') Лид @endsection

@section('leftmenuone')
<li class="nav-item text-center p-3">
    <a class="text-white text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#editleadModal">Редактировать лид</a>
</li>
<li class="nav-item text-center p-3">
    <a class="text-white text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#modalleadtoclient">В клиента</a>
</li>
<li class="nav-item text-center p-3">
    <a class="text-white text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#modalleaddelete">В брак/спам</a>
</li>
<li class="nav-item text-center p-3">
    <a class="text-white text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#modalleadfail">В проваленные</a>
</li>
@endsection


@section('head')
<script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" href="/resources/datetimepicker/jquery.datetimepicker.css">
@endsection

@section('footerscript')
<script src="/resources/datetimepicker/jquery.datetimepicker.full.js"></script>
@endsection

@section('main')
<div class="row">
    <!-- карточка лида-->
    <div class="col-xl-6">
        <div class="card border-secondary">

            <div class="card-header d-flex justify-content-between">
                <p class="col-2">{{$data->name}}</p>
                <p>{{$data->status}}</p>

                <div class="col-2 mb-3">
                    <a class="btn btn-light w-100" href="#" data-bs-toggle="modal" data-bs-target="#editleadModal" data-toggle="tooltip" data-placement="top" title="Обработать">
                        <i class="bi-pen"></i>
                    </a>
                </div>

                <div class="col-2 mb-3">

                    <a class="btn w-100 btn-light nameToForm lead" href="#" data-bs-toggle="modal" data-bs-target="#taskringModal" data-toggle="tooltip" data-placement="top" title="{{ \App\Models\Enums\Tasks\Type::Ring->value }}" data-lead-id="{{ $data->id }}" data-user-id="{{ Auth::id() }}" data-type="{{ \App\Models\Enums\Tasks\Type::Ring->value }}">
                        <i class="bi-phone"></i>
                    </a>

                </div>

                <div class="col-2 mb-3">
                    <a class="btn w-100 btn-light nameToForm lead" href="#" data-bs-toggle="modal" data-bs-target="#taskconsModal" data-toggle="tooltip" data-placement="top" title="Консультация" data-lead-id="{{ $data->id }}" data-user-id="{{ Auth::id() }}" data-type="{{ \App\Models\Enums\Tasks\Type::Consultation->value }}">
                        <i class="bi-people"></i>
                    </a>

                </div>


                <div class="col-2 mb-3">
                    <a class="btn btn-light w-100" href="#" data-toggle="tooltip" data-placement="top" title="Перевести в клиента" data-bs-toggle="modal" data-bs-target="#modalleadtoclient">
                        <i class="bi-person-check"></i>
                    </a>
                </div>



            </div>

            <div class="card-body row mb-4 d-flex justify-content-center">
                <div class="row">
                    <h4 class="col-9">{{$data->phone}}</h4>
                </div>
                <div class="row mt-5">
                    <div class="col-4">
                        <h5>создан</h5>
                        <p>{{$data->created_at->format('d.m.Y в H:i')}}</p>
                    </div>
                    <div class="col-4">
                        @if ($data->updated_at !== null)
                        <h5>обработан</h5>
                        <p>{{$data->updated_at->format('d.m.Y в H:i')}}</p>
                        @else
                        <h5>не обработан</h5>
                        @endif
                    </div>
                    <div class="col-4">
                        <h5>Что предлагаем</h5>
                        <p>{{$data->servicesFunc->name}}</p>
                    </div>
                </div>
                <div class="row my-2">
                    <div class="col-3">Описание</div>
                    <div class="col-9">{{$data->description}}</div>
                </div>
                <div class="row my-2">
                    <div class="col-3">Что делаем с лидом</div>
                    <div class="col-9">{{$data->action}}</div>
                </div>
                <div class="row my-2">
                    <div class="col-3">Причина успеха</div>
                    <div class="col-9">{{$data->successreason}}</div>
                </div>
                <div class="row my-2">
                    <div class="col-3">Причина неудачи</div>
                    <div class="col-9">{{$data->failurereason}}</div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <h6>Источник</h6>
                        <p>{{$data->source}}</p>
                    </div>
                    <div class="col-4">
                        <h6>Привлек</h6>
                        <p>{{$data->userFunc->name}}</p>
                    </div>
                    <div class="col-4">
                        <h6>Ответсвенный</h6>
                        <p>{{$data->responsibleFunc->name}}</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="card-footer text-center">
            <div class="mt-3 row d-flex justify-content-center">
                <div class="mt-3 row d-flex justify-content-center">

                    <div class="col-2 mb-3">
                        <a class="btn btn-light w-100 @if ($data -> status == 'конвертирован') disabled @endif" href="#" data-toggle="tooltip" data-placement="top" title="Брак(спам)" data-bs-toggle="modal" data-bs-target="#modalleaddelete">
                            <i class="bi-trash"></i>
                        </a>
                    </div>

                    <div class="col-2 mb-3">
                        <a class="btn btn-light w-100 @if ($data -> status == 'конвертирован') disabled @endif" href="#" data-toggle="tooltip" data-placement="top" title="Провален" data-bs-toggle="modal" data-bs-target="#modalleadfail">
                            <i class="bi-bag-x"></i>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- карточка лида-->

    <!-- задачи по лиду-->
    <div class='col-xl-6'>
        <div class='card border-light'>
            <div class="card ">
                <div class="card-header text-center">
                    <p>Задачи <span>({{ $data->tasks->count() }})</p>
                </div>

                @foreach ($data->tasks as $task)
                <div class="mx-3 card-body d-flex justify-content-start">
                    <span class="text-start col-2">{{$task->created_at->month}} / {{$task->created_at->day}}</span>
                    <span class="text-center col-3">{{$task->status}}</span>
                    <a class="text-start col-4" href="/tasks/{{$task->id}}" target="_blank">{{$task->name}}</a>
                </div>
                @if($task->description !== null)
                <div class="mx-1 d-flex justify-content-start">
                    <div class="mx-3 mb-2 text-start"> <small class="text-muted">{{$task->description}}</small></div>
                </div>
                @endif

                @endforeach
            </div>
        </div>
    </div>
    <!-- задачи по лиду-->
</div>
@include('../inc/modal/leadsmodal/editlead')
@include('inc/modal/leadsmodal/leadtowork')
@include('inc/modal/leadsmodal/leadtoclient')
@include('inc/modal/leadsmodal/leaddelete')
@include('inc/modal/leadsmodal/modalleadfail')
@include('inc.modal.leadsmodal.add_task_ring')
@include('inc.modal.leadsmodal.add_task_cons')
@endsection