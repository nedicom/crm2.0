@extends('layouts.app')

@section('head')
<script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" href="/resources/datetimepicker/jquery.datetimepicker.css">
@endsection

@section('footerscript')
<script src="/resources/datetimepicker/jquery.datetimepicker.full.js"></script>
@endsection

@section('title') Лиды @endsection

@section('leftmenuone')
<li class="nav-item text-center  p-3">
    <a class="text-white text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#leadsModal">Добавить лид</a>
</li>
<li class="nav-item text-center p-3">
    <a class="text-white text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#SourcesModal">Источники лидов</a>
</li>
@endsection

@section('main')

<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pills-newleads-tab" data-bs-toggle="pill" data-bs-target="#pills-newleads" type="button" role="tab" aria-controls="pills-newleads" aria-selected="true">Новые <span class="badge text-bg-secondary">{{$newleads->count()}}</span></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-phoneleads-tab" data-bs-toggle="pill" data-bs-target="#pills-phoneleads" type="button" role="tab" aria-controls="pills-phoneleads" aria-selected="false">Дозвон <span class="badge text-bg-secondary">{{$phoneleads->count()}}</span></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-consleads-tab" data-bs-toggle="pill" data-bs-target="#pills-consleads" type="button" role="tab" aria-controls="pills-consleads" aria-selected="false">Консультация <span class="badge text-bg-secondary">{{$consleads->count()}}</span></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-defeatleads-tab" data-bs-toggle="pill" data-bs-target="#pills-defeatleads" type="button" role="tab" aria-controls="pills-defeatleads" aria-selected="false">Мусор <span class="badge text-bg-secondary">{{$defeatleads->count()}}</span></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-withoutcaseleads-tab" data-bs-toggle="pill" data-bs-target="#pills-withoutcaseleads" type="button" role="tab" aria-controls="pills-withoutcaseleads" aria-selected="false">Бездельники <span class="badge text-bg-secondary">{{$withoutcaseleads->count()}}</span></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-winleads-tab" data-bs-toggle="pill" data-bs-target="#pills-winleads" type="button" role="tab" aria-controls="pills-winleads" aria-selected="false">Конвертирован<span class="badge text-bg-secondary">{{$winleads->count()}}</span></button>
    </li>
</ul>


<div class="tab-content" id="pills-tabContent">

    <div class="tab-pane fade show active" id="pills-newleads" role="tabpanel" aria-labelledby="pills-newleads-tab">          
        <p class="row font-weight-light bg-white">Сюда поступают новые лиды. Нужно зайти в карточку лида и там редактировать его. Если не поставить звонок или консультацию лид попадает в бездельники </p>
        <div class="row">            
            @foreach ($newleads as $el)
            @include('leads/leadbadge')
            @endforeach
        </div>
    </div>

    <div class="tab-pane fade" id="pills-consleads" role="tabpanel" aria-labelledby="pills-consleads-tab">
    <p class="row font-weight-light bg-white">Тут лиды с задачей - звонок и любым статусом кроме "выполнена"</p>
        <div class="row">
            @foreach ($consleads as $el)
            @include('leads/leadbadge')
            @endforeach
        </div>
    </div>

    <div class="tab-pane fade" id="pills-phoneleads" role="tabpanel" aria-labelledby="pills-phoneleads-tab">
    <p class="row font-weight-light bg-white">Тут лиды с задачей - консультация и любым статусом кроме "выполнена"</p>
        <div class="row"> @foreach ($phoneleads as $el)
            @include('leads/leadbadge')
            @endforeach
        </div>
    </div>

    <div class="tab-pane fade" id="pills-defeatleads" role="tabpanel" aria-labelledby="pills-defeatleads-tab">
    <p class="row font-weight-light bg-white">Тут мусорные лиды - дубли, спам звонки, ошиблись номером и так далее за последние 2 месяца</p>
        <div class="row">@foreach ($defeatleads as $el)
            @include('leads/leadbadge')
            @endforeach
        </div>
    </div>


    <div class="tab-pane fade" id="pills-withoutcaseleads" role="tabpanel" aria-labelledby="pills-withoutcaseleads-tab">
    <p class="row font-weight-light bg-white">Тут лиды без консультаций, звонков - с которыми мы не знаем что делать (мне просто спросить, далеко ехать, дорого и так далее) за последние 2 месяца. Причину обязательно указать в описании. </p>
        <div class="row">@foreach ($withoutcaseleads as $el)
            @include('leads/leadbadge')
            @endforeach
        </div>
    </div>

    <div class="tab-pane fade" id="pills-winleads" role="tabpanel" aria-labelledby="pills-winleads-tab">
    <p class="row font-weight-light bg-white">Тут результат работы отдела продаж за последние 2 месяца</p>
        <div class="row">@foreach ($winleads as $el)
            @include('leads/leadbadge')
            @endforeach
        </div>
    </div>
</div>



<!-- Модальные окна -->
@include('inc./modal/leadsmodal/addlead')
@include('inc./modal/leadsmodal/sources')

<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>
@endsection