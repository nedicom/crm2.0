@extends('layouts.app')

@section('head')
@endsection

@section('title')
    Договора
@endsection

@section('leftmenuone')
    <li class="nav-item text-center p-3">
        <a class="text-white text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#editdogovorModal">Изменить договор</a>
    </li>
@endsection

@section('main')
    <h2 class="px-3">Договор</h2>

    <div class="col-md-6 my-3 mx-3">
        <div class="card border-light">
            <div class="card-header d-flex justify-content-between">
                <h2 class="h3 mb-0 col-8">Инфорация о договоре</h2>
            </div>
            <div class="card-body row mb-4 d-flex justify-content-center">
                <div class="row">
                    <div class="col-3">Название</div>
                    <h4 class="col-9">{{$data -> name}}</h4>
                </div>
                <div class="row mt-5">
                    <div class="col-4">
                        <h5>заключен</h5>
                        <p>{{$data -> created_at}}</p>
                    </div>
                </div>
                <div class="row my-2">
                    <div class="col-3">Описание</div>
                    <div class="col-9">{{$data -> subject}}</div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <h6>Клиент</h6>
                        <p>{{$data -> clientFunc -> name}}</p>
                    </div>
                    <div class="col-4">
                        <h6>Ответсвенный</h6>
                        <p>{{$data -> userFunc -> name}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
