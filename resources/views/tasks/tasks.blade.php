@extends('layouts.app')

@section('head')
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="/resources/datetimepicker/jquery.datetimepicker.css">
    <style>
        .task-toggle {
            position: absolute;
            top: 50%;
            right: 0;
            margin-top: -8px;
        }
        .task-content {
            padding: 0.4em;
        }
        .task-placeholder {
            border-left: 2px solid red;
            background-image: url('/avatars/plus.png');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            height: 100px;
        }
        .columncard:hover{
        }
    </style>
@endsection

@section('footerscript')
    <script src="/resources/datetimepicker/jquery.datetimepicker.full.js"></script>
@endsection

@section('title')
    Задачи
@endsection

@section('leftmenuone')
    <li class="nav-item text-center p-3">
        <a class="text-white text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#taskModal"
           data-user-id="{{ Auth::id() }}" data-type="{{ \App\Models\Enums\Tasks\Type::Task->value }}">
            Добавить задачу
        </a>
    </li>
@endsection

@section('main')
    @php $request = app('request'); @endphp
        <!-- Фильтр поиска -->
        <form class="row px-0" action="" method="GET">
            <h2 class="col-md-1 px-3">Задачи</h2>
            <div class="col-12">
                <div class="row">
                    <div class="col-12 col-md-2 my-2">
                        <a href="{{route('tasks')}}?checkedlawyer={{ Auth::user()->id}}" class="btn btn-outline-primary btn-sm">мои задачи</a>
                    </div>
                    <!-- Чекбоксы по интервалу времени -->
                    <div class="col-12 col-md-10">{!! \App\Helpers\TaskHelper::formCheckDateInterval($request) !!}</div>

                    @if ($request->input('calendar') == \App\Models\Enums\Tasks\DateInterval::Month->name)
                        <!-- Вывод селекта со списком месяцев -->
                        <div class="col-12 col-md-3">{!! \App\Helpers\TaskHelper::formListMonths($request) !!}</div>
                    @endif
                    
                </div>

            <div class="row">
                <div class="col-md-6 mt-2">
                    <div class="row justify-content-start">
                        <div class="col-6 col-md-4">
                            <select class="form-select" name="checkedlawyer" id="checkedlawyer">
                                <option value=''>не выбрано</option>
                                @foreach ($datalawyers as $el)
                                    <option value="{{ $el->id }}" @if ($el->id == $request->input('checkedlawyer')) selected @endif>
                                        {{ $el->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-md-">
                            <select class="form-select" name="type" id="type">
                                <option value="" @if ($request->input('type') == "") selected @endif >все типы</option>
                                @foreach (\App\Models\Enums\Tasks\Type::cases() as $type)
                                    <option value="{{ $type->value }}" @if ($request->input('type') == $type->value) selected @endif >
                                        {{ $type->value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mt-4 mt-md-2">
                    <div class="row justify-content-end">
                        <div class="col-6 col-md-4">
                            <button type="submit" class="btn btn-primary btn-sm col-12">Применить</button>                        
                        </div>
                        <div class="col-6 col-md-4">
                            <a href='tasks' class='button btn btn-secondary btn-sm col-12'>Сбросить</a>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </form>
    

    <div class="row" id="taskarea">
        @php
            $weekMap = [1 => 'Понедельник', 2 => 'Вторник', 3 => 'Среда', 4 => 'Четерг', 5 => 'Пятница', 6 => 'Суббота', 7 => 'Воскресенье'];
        @endphp
        @if ($request->input('calendar') == '' || $request->input('calendar') == \App\Models\Enums\Tasks\DateInterval::AllTime->name
            || \App\Helpers\TaskHelper::isDayInterval($request))
            <div class="row pt-4">
                <div class="col-md-3 columncard text-center" id="timeleft">
                    <h5 class="page-title" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Сюда попадают просроченные задачи каждый день в 8.00 утра">просрочка</h5>
                    @foreach ($data as $el)
                        @if ($el->status == "просрочена")
                            @include('tasks.taskcard')
                        @endif
                    @endforeach
                </div>
                <div class="col-md-3 columncard text-center" id="waiting">
                    <h5 class="page-title" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Тут задачи которые Вам поставили, но не принятые в работу">ожидает</h5>
                    @foreach ($data as $el)
                        @if ($el->status == "ожидает")
                            @include('tasks.taskcard')
                        @endif
                    @endforeach
                </div>
                <div class="col-md-3 columncard text-center" id="inwork">
                    <h5 class="page-title" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Здесь задачи с которыми вы работаете сейчас. Позже будет учитываться время потраченное на выполнение">в работе</h5>
                    @foreach ($data as $el)
                        @if ($el->status == "в работе")
                            @include('tasks.taskcard')
                        @endif
                    @endforeach
                </div>
                <div class="col-md-3 columncard text-center" id="finished">
                    <h5 class="page-title" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Каждый день в 00.00 выполненные задачи будут пропадать из списка">выполнена</h5>
                    @foreach ($data as $el)
                        @if (($el->status == "выполнена") && ($el->donetime > Carbon\Carbon::today()))
                            @include('tasks.taskcard')
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        @if ($request->input('calendar') == \App\Models\Enums\Tasks\DateInterval::Week->name)
            <div class="mt-2" style="display: grid; grid-template-columns: 20% 20% 20% 20% 20%;">
                @for ($i = 1; $i < 6; $i++)
                    <div class="text-center"> <h1 class="badge bg-secondary">{{$weekMap[$i]}}</h1></div>
                @endfor
                @for ($i = 1; $i < 6; $i++)
                    <div class="columncard bg-white m-1" style="font-size:12px; min-height: 400px" dayofweek="{{$i}}">
                        <span class="px-2"> </span>
                        @foreach ($data as $el)
                            @if ($el['date']['day'] == $weekMap[$i])
                                @include('tasks.taskcard')
                            @endif
                        @endforeach
                    </div>
                @endfor
            </div>
        @endif

        @if ($request->input('calendar') == \App\Models\Enums\Tasks\DateInterval::Month->name)
            <div class="mt-2" style="display: grid; grid-template-columns: 14% 14% 14% 14% 14% 14% 14%;">
                @for ($i = 1; $i < 8; $i++)
                    <div class="text-center">
                        <h1 class="badge bg-secondary">{{$weekMap[$i]}}</h1>
                    </div>
                @endfor
                @php
                    $time = mktime(0, 0, 0, date('n'), 1, date('Y'));
                    $firstday = (date('w', $time) + 6) % 7; //воскресенье сделаем 7-м днем, а не первым
                    $daycount = date('t', $time);
                @endphp
                @for ($i = 0; $i < $firstday; $i++)
                    <div class="my-3" style="min-height: 100px"></div>
                @endfor

                @for ($i = 1; $i <= (cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'))); $i++)
                    <div class="columncard bg-white m-1 " style="min-height: 100px" dayofmonth="{{$i}}">
                        <span class="px-2">{{$i}}</span>
                        @foreach ($data as $el)
                            @if ($el['date']['currentDay'] == $i)
                                @include('inc.calendar.task')
                            @endif
                        @endforeach
                    </div>
                @endfor
            </div>
        @endif

        @if ($request->input('calendar') == \App\Models\Enums\Tasks\DateInterval::Day->name)
            <h2 class=""></h2>
            @for ($i = 8; $i < 22; $i++)
                <div class='row'>
                    <div class='col-1 border-bottom py-1'>
                        <span class="w-20  badge bg-secondary">{{$i}}.00</span>
                    </div>
                    <div style="min-height: 200px;" class="col-11  bg-white my-3 columncard" hourofday="{{$i}}">
                        @foreach($data as $el)
                            @if($el['date']['currentHour'] == $i)
                                @include('tasks.taskcard')
                            @else
                                <div class="taskcard inline-block"></div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endfor
        @endif
        {{-- end views for all meetings--}}

        <script>
            function mouseDown(clicked_id) {
                document.getElementById(clicked_id).style.border = "solid 1px #FF1493";
                // document.getElementById('status'+clicked_id).innerHTML = "изменен"; // solwing problem with month
            }
            function mouseUp(clicked_id) {
                document.getElementById(clicked_id).style.border = "";
            }
        </script>

        <script>
            $(document).ready(function() {
                $( function() {
                    $( ".columncard" ).sortable({
                        connectWith: ".columncard",
                        handle: ".card",
                        cancel: ".task-toggle",
                        placeholder: "task-placeholder ui-corner-all",
                        opacity: 0.5,
                        receive: function(event, ui) {
                            var id =  ui.item.attr("id");

                            if (this.id) {var status =  this.id;}
                            else {var status = 0;}

                            $input = $( this );
                            if ($input.attr( "dayofweek" )) {var dayofweek = $input.attr( "dayofweek" );}
                            else {var dayofweek = 0;}
                            if (ui.item.attr("date")) {var date = ui.item.attr("date");}
                            else {var date = 0;}
                            if ($input.attr("hourofday")) {var hourofday = $input.attr("hourofday");}
                            else {var hourofday = 0;}
                            if ($input.attr( "dayofmonth" )) {var dayofmonth = $input.attr( "dayofmonth" );}
                            else {var dayofmonth = 0;}

                            $.ajax({
                                method:"POST",
                                url: "{{ route('setstatus') }}",
                                data: { id: id, status: status, date: date, hourofday: hourofday, dayofweek: dayofweek, dayofmonth: dayofmonth, _token: '{{csrf_token()}}' },
                                success: function(data) {
                                }
                            });
                        }
                    });

                    $( ".taskcard" )
                        .addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
                        .find( ".task-header" )
                        .addClass( "ui-widget-header ui-corner-all" )
                        .prepend( "<span class='ui-icon ui-icon-minusthick task-toggle'></span>");

                    $( ".task-toggle" ).on( "click", function() {
                        var icon = $( this );
                        icon.toggleClass( "ui-icon-minusthick ui-icon-plusthick" );
                        icon.closest( ".taskcard" ).find( ".task-content" ).toggle();
                    });
                });
            });
        </script>

        <script>
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl)
            })
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
        </script>

        <script>
            $( document ).ready(function() {
                $( ".changetags" ).click(function() {
                    var $input = $( this );
                    var id =  $input.attr( "tagName" )
                    var color =  $input.attr( "color" )
                    var value =  this.value;
                    $('#tagspan'+id).css("color", color);
                    $.ajax({
                        url:"{{ route('tag') }}",
                        method:"POST",
                        data: { id: id, value: value, _token: '{{csrf_token()}}' },
                        success:function(data){
                        }
                    });
                });
            });
        </script>
    @include('inc/modal/addtask')
    @include('inc/modal/addtypetask')
@endsection
