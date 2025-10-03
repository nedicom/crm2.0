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
