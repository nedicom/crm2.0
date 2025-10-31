<div class="my-3 d-inline-block shadow m-1 taskcard" onmousedown="mouseDown(this.id)" onmouseup="mouseUp(this.id)"
    date="{{ $el['date']['value'] }}" id="{{ $el->id }}"
    style="width: 100%; font-size: 14px;
    @if (app('request')->input('calendar') == 'week') max-width:250px;
    @elseif (app('request')->input('calendar') == 'day')
    max-width:250px;
    @else max-width:300px; @endif ">
    <div class="card" id="card{{ $el->id }}" style="{{ \App\Helpers\TaskHelper::typeStyle($el->type) }}">
        <div class="task-header px-3 pt-3 d-flex justify-content-between">
            <span>
                <i class="bi bi-calendar"></i>
                <span>{{ $el['date']['currentDay'] }}</span>
                <span>{{ $el['date']['currentMonth'] }}</span>
                <span>{{ $el['date']['currentTime'] }}</span>
            </span>
            <span>
                <span id="status{{ $el->id }}">
                    {{ $el->status }}
                </span>
            </span>
        </div>
        <div class="card-body task-content">
            <div class="progress" style="height: 2px; ">
                <div class="progress-bar" role="progressbar" aria-label="Example 1px high"
                    style="{{ \App\Helpers\TaskHelper::widthSidebarByStatus($el->status) }}"></div>
            </div>

            <div class="d-flex p-2 justify-content-between align-items-center">
                <span>
                    @foreach ($datalawyers as $ellawyer)
                    @if ($ellawyer->id == $el->postanovshik)
                    <img src="{{ $ellawyer->avatar }}" style="width: 30px; height:30px" class="rounded-circle"
                        data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="постановщик">
                    @endif
                    @endforeach
                </span>
                <span class="">
                    <span class="">{{ $el->duration }} </span><i class="bi bi-stopwatch mx-1"></i>
                </span>
                <span>
                    <span>
                        @foreach ($datalawyers as $ellawyer)
                        @if ($ellawyer->id == $el->lawyer)
                        <img src="{{ $ellawyer->avatar }}" style="width: 30px; height:30px"
                            class="rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="исполнитель">
                        @endif
                        @endforeach
                    </span>
                </span>
            </div>



            @if ($el->lead_id)
            <a href="/leads/{{ $el->lead_id }}" target="_blank" class="text-truncate" data-bs-trigger="hover focus" data-bs-toggle="popover" data-bs-content="{{ $el->name }}">{{ $el->name }}</a>
            @endif
            @if ($el->clientid)
            <a href="/clients/{{ $el->clientid }}" class="text-truncate" target="_blank" data-bs-trigger="hover focus" data-bs-toggle="popover" data-bs-content="{{ $el->client }}">{{ $el->client }}</a>
            <div class="px-2 text-truncate text-center" data-bs-trigger="hover focus" data-bs-toggle="popover" data-bs-content="{{ $el->name }}">{{ $el->name }}</div>
            @else
            <div class="px-2 text-truncate text-center" data-bs-trigger="hover focus" data-bs-toggle="popover" data-bs-content="{{ $el->client }}">{{ $el->client }}</div>
            @endif

            @if ($el->description)
            <div class="px-2 text-truncate text-center" tabindex="0" data-bs-toggle="popover"
                data-bs-trigger="hover focus" data-bs-content="{{ $el->description }}">
                {{ $el->description }}
            </div>
            @else
            описания нет
            @endif


            <div class="mt-3 px-3 d-flex justify-content-center">
                @if ($el->hrftodcm)
                <div class="col-3 px-1 mb-1">
                    <a href="{{ $el->hrftodcm }}" class="btn btn-light w-100" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="Blue"
                            class="bi bi-hdd" viewBox="0 0 16 16">
                            <path d="M4.5 11a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zM3 10.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z">
                            </path>
                            <path
                                d="M16 11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V9.51c0-.418.105-.83.305-1.197l2.472-4.531A1.5 1.5 0 0 1 4.094 3h7.812a1.5 1.5 0 0 1 1.317.782l2.472 4.53c.2.368.305.78.305 1.198V11zM3.655 4.26 1.592 8.043C1.724 8.014 1.86 8 2 8h12c.14 0 .276.014.408.042L12.345 4.26a.5.5 0 0 0-.439-.26H4.094a.5.5 0 0 0-.44.26zM1 10v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1z">
                            </path>
                        </svg>
                    </a>
                </div>
                @endif
<div class="col-3 px-1 mb-1">
    <button type="button" class="btn btn-light w-100 copy-title-btn"
            data-title="{{$el->client}} - {{$el->name}} - {{$el->status}} - https://crm.nedicom.ru/tasks/{{$el->id}}"
            title="Скопировать название">
        <i class="bi bi-clipboard"></i>
    </button>
</div>
<div class="col-3 px-1 mb-1">
    <a class="btn btn-light w-100" href="{{ route('showTaskById', $el->id) }}">
        <i class="bi-three-dots"></i>
    </a>
</div>
<div class="col-3 px-1 mb-1">
    <a class="btn btn-light w-100" data-bs-toggle="collapse" href="#collapse{{ $el->id }}"
       role="button" aria-expanded="false" aria-controls="collapseExample">
        <i class="bi bi-tag-fill"></i>
    </a>
</div>
            </div>
            <div class="collapse text-center" id="collapse{{ $el->id }}">
                <button class="btn changetags" tagName="{{ $el->id }}" color='LightGray' value="неважно"
                    data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="неважно">
                    <i class="bi bi-tag-fill" style="color: LightGray;"></i>
                </button>
                <button class="btn changetags" tagName="{{ $el->id }}" color='Aquamarine' value="перенос"
                    data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="перенос">
                    <i class="bi bi-tag-fill" style="color: Aquamarine;"></i>
                </button>
                <button class="btn changetags" tagName="{{ $el->id }}" color='Red' value="срочно"
                    data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="срочно">
                    <i class="bi bi-tag-fill" style="color: Red;"></i>
                </button>
                <button class="btn changetags" tagName="{{ $el->id }}" color='BlueViolet' value="приоритет"
                    data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="приоритет">
                    <i class="bi bi-tag-fill" style="color: BlueViolet;"></i>
                </button>
            </div>
            <span class="position-absolute top-0 start-100 translate-middle">
                <i class="bi bi-tag-fill" id="tagspan{{ $el->id }}"
                    style="color:
                @if ($el->tag == 'неважно') LightGray
                @elseif ($el->tag == 'перенос') Aquamarine
                @elseif ($el->tag == 'срочно') Red
                @elseif ($el->tag == 'приоритет') BlueViolet
                @else Black @endif
                ;"></i>
            </span>
            <span class="badge position-absolute top-0 start-50 translate-middle"
                style="background-color:
                @if ($el->type == 'задача') CornflowerBlue
                @elseif ($el->type == 'консультация') LightGreen
                @elseif ($el->type == 'звонок') LightSeaGreen
                @elseif ($el->type == 'заседание') LightSalmon
                @elseif ($el->type == 'допрос') CornflowerBlue
                @else Black @endif
                ;">{{ $el->type }}</span>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const copyButtons = document.querySelectorAll('.copy-title-btn');

        copyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const title = this.getAttribute('data-title');

                // Создаем временный textarea для копирования
                const textarea = document.createElement('textarea');
                textarea.value = title;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);

                // Выделяем и копируем текст
                textarea.select();
                textarea.setSelectionRange(0, 99999); // Для мобильных устройств

                try {
                    const successful = document.execCommand('copy');
                    if (successful) {
                        // Визуальная обратная связь
                        const originalHtml = this.innerHTML;
                        this.innerHTML = '<i class="bi bi-check"></i>';
                        this.classList.remove('btn-outline-secondary');
                        this.classList.add('btn-success');

                        // Возвращаем исходное состояние через 2 секунды
                        setTimeout(() => {
                            this.innerHTML = originalHtml;
                            this.classList.remove('btn-success');
                            this.classList.add('btn-outline-secondary');
                        }, 2000);
                    }
                } catch (err) {
                    console.error('Ошибка при копировании: ', err);
                }

                // Удаляем временный элемент
                document.body.removeChild(textarea);
            });
        });
    });
</script>