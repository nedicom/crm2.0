@extends('layouts.app')

@section('head')
    <link rel="stylesheet" type="text/css" href="/resources/datetimepicker/jquery.datetimepicker.css">
@endsection

@section('footerscript')
    <script src="/resources/datetimepicker/jquery.datetimepicker.full.js"></script>
@endsection

@section('title')
    Задача
@endsection

@section('leftmenuone')
    <li class="nav-item text-center p-3">
        <a class="text-white text-decoration-none nameToForm edit @if ($data->clientsModel) clientTask @endif" href="#" @if ($data->clientsModel) data-client="{{ $data->clientsModel->name }}" data-value-id="{{ $data->clientsModel->id }}" @endif
        data-task-id="{{ $data->id }}" data-bs-toggle="modal" data-bs-target="#edittaskModal" data-type="{{ \App\Models\Enums\Tasks\Type::Task->value }}">
            Изменить задачу
        </a>
    </li>
@endsection

@section('main')
    <div class="col-md-6 text-center">
        @php
            $weekMap = [0 => 'Понедельник', 1 => 'Вторник', 2 => 'Среда', 3 => 'Четерг', 4 => 'Пятница', 5 => 'Суббота', 6 => 'Воскресенье']
        @endphp
        <div class="card border-light">
            <div class="card-body">
                <h5 class="card-title text-truncate">{{$data->name}}</h5>
                <h6>
                    @foreach($datalawyers as $ellawyer)
                        @if ($ellawyer->id == $data->lawyer) {{$ellawyer->name}} @endif
                    @endforeach
                </h6>
                <p class="text-truncate">начало: {{$data['date']['value'] }}</p>
                <p class="text-truncate">статус: {{$data->status}}</p>
                <p class="">{{$data->description}}</p>

                <p class="text-truncate"><a href="/clients/{{$data->clientid}}">{{$data->client}}</a></p>
                <div class="mt-3 row d-flex justify-content-center">
                <div class="col-6 mb-3">               
                    <div class="col-2 mb-3">
                        <a class="btn btn-light w-100" href="{{ route ('TaskDelete', $data->id) }}" data-toggle="tooltip" data-placement="top" title="Удалить">
                            <i class="bi-trash"></i>
                        </a>
                    </div>
                    </div>
                    @if ($data->hrftodcm)
                        <div class="col-2 mb-3">
                            <a href="{{$data->hrftodcm}}"class="btn btn-light w-100" target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="Blue" class="bi bi-hdd" viewBox="0 0 16 16">
                                    <path d="M4.5 11a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zM3 10.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"></path>
                                    <path d="M16 11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V9.51c0-.418.105-.83.305-1.197l2.472-4.531A1.5 1.5 0 0 1 4.094 3h7.812a1.5 1.5 0 0 1 1.317.782l2.472 4.53c.2.368.305.78.305 1.198V11zM3.655 4.26 1.592 8.043C1.724 8.014 1.86 8 2 8h12c.14 0 .276.014.408.042L12.345 4.26a.5.5 0 0 0-.439-.26H4.094a.5.5 0 0 0-.44.26zM1 10v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1z"></path>
                                </svg>
                            </a>
                        </div>
                    @endif
                    @if ($data->clientsModel)
                        <div class="col-2 mb-3">
                            <a class="btn btn-light w-100 nameToForm edit @if ($data->clientsModel) clientTask @endif" href="#" data-client="{{ $data->clientsModel->name }}" data-value-id="{{ $data->clientsModel->id }}" data-task-id="{{ $data->id }}"
                                data-bs-toggle="modal" data-bs-target="#edittaskModal" data-type="{{ \App\Models\Enums\Tasks\Type::Task->value }}">
                                <i class="bi-three-dots"></i>
                            </a>
                        </div>
                    @endif
                    <div class="col-2 mb-3">
                        <a class="btn btn-light w-100" href="{{ route ('TaskComplete', $data->id) }}" data-toggle="tooltip" data-placement="top" title="Выполнить" 
                            @if ($data->status == 'выполнена') disabled @endif>
                           <i class="bi-check-circle"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
