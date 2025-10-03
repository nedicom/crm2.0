@extends('layouts.app')

@section('title') Договоры @endsection

@section('leftmenuone')
    <li class="nav-item text-center p-3">
        <a class="text-white text-decoration-none" href="#" id="ipmina" onclick="checkIspolnitel('ipmina')" data-bs-toggle="modal" data-bs-target="#dogovorModal">договор с ИП Мина О. В.</a>
    </li>
    <li class="nav-item text-center p-3">
        <a class="text-white text-decoration-none" href="#" id="advoakatmina" onclick="checkIspolnitel('advokatmina')" data-bs-toggle="modal" data-bs-target="#dogovorModal">договор с адвокатом</a>
    </li>
@endsection
@section('main')
    <h2 class="px-3 text-center">Договоры</h2>

    <!-- Поисковая форма -->
    <div class="row mb-4">
        <div class="col-md-6 mx-auto">
            <form action="{{ route('contracts.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Введите фамилию клиента..." 
                           value="{{ request('search') }}"
                           autocomplete="off">
                    <button class="btn btn-primary" type="submit">Найти</button>
                    @if(request('search'))
                        <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">Сбросить</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Информация о поиске -->
    @if(request('search'))
        <div class="alert alert-info text-center">
            Результаты поиска по: "{{ request('search') }}"
        </div>
    @endif

    <div class="row">
        @php /** @var \App\Models\Dogovor $el */ @endphp
        @if ($currentuser->role == 'admin' || $currentuser->role == 'head_lawyer' || $currentuser->role  == 'head_sales' || $currentuser->role  == 'user_service_clients')
            @foreach ($data as $el)
                @include('dogovor/_item_dogovor', compact('datalawyers', 'dataclients', 'el'))
            @endforeach
        @else
            @foreach ($data as $el)
                @if($currentuser->id == $el->lawyer_id)
                    @include('dogovor/_item_dogovor', compact('datalawyers', 'dataclients', 'el'))
                @endif
            @endforeach
        @endif 
    </div>

    <!-- Сообщение если ничего не найдено -->
    @if($data->count() == 0)
        <div class="alert alert-warning text-center">
            @if(request('search'))
                Договоры по запросу "{{ request('search') }}" не найдены
            @else
                Договоры не найдены
            @endif
        </div>
    @endif
    
    <script>
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl)
        })
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>

    @include('inc./modal/adddogovor')
@endsection