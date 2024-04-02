@extends('layouts.app')

@section('head')
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="/resources/datetimepicker/jquery.datetimepicker.css">
@endsection

@section('footerscript')
    <script src="/resources/datetimepicker/jquery.datetimepicker.full.js"></script>
@endsection

@section('title') Лиды @endsection

@section('leftmenuone')
    <li class="nav-item text-center p-3">
        <a class="text-white text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#leadsModal">Добавить лид</a>
    </li>
    <li class="nav-item text-center p-3">
        <a class="text-white text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#SourcesModal">Источники лидов</a>
    </li>
@endsection

@section('main')

    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-newleads-tab" data-bs-toggle="pill" data-bs-target="#pills-newleads" type="button" role="tab" aria-controls="pills-newleads" aria-selected="true">Новые</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-phoneleads-tab" data-bs-toggle="pill" data-bs-target="#pills-phoneleads" type="button" role="tab" aria-controls="pills-phoneleads" aria-selected="false">Дозвон</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-consleads-tab" data-bs-toggle="pill" data-bs-target="#pills-consleads" type="button" role="tab" aria-controls="pills-consleads" aria-selected="false">Консультация</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-defeatleads-tab" data-bs-toggle="pill" data-bs-target="#pills-defeatleads" type="button" role="tab" aria-controls="pills-defeatleads" aria-selected="false">Провален</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-winleads-tab" data-bs-toggle="pill" data-bs-target="#pills-winleads" type="button" role="tab" aria-controls="pills-winleads" aria-selected="false">Конвертирован</button>
            </li>
        </ul>


        <div class="tab-content" id="pills-tabContent">
            
            <div class="tab-pane fade show active" id="pills-newleads" role="tabpanel" aria-labelledby="pills-newleads-tab">
               <div class="row">
                    @foreach ($newleads as $el)
                        @include('leads/leadbadge')
                    @endforeach
                </div>
            </div>

            <div class="tab-pane fade" id="pills-consleads" role="tabpanel" aria-labelledby="pills-consleads-tab">
            <div class="row">
                @foreach ($consleads as $el)
                    @include('leads/leadbadge')
                @endforeach
            </div>
            </div>

            <div class="tab-pane fade" id="pills-phoneleads" role="tabpanel" aria-labelledby="pills-phoneleads-tab">
            <div class="row"> @foreach ($phoneleads as $el)
                @include('leads/leadbadge')
                @endforeach
            </div>
            </div>


            <div class="tab-pane fade" id="pills-defeatleads" role="tabpanel" aria-labelledby="pills-defeatleads-tab">
            <div class="row">@foreach ($defeatleads as $el)
                @include('leads/leadbadge')
                @endforeach
                </div>
            </div>

            <div class="tab-pane fade" id="pills-winleads" role="tabpanel" aria-labelledby="pills-winleads-tab">
            <div class="row">@foreach ($winleads as $el)
            
                @include('leads/leadbadge')
                
                @endforeach
                </div>
            </div>
</div>



    <!-- Модальные окна -->
    @include('inc./modal/leadsmodal/addlead')
    @include('inc./modal/leadsmodal/sources')

    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endsection
