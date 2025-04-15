@extends('layouts.app')

@section('head')
    <script src="https://kit.fontawesome.com/dd594527e8.js" crossorigin="anonymous"></script>
@endsection

@section('title')
    Главная
@endsection

@section('leftmenuone')
    <form action="{{route('home')}}" class="my-3" method="GET" id="form-filter">
        <div class="d-inline-flex flex-column px-3 m-1 mb-3">
            <div class="form-check">
                <input class="btn-check input-home-filter" type="radio" name="date" id="day" value="day" @if ('day' == (request()->get('date'))) checked @endif>
                <label class="btn btn-light" for="day">День</label>
                <input class="btn-check input-home-filter" type="radio" name="date" id="month" value="month" @if ('month' == (request()->get('date'))) checked @endif>
                <label class="btn btn-light" for="month">Месяц</label>
            </div>
        </div>
        <div class="px-3 m-1">
            <select class="form-select form-select-sm input-home-filter" name="lawyer">
                <option value="">юрист</option>
                @foreach($data['datalawyers'] as $el)
                    <option value="{{$el->id}}" @if ($el->id == (request()->get('lawyer'))) selected @endif>{{$el->name}}</option>
                @endforeach
            </select>
        </div>
    </form>
@endsection

@section('main')
    <div class="row">
        @can('manage-users')
            <div class="px-3 col-md-6 col-12 pb-3">
                <h5>Сервис Мои звонки</h5>
                <div class="row">
                    <div class="col-md-3 col-12">
                        <form action="{{ route('mycalls.subscribe.call') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">Подписаться</button>
                        </form>
                    </div>
                    <div class="col-md-3 col-12 my-1 my-md-0">
                        <form action="{{ route('mycalls.unsubscribe.call') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">Отписаться</button>
                        </form>
                    </div>
                    <!-- Для теста -->
                    <div class="col-md-3 col-12">
                        <form action="{{ route('mycalls.download_log') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">Скачать логи</button>
                        </form>
                    </div>
                </div>
            </div>
        @endcan
        {{ config('app.bot_staff.link') }}
        <div class="col-md-6 col-12">
            <h5>Бот информер</h5>
            <div class="mb-2">
                @if($user->tg_id)
                    <a href="{{ config('app.bot_staff.link') }}?start={{ base64_encode($user->id) }}">Бот-информер подключен</a>.
                @else
                    Для подключения бота-инфомера к своему аккаунту
                    <a href="{{ config('app.bot_staff.link') }}?start={{ base64_encode($user->id) }}">перейдите по ссылке</a>.
                @endif
            </div>

            <h5>Аватар</h5>
            <form enctype="multipart/form-data" action="{{ route('add-avatar') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-8">
                        <input class="form-control" type="file" id="avatar" name="avatar" accept=".png, .jpg, .jpeg" required>
                    </div>
                    <div class="col-4">
                        <input type="submit" value="сменить аватар" class="btn btn-secondary">
                    </div>
                </div>
            </form>

            <div class="pt-2">
            <h5>Яндекс календарь</h5>
                <label for="calendarurl" class="visually-hidden">Копировать ссылку</label>
                <div class="input-group">
                    <div class="input-group-text" id="btnurl">Копировать ссылку</div>
                    <input class="form-control" type="text"
                        value="{{ (file_exists(storage_path('app/public/calendar/user_' . auth()->user()->id . '/calendar.ics') )) ? route('calendar.browse', auth()->user()->id) : 'Файл отсутствует' }}"
                        id="calendarurl">
                </div>
            </div>

            <div class="pt-2">
            <h5>Отправить лиды в Яндекс</h5>
            <form action="{{ route('metrikaleads') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-4">
                        <input type="submit" value="отправить" class="btn btn-secondary">
                    </div>
                </div>
            </form>
            </div>

            <div class="pt-2">
                <h5>xls для Яндекс карт</h5>
                <form enctype="multipart/form-data" action="{{ route('yandexmap') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <input class="form-control" type="file" id="yandexcsv" name="yandexcsv"
                           accept=".csv" required>
                        </div>
                        <div class="col-3">
                            <input class="form-control" type="text" id="city" name="city" placeholder="Город"
                           required>
                        </div>
                        <div class="col-3">
                            <input type="submit" value="конвертировать" class="btn btn-secondary">
                        </div>
                    </div>
                </form>
                </div>
        </div>
    </div>

    <div class="row mt-5">
        <h3>Показатели <small class="text-muted">@if ('day' == (request()->get('date'))) сегодня @else месяц @endif</small></h3>
        @if (config('app.debug'))
            <div class = "row mt-2" style="height: 700px;">
                <iframe src="https://datalens.yandex/gwhlvrc5b8es6"></iframe>
            </div>
        @endif
        <!-- новые задачи -->
        <div class="d-flex align-items-center p-3 my-3 text-white rounded shadow-sm" style="background-color: #0d6efd;">
            <div class="lh-1">
                <h1 class="h6 mb-0 text-white lh-1">Новые задачи</h1>
                <small>{{$crtuser->name}}</small>
            </div>
        </div>
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Исполнитель</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Постановщик</button>
            </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                @foreach($all['alltasksnew'] as $el)
                    <div class="d-flex text-body-secondary pt-3">
                        <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#ff0000"></rect><text x="50%" y="50%" fill="#ff0000" dy=".3em">{{$el->type}}</text></svg>
                        <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
                            <div class="d-flex justify-content-between">                               
                                <div @if($el->client)@endif>
                                    <strong class="text-gray-dark"> {{$el->client}} </strong>
                                    {{$el->type}}
                                </div>                                 
                                <span
                                    @if ($el->tag == "неважно")
                                    class="badge bg-secondary rounded-pill"
                                    @elseif($el->tag == "срочно ")
                                    class="badge bg-danger rounded-pill"
                                    @elseif($el->tag == "приоритет")
                                    class="badge bg-success rounded-pill"
                                    @elseif($el->tag == "перенос")
                                    class="badge bg-info rounded-pill"
                                    @else
                                    class="badge bg-secondary rounded-pill"
                                    @endif
                                ><div>{{$el->tag}}</div></span>
                            </div>  
                                          
                            <a href="tasks/{{$el->id}}" class="text-decoration-none" target="_blank">{{$el->name}}</a>
                            <div class="d-flex justify-content-between"> 
                                <div>поставлена - {{$el->created}} </div>
                                <div>срок - {{$el->donedate}} </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                @foreach($all['alltasksnewpostanovshik'] as $el)
                    <div class="d-flex text-body-secondary pt-3">
                        <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#e83e8c"></rect><text x="50%" y="50%" fill="#e83e8c" dy=".3em">32x32</text></svg>
                        <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
                            <div class="d-flex justify-content-between">                               
                                <div @if($el->client)@endif>
                                    <strong class="text-gray-dark"> {{$el->client}} </strong>
                                    {{$el->type}}
                                </div>                                 
                                <span
                                    @if ($el->tag == "неважно")
                                    class="badge bg-secondary rounded-pill"
                                    @elseif($el->tag == "срочно ")
                                    class="badge bg-danger rounded-pill"
                                    @elseif($el->tag == "приоритет")
                                    class="badge bg-success rounded-pill"
                                    @elseif($el->tag == "перенос")
                                    class="badge bg-info rounded-pill"
                                    @else
                                    class="badge bg-secondary rounded-pill"
                                    @endif
                                ><div>{{$el->tag}}</div></span>
                            </div>  
                                          
                            <a href="tasks/{{$el->id}}" class="text-decoration-none" target="_blank">{{$el->name}}</a>
                            <div class="d-flex justify-content-between"> 
                                <div>поставлена - {{$el->created}} </div>
                                <div>срок - {{$el->donedate}} </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- просрочка -->
                <div class="d-flex align-items-center p-3 my-3 text-white rounded shadow-sm" 
                style="background-color: #6f42c1;">
                    <div class="lh-1">
                        <h1 class="h6 mb-0 text-white lh-1">Просрочка</h1>
                        <small>{{$crtuser->name}}</small>
                    </div>
                </div>
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-isp-tab" data-bs-toggle="pill" data-bs-target="#pills-isp" type="button" role="tab" aria-controls="pills-isp" aria-selected="true">Исполнитель</button>
                    </li>                   
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-post-tab" data-bs-toggle="pill" data-bs-target="#pills-post" type="button" role="tab" aria-controls="pills-post" aria-selected="false">Постановщик</button>
                    </li>
                </ul>

                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-isp" role="tabpanel" aria-labelledby="pills-isp-tab">
                        <h6 class="border-bottom pb-2 mb-0">
                            Я - исполнитель
                        </h6>
                        @foreach($all['alltaskstime'] as $el)
                            <div class="d-flex text-body-secondary pt-3">
                                <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#007bff"></rect><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
                                <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
                                    <div class="d-flex justify-content-between">                               
                                        <div @if($el->client)@endif>
                                            <strong class="text-gray-dark"> {{$el->client}} </strong>
                                            {{$el->type}}
                                        </div>                                 
                                        <span
                                            @if ($el->tag == "неважно")
                                            class="badge bg-secondary rounded-pill"
                                            @elseif($el->tag == "срочно ")
                                            class="badge bg-danger rounded-pill"
                                            @elseif($el->tag == "приоритет")
                                            class="badge bg-success rounded-pill"
                                            @elseif($el->tag == "перенос")
                                            class="badge bg-info rounded-pill"
                                            @else
                                            class="badge bg-secondary rounded-pill"
                                            @endif
                                        ><div>{{$el->tag}}</div></span>
                                    </div>  
                                                  
                                    <a href="tasks/{{$el->id}}" class="text-decoration-none" target="_blank">{{$el->name}}</a>
                                    <div class="d-flex justify-content-between"> 
                                        <div>поставлена - {{$el->created}} </div>
                                        <div>срок - {{$el->donedate}} </div>
                                    </div>
                                </div>
            
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show" id="pills-post" role="tabpanel" aria-labelledby="pills-post-tab">
                        <h6 class="border-bottom pb-2 mb-0">
                            Я - постановщик
                        </h6>
                        @foreach($all['alltaskspostanovshik'] as $el)
                            <div class="d-flex text-body-secondary pt-3">
                                <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#e83e8c"></rect><text x="50%" y="50%" fill="#e83e8c" dy=".3em">32x32</text></svg>
                                <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
                                    <div class="d-flex justify-content-between">                               
                                        <div @if($el->client)@endif>
                                            <strong class="text-gray-dark"> {{$el->client}} </strong>
                                            {{$el->type}}
                                        </div>                                 
                                        <span
                                            @if ($el->tag == "неважно")
                                            class="badge bg-secondary rounded-pill"
                                            @elseif($el->tag == "срочно ")
                                            class="badge bg-danger rounded-pill"
                                            @elseif($el->tag == "приоритет")
                                            class="badge bg-success rounded-pill"
                                            @elseif($el->tag == "перенос")
                                            class="badge bg-info rounded-pill"
                                            @else
                                            class="badge bg-secondary rounded-pill"
                                            @endif
                                        ><div>{{$el->tag}}</div></span>
                                    </div>  
                                                  
                                    <a href="tasks/{{$el->id}}" class="text-decoration-none" target="_blank">{{$el->name}}</a>
                                    <div class="d-flex justify-content-between"> 
                                        <div>поставлена - {{$el->created}} </div>
                                        <div>срок - {{$el->donedate}} </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
@endsection
