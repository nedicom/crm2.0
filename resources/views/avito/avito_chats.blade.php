@extends('layouts.app')

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
        <a class="text-white text-decoration-none" href="{{ route('avito.chats') }}">Авито чаты</a>
    </li>
@endsection

@section('main')
    <h2 class="px-3">Авито чаты</h2>

    <div class="container">

        <div class="list-group">
            @forelse ($chats as $chat)
                <a href="{{ url('/avito/chat/' . $chat->chat_id) }}"
                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    Чат: {{ $chat->chat_id }}
                    <small
                        class="text-muted">{{ \Carbon\Carbon::parse($chat->last_message_at)->format('d.m.Y H:i') }}</small>
                </a>
            @empty
                <p>Чаты не найдены</p>
            @endforelse

        </div>
    </div>
@endsection
