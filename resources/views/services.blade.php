@extends('layouts.app')

@section('title')
    услуги
@endsection

@section('leftmenuone')
    <li class="nav-item text-center p-3">
        <a class="text-white text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#serviceModal">Добавить услугу</a>
    </li>
@endsection

@section('main')
    <h2 class="px-3 text-center">Услуги</h2>
    {{-- start views for all services--}}
    <div class="row">
    @foreach ($data as $service)
        <div class="col-md-4 my-3">
            <div class="card border-light">
                <div class="card-body">
                    <h5 class="card-title">{{ $service->name }}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">цена - {{ $service->price }}</h6>
                    <h6 class="header-title mb-3">время - {{ $service->execution_time/60 }} ч</h6>
                    
                    <div class="mt-3 row d-flex justify-content-center">
                        <div class="col-2 mb-3">
                            <a class="btn btn-edit-service btn-light" href="#"
                               data-url="{{ route('services.edit', $service) }}" data-bs-toggle="modal" data-bs-target="#editServiceModal" data-type="услуга">
                                <i class="bi-three-dots"></i>
                            </a>
                        </div>
                        <div class="col-2 mb-3">
                            <form action="{{ route('services.destroy', $service) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-light" type="submit">
                                    <i class="bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
    {{-- end views for all services--}}
@endsection

@include('inc/modal/edit-service')

@section('footerscript')
    <script type="text/javascript">
        $(document).ready(function () {
            // Модальное окно с формой редактирования Услуги
            $('.btn-edit-service').on('click', function (e) {
                e.preventDefault();
                var url = $(this).attr('data-url');
                $.ajax({
                    method: "POST",
                    url: url,
                    success: function (data) {
                        $('#editServiceModal .modal-body').html(data.content);
                    },
                    error: function (err) {
                        console.log(err);
                    }
                });
            });
        });
    </script>
@endsection
