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
                <div class="chat-item mb-3 p-3 border rounded position-relative">
                    <h5>Чат ID: {{ $chat['id'] }}</h5>

                    {{-- Чекбокс GPT Active в правом верхнем углу --}}
                    <div class="gpt-active-checkbox" style="position: absolute; top: 10px; right: 10px;">
                        <input type="checkbox" id="{{ $chat['id'] }}"
                            {{ $chat['is_gpt_active'] ? 'checked' : '' }}>
                        <label for="gpt-active-{{ $chat['id'] }}" style="user-select: none;">GPT Active</label>
                    </div>

                    {{-- Информация о предмете (item) из context --}}
                    <p><strong>Тема:</strong> {{ $chat['context']['value']['title'] ?? 'Без названия' }}</p>

                    <p><strong>Цена:</strong> {!! nl2br(e($chat['context']['value']['price_string'] ?? 'Не указана')) !!}</p>
                    <p><strong>Ссылка:</strong> <a href="{{ $chat['context']['value']['url'] ?? '#' }}"
                            target="_blank">Перейти</a></p>

                    {{-- Пользователи в чате --}}
                    <p><strong>Участники:</strong>
                        @foreach ($chat['users'] as $user)
                            {{ $user['name'] }}@if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                    </p>

                    {{-- Последнее сообщение --}}
                    @if (!empty($chat['last_message']))
                        <div class="last-message mt-2 p-2 bg-light rounded">
                            <p><strong>Последнее сообщение:</strong></p>
                            <p>{{ $chat['last_message']['content']['text'] ?? '' }}</p>
                            <small class="text-muted">
                                От {{ $chat['last_message']['author_id'] }} —
                                {{ \Carbon\Carbon::createFromTimestamp($chat['last_message']['created'])->format('d.m.Y H:i') }}
                            </small>
                        </div>
                    @endif
                </div>

                @empty
                    <p>Чаты не найдены</p>
                @endforelse

            </div>
        </div>

        <script>
            $(document).ready(function() {
                $('input[type=checkbox]').change(function() {
                    var chatId = $(this).attr('id').split('-').pop(); // получить id из id чекбокса
                    var isActive = $(this).is(':checked') ? 1 : 0;

                    $.ajax({
                        url: '/update-gpt-active', // маршрут для обновления
                        type: 'POST',
                        data: {
                            id: chatId,
                            is_gpt_active: isActive,
                            _token: '{{ csrf_token() }}' // CSRF токен для безопасности
                        },
                        success: function(response) {
                            console.log('Статус обновлен');
                        },
                        error: function(xhr) {
                            alert('Ошибка при обновлении статуса');
                        }
                    });
                });
            });
        </script>
    @endsection