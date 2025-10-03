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
    <div class="container">
        <div class="row">
            <h2 class="px-3 col">Авито чаты</h2>
            <form id="gptToggleForm" class="col d-flex flex-column flex-sm-row align-items-center px-3">
                @csrf
                <div class="form-check me-sm-3 mb-2 mb-sm-0">
                    <input class="form-check-input" type="radio" name="is_active" id="gptActiveOn" value="1"
                        {{ $is_gpt_active[0]->global_gpt_active == '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="gptActiveOn">Включено</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="is_active" id="gptActiveOff" value="0"
                        {{ $is_gpt_active[0]->global_gpt_active == '0' ? 'checked' : '' }}>
                    <label class="form-check-label" for="gptActiveOff">Выключено</label>
                </div>
            </form>
        </div>
        
        <!-- Форма для ввода prompt -->
        <form action="{{ route('prompt.store') }}" method="POST" class="my-4">
            @csrf

            <div class="mb-4">
                <h5>Текущий промпт</h5>
                @if ($promptForm)
                    <div class="card p-3 bg-light">
                        <p class="mb-1"><strong>Ты — специалист (юрист), который продает клиенту юридические услуги на
                                Авито в чате.</strong> {{ $promptForm->prompt }}</p>
                        <p class="mb-0 text-muted">
                            <small>Создан: {{ $promptForm->created_at->format('d.m.Y H:i') }}</small> |
                            <small>ID: {{ $promptForm->id }}</small>
                        </p>
                    </div>
                @else
                    <p class="text-muted">Промпт не найден.</p>
                @endif
            </div>

            <div class="mb-3">
                <label for="prompt" class="form-label">Новый промпт:</label>
                <textarea id="prompt" name="prompt" rows="3" class="form-control @error('prompt') is-invalid @enderror"
                    required>{{ old('prompt') }}</textarea>
                @error('prompt')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>


        <!-- Отображение ошибок валидации -->
        @if ($errors->any())
            <div style="color: red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif



        <div class="list-group">
            @forelse ($chats as $chat)
                <div class="chat-item mb-3 p-3 border rounded position-relative">
                    <h5><strong>Тема:</strong> {{ $chat['context']['value']['title'] ?? 'Без названия' }}</h5>

                    {{-- Чекбокс GPT Active в правом верхнем углу --}}
                    <div class="gpt-active-checkbox" style="position: absolute; top: 10px; right: 10px;">
                        <input type="checkbox" id="{{ $chat['id'] }}" {{ $chat['is_gpt_active'] ? 'checked' : '' }}>
                        <label for="gpt-active-{{ $chat['id'] }}" style="user-select: none;">GPT Active</label>
                    </div>

                    {{-- Информация о предмете (item) из context --}}


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

                    @if (!empty($chat['gpt_prompt']))
                        <div class="mt-2 p-2 bg-light rounded">
                            <small>
                                <p><strong>Промпт:</strong></p>
                                <p>{{ $chat['gpt_prompt'] ?? '' }}</p>
                            </small>
                        </div>
                    @endif

                    <small class="text-muted">
                        id чата - {{ $chat['id'] }}
                    </small>
                </div>

                @empty
                    <p>Чаты не найдены</p>
                @endforelse

            </div>
        </div>

        <script>
            $(document).ready(function() {
                $('input[type=checkbox]').change(function() {
                    var chatId = $(this).attr('id'); // получить id из id чекбокса
                    var isActive = $(this).is(':checked') ? 1 : 0;
                    $.ajax({
                        url: 'https://crm.nedicom.ru/update-gpt-active', // маршрут для обновления
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

            document.querySelectorAll('input[name="is_active"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    fetch("{{ route('gpt.toggle') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                is_active: this.value
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Статус обновлен', data);
                        })
                        .catch(error => {
                            console.error('Ошибка:', error);
                        });
                });
            });
        </script>


    @endsection
