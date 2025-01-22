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
<ul class="justify-content-center mb-3 shadow p-1">
    <a class="link-offset-2 link-underline link-opacity-75-hover @if($route == 'all') link-underline-opacity-100 @else link-underline-opacity-0 @endif" href="/filterleads/all">Все<span
            class="badge m-1 @if($allleadscount == '0') text-bg-danger @else text-bg-secondary @endif">{{ $allleadscount }}</span></a>

    <a class="link-offset-2 link-underline link-opacity-75-hover  @if($route == 'new') link-underline-opacity-100 @else  link-underline-opacity-0 @endif" href="/filterleads/new">Новые<span
            class="badge m-1 @if($newleadscount == '0') text-bg-danger @else text-bg-secondary @endif">{{ $newleadscount }}</span></a>

    <a class="link-offset-2 link-underline link-opacity-75-hover  @if($route == 'phone') link-underline-opacity-100 @else  link-underline-opacity-0 @endif" href="/filterleads/phone">Дозвон<span
            class="badge m-1 @if($phoneleads == '0') text-bg-danger @else text-bg-secondary @endif">{{ $phoneleads }}</span></a>

    <a class="link-offset-2 link-underline link-opacity-75-hover  @if($route == 'consleads') link-underline-opacity-100 @else  link-underline-opacity-0 @endif" href="/filterleads/consleads">Консультация<span
            class="badge m-1 @if($consleads == '0') text-bg-danger @else text-bg-secondary @endif">{{ $consleads }}</span></a>

    <a class="link-offset-2 link-underline link-opacity-75-hover  @if($route == 'defeatleads') link-underline-opacity-100 @else  link-underline-opacity-0 @endif" href="/filterleads/defeatleads">Мусорные<span
            class="badge m-1 @if($defeatleads == '0') text-bg-danger @else text-bg-secondary @endif">{{ $defeatleads }}</span></a>

    <a class="link-offset-2 link-underline link-opacity-75-hover  @if($route == 'withoutcaseleads') link-underline-opacity-100 @else  link-underline-opacity-0 @endif" href="/filterleads/withoutcaseleads">Бездельники<span
            class="badge m-1 @if($withoutcaseleads == '0') text-bg-danger @else text-bg-secondary @endif">{{ $withoutcaseleads }}</span></a>

    <a class="link-offset-2 link-underline link-opacity-75-hover  @if($route == 'winleads') link-underline-opacity-100 @else  link-underline-opacity-0 @endif" href="/filterleads/winleads">Выигранные<span
            class="badge m-1 @if($winleads == '0') text-bg-danger @else text-bg-secondary @endif">{{ $winleads }}</span></a>

    <a class="link-offset-2 link-underline link-opacity-75-hover  @if($route == 'failleads') link-underline-opacity-100 @else  link-underline-opacity-0 @endif" href="/filterleads/failleads">Проваленные<span
            class="badge m-1 @if($failleads == '0') text-bg-danger @else text-bg-secondary @endif">{{ $failleads }}</span></a>
</ul>

<form class="row my-2" action="{{ route('leads.filter', ['leadfilter' => $route]) }}" method="get" autocomplete="off">

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
            <a type="reset" href="{{ route('leads.filter', ['leadfilter' => $route]) }}" class="btn btn-secondary form-control">сбросить</a>
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
            <label for='casettype'><small>Тип дела</small></label>
            <select class="form-select" name="casettype" id="casettype">
                <option value="">не выбрано</option>
                @foreach(App\Models\Enums\Clients\Type::cases() as $case)
                <option value={{$case->value}} @if (($case->value) == request()->input('casettype'))) selected @endif>
                    {{$case->value}}
                </option>
                @endforeach
            </select>


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

<div>

    <div class="row">
        @foreach ($leads as $el)
        @include('leads/leadbadge')
        @endforeach
    </div>
    <div class="my-5 d-flex justify-content-center">
        {{ $leads->links() }}
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