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
    <li class="nav-item text-center p-3">
        <a class="text-white text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#leadsModal">Добавить лид</a>
    </li>
    <li class="nav-item text-center p-3">
        <a class="text-white text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#SourcesModal">Источники лидов</a>
    </li>
@endsection

@section('main')
    <h2 class="px-3">Лиды</h2>
    @include('inc/filter.leadfilter')

    <div class="row">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col"># </th>
                    <th scope="col">Дата обращения)</th>
                    <th scope="col">Суть проблемы</th>
                    <th scope="col">Имя клиента</th>
                    <th scope="col">Телефон</th>
                    <th scope="col">Консультация</th>
                    <th scope="col">Результат</th>
                    <th scope="col">Ответственный юрист</th>
                    <th scope="col">Привлек</th>
                    <th scope="col">Источник</th>
                    <th scope="col">Задача</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $el)
                    @include('leads/leadrow')
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Модальные окна -->
    @include('inc./modal/leadsmodal/addlead')
    @include('inc./modal/leadsmodal/sources')
    @include('inc.modal.leadsmodal.add_task')

    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endsection
