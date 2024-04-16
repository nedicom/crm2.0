@extends('layouts.app')

@section('head')
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="/resources/datetimepicker/jquery.datetimepicker.css">
@endsection

@section('footerscript')
    <script src="/resources/datetimepicker/jquery.datetimepicker.full.js"></script>
@endsection

@section('title') Аналитика лидов @endsection

@section('leftmenuone')
    <li class="nav-item text-center p-3">
        <a class="text-white text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#leadsModal">Добавить лид</a>
    </li>
    <li class="nav-item text-center p-3">
        <a class="text-white text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#SourcesModal">Источники лидов</a>
    </li>
@endsection

@section('main')
    <h2 class="px-3">Аналитика лидов</h2>

    <div class="row">
        <div class = "row mt-2" style="height: 700px;">
            <iframe src="https://datalens.yandex/6ha1zr8aw6tku"></iframe>
        </div>
    </div>

    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endsection
