@extends('layouts.app')

@section('head')
<script src="https://code.jquery.com/jquery-3.6.1.min.js"
    integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"
    integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" href="/resources/datetimepicker/jquery.datetimepicker.css">
@endsection

@section('footerscript')
<script src="/resources/datetimepicker/jquery.datetimepicker.full.js"></script>
<script>
    jQuery('#datetimepicker3').datetimepicker({
        format: 'Y-m-d',
        timepicker: false,
        lang: 'ru',
    });
    jQuery('#datetimepicker4').datetimepicker({
        format: 'Y-m-d',
        timepicker: false,
        lang: 'ru'
    });
</script>

@endsection

@section('title')
Лиды
@endsection

@section('leftmenuone')
<li class="nav-item text-center  p-3">
    <a class="text-white text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#leadsModal">Добавить
        лид</a>
</li>
<li class="nav-item text-center p-3">
    <a class="text-white text-decoration-none" href="#" data-bs-toggle="modal"
        data-bs-target="#SourcesModal">Источники лидов</a>
</li>
@endsection

@section('main')
<ul class="nav nav-pills justify-content-center mb-3 shadow p-1" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-allleads-tab" data-bs-toggle="pill" data-bs-target="#pills-allleads"
            type="button" role="tab" aria-controls="pills-allleads" aria-selected="true">Все<span
                class="badge @if($allleads->count() == 0) text-bg-danger @else text-bg-secondary @endif">{{ $allleads->count() }}</span></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pills-newleads-tab" data-bs-toggle="pill" data-bs-target="#pills-newleads"
            type="button" role="tab" aria-controls="pills-newleads" aria-selected="true">Новые <span
                class="badge @if($newleads->count() == 0) text-bg-danger @else text-bg-secondary @endif">{{ $newleads->count() }}</span></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-phoneleads-tab" data-bs-toggle="pill" data-bs-target="#pills-phoneleads"
            type="button" role="tab" aria-controls="pills-phoneleads" aria-selected="false">Дозвон <span
                class="badge @if($phoneleads->count() == 0) text-bg-danger @else text-bg-secondary @endif">{{ $phoneleads->count() }}</span></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-consleads-tab" data-bs-toggle="pill" data-bs-target="#pills-consleads"
            type="button" role="tab" aria-controls="pills-consleads" aria-selected="false">Консультация <span
                class="badge @if($consleads->count() == 0) text-bg-danger @else text-bg-secondary @endif">{{ $consleads->count() }}</span></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-defeatleads-tab" data-bs-toggle="pill" data-bs-target="#pills-defeatleads"
            type="button" role="tab" aria-controls="pills-defeatleads" aria-selected="false">Мусор <span
                class="badge @if($defeatleads->count() == 0) text-bg-danger @else text-bg-secondary @endif">{{ $defeatleads->count() }}</span></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-withoutcaseleads-tab" data-bs-toggle="pill"
            data-bs-target="#pills-withoutcaseleads" type="button" role="tab"
            aria-controls="pills-withoutcaseleads" aria-selected="false">Бездельники <span
                class="badge @if($withoutcaseleads->count() == 0) text-bg-danger @else text-bg-secondary @endif">{{ $withoutcaseleads->count() }}</span></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-winleads-tab" data-bs-toggle="pill" data-bs-target="#pills-winleads"
            type="button" role="tab" aria-controls="pills-winleads" aria-selected="false">Конвертирован<span
                class="badge @if($winleads->count() == 0) text-bg-danger @else text-bg-secondary @endif">{{ $winleads->count() }}</span></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-failleads-tab" data-bs-toggle="pill" data-bs-target="#pills-failleads"
            type="button" role="tab" aria-controls="pills-failleads" aria-selected="false">Провален<span
                class="badge @if($failleads->count() == 0) text-bg-danger @else text-bg-secondary @endif">{{ $failleads->count() }}</span></button>
    </li>
</ul>

<form class="row my-2" action="{{ route('leads') }}" method="get" autocomplete="off">

    @csrf

    <div class="row">
        <div class="col-md-2 col-12">
            <label for="findNumber"><small>поиск по номеру</small></label>
            <input type="text" class="form-control" value="{{ session('number') }}" name="findNumber" id="findNumber" placeholder="телефон">
        </div>
        <div class="col-md-2 col-12">
            <label for="findName"><small>поиск по имени</small></label>
            <input type="text" class="form-control" value="{{ session('name') }}" name="findName" id="findName" placeholder="ФИО (часть)">
        </div>

        <div class="col-md-2 col-12">
            <label for="datetimepicker3"><small>начало:</small></label>
            <input type="text" id="datetimepicker3" class="form-control" name="startdate" value="{{ request()->input('startdate') }}">
        </div>

        <div class="col-md-2 col-12">
            <label for="datetimepicker4"><small>конец:</small></label>
            <input id="datetimepicker4" class="form-control" name="enddate" type="text" value="{{ request()->input('enddate') }}">
        </div>

        <div class="col-md-2 col-12">
            <label for="submit"></label>
            <button type="submit" class="btn btn-primary form-control">найти</button>
        </div>
        <div class="col-md-2 col-12">
            <label for="reset"></label>
            <a type="reset" href="{{ route('leads') }}" class="btn btn-secondary form-control">сбросить</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2 col-12">
            <label for="lawyer"><small>кто привлек</small></label>
            <select class="form-select" name="lawyer" id="lawyer">
                <option value="">не выбрано</option>
                @foreach($datalawyers as $el)
                <option value={{$el->id}} @if ($el->id == session('lawyer')) selected @endif>
                    {{$el->name}}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 col-12">
            <label for="responsible"><small>кто ответственный</small></label>
            <select class="form-select" name="responsible" id="responsible">
                <option value="">не выбрано</option>
                @foreach($datalawyers as $el)
                <option value={{$el->id}} @if (($el->id) == request()->input('responsible'))) selected @endif>
                    {{$el->name}}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 col-12">
            {!! \App\Helpers\ClientHelper::typeList(request()->input('casettype')) !!}
        </div>

        <div class="col-md-2 col-12">
            <label for="lawyer"><small>город</small></label>
            <select class="form-select" name="city" id="city">
                <option value="">не выбрано</option>
                @foreach($cities as $el)
                <option value={{$el->id}} @if ($el->id == request()->input('city')) selected @endif>
                    {{$el->city}}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 col-12">
            <label for="button"><small>Уведомления telegram:</small></label>
            <a type="button" href="https://t.me/nedicomlead" class="btn btn-light form-control" target="_blank">В группу</a>
        </div>

    </div>
</form>

<div class="tab-content" id="pills-tabContent">

    <div class="tab-pane fade" id="pills-allleads" role="tabpanel" aria-labelledby="pills-allleads-tab">
        <p class="row font-weight-light p-3">Последние 200 лидов по хронологии для обозримости</p>
        <div class="row">
            @foreach ($allleads as $el)
            @include('leads/leadbadge')
            @endforeach
        </div>
    </div>

    <div class="tab-pane fade show active" id="pills-newleads" role="tabpanel" aria-labelledby="pills-newleads-tab">
        <p class="row font-weight-light p-3 ">Сюда поступают новые лиды. Нужно зайти в карточку лида и
            там редактировать его. Если не поставить звонок или консультацию лид попадает в бездельники </p>
        <div class="row">
            @foreach ($newleads as $el)
            @include('leads/leadbadge')
            @endforeach
        </div>
    </div>

    <div class="tab-pane fade" id="pills-consleads" role="tabpanel" aria-labelledby="pills-consleads-tab">
        <p class="row font-weight-light p-3">Тут лиды с задачей - консультация и любым статусом кроме
            "выполнена"</p>
        <div class="row">
            @foreach ($consleads as $el)
            @include('leads/leadbadge')
            @endforeach
        </div>
    </div>

    <div class="tab-pane fade" id="pills-phoneleads" role="tabpanel" aria-labelledby="pills-phoneleads-tab">
        <p class="row font-weight-light p-3">Тут лиды с задачей - Звонок и любым статусом кроме
            "выполнена"</p>
        <div class="row">
            @foreach ($phoneleads as $el)
            @include('leads/leadbadge')
            @endforeach
        </div>
    </div>

    <div class="tab-pane fade" id="pills-defeatleads" role="tabpanel" aria-labelledby="pills-defeatleads-tab">
        <p class="row font-weight-light p-3">Тут мусорные лиды - дубли, спам звонки, ошиблись номером и
            так далее за последние 3 месяца</p>
        <div class="row">
            @foreach ($defeatleads as $el)
            @include('leads/leadbadge')
            @endforeach
        </div>
    </div>


    <div class="tab-pane fade" id="pills-withoutcaseleads" role="tabpanel"
        aria-labelledby="pills-withoutcaseleads-tab">
        <p class="row font-weight-light p-3" style="font-size:12px">Тут лиды без консультаций, звонков
            - с которыми мы не знаем что делать (мне просто спросить, далеко ехать, дорого и так далее) за последние 2
            месяца. Причину обязательно указать в описании. </p>
        <div class="row">
            @foreach ($withoutcaseleads as $el)
            @include('leads/leadbadge')
            @endforeach
        </div>
    </div>

    <div class="tab-pane fade" id="pills-winleads" role="tabpanel" aria-labelledby="pills-winleads-tab">
        <p class="row font-weight-light p-3">Тут результат работы отдела продаж
        </p>
        <div class="row">
            @foreach ($winleads as $el)
            @include('leads/leadbadge')
            @endforeach
        </div>
    </div>

    <div class="tab-pane fade" id="pills-failleads" role="tabpanel" aria-labelledby="pills-failleads-tab">
        <p class="row font-weight-light p-3">Тут лиды, которые могли стать клиентами, но не стали</p>
        <div class="row">
            @foreach ($failleads as $el)
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