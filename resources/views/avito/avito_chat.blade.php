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
@endsection

@section('title')
    Авито чаты
@endsection

@section('leftmenuone')
    <li class="nav-item text-center p-3">
        <a class="text-white text-decoration-none" href="{{ route('leads.filter', 'new') }}">Лиды
        </a>
    </li>
    <li class="nav-item text-center p-3">
        <a class="text-white text-decoration-none" href="{{ route('leadanalitics') }}">Аналитика</a>
    </li>
    <li class="nav-item text-center p-3">
        <a class="text-white text-decoration-none" href="{{ route('avito.chats') }}">Авито</a>
    </li>
@endsection

@section('main')
    <h2 class="px-3">Авито чат</h2>

    <div class="container">

        <ul class="list-group">
            @foreach ($messages as $msg)
                <li class="list-group-item">
                    <strong>{{ $msg->sender_id ?? 'Неизвестный' }}</strong>
                    <small
                        class="text-muted float-end">{{ \Carbon\Carbon::parse($msg->sent_at)->format('d.m.Y H:i') }}</small>
                    <br>
                    {{ $msg->message }}
                </li>
            @endforeach
        </ul>
    </div>

    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endsection
