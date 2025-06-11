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
    <h2 class="px-3">Авито чаты</h2>

    <div class="container">

        <div class="list-group">
            @foreach ($chats as $chat)
                <a href="{{ url('/avito/chat/' . $chat->chat_id) }}"
                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    Чат: {{ $chat->chat_id }}
                    <small
                        class="text-muted">{{ \Carbon\Carbon::parse($chat->last_message_at)->format('d.m.Y H:i') }}</small>
                </a>
            @endforeach
        </div>
    </div>

    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endsection
