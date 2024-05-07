@extends('layouts.app')

@section('title') Договоры @endsection

@section('leftmenuone')
    <li class="nav-item text-center p-3">
        <a class="text-white text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#dogovorModal">Добавить договор</a>
    </li>
@endsection

@section('main')
    <h2 class="px-3 text-center">Договоры</h2>

    <div class="row">
        @php /** @var \App\Models\Dogovor $el */ @endphp
        @foreach ($data as $el)
            @include('dogovor/_item_dogovor', compact('datalawyers', 'dataclients', 'el'))
        @endforeach
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
