<div class="col-10 p-3 shadow-sm bg-white">
    <header class="d-flex justify-content-evenly">
        <ul class="nav nav-pills">            
      
            <li class="nav-item btn-group dropdown">
                <a href="{{route('leads')}}" data-bs-toggle="dropdown" role="button" aria-expanded="false"
                class="nav-link dropdown-toggle {{ (request()->is('leads*')) ? 'active' : '' }}">Лиды</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{route('leads')}}">Лиды</a></li>
                    <li><a class="dropdown-item" href="{{route('oldleads')}}">Старая версия</a></li>
                    <li><a class="dropdown-item" href="{{route('leadanalitics')}}">Аналитика</a></li>
                </ul>
            </li>

            <li class="nav-item btn-group dropdown">
                <a href="#" data-bs-toggle="dropdown" role="button" aria-expanded="false"
                    class="nav-link dropdown-toggle {{ (request()->is('clients*')) ? 'active' : '' }} {{ (request()->is('dogovor*')) ? 'active' : '' }}">Клиенты</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('clients') }}?checkedlawyer={{Auth::user()->id}}&status=1">Все клиенты</a></li>
                    <li><a class="dropdown-item" href="{{ route('contracts.index') }}">Договоры</a></li>
                </ul>
            </li>
            <li class="nav-item btn-group dropdown">
                <a href="#" data-bs-toggle="dropdown" role="button" aria-expanded="false"
                   class="nav-link dropdown-toggle {{ (request()->is('tasks*')) ? 'active' : '' }}">Задачи</a>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('tasks', [
                            'checkedlawyer' => Auth::user()->id,
                            'calendar' => \App\Models\Enums\Tasks\DateInterval::Today->name
                        ])}}">Сегодня</a>
                    </li>
                </ul>
            </li>
            @can ('manage-services')
                <li class="nav-item"><a href="{{ route('services.index') }}" class="nav-link {{ (request()->is('services*')) ? 'active' : '' }}">Услуги</a></li>
            @endcan
            <li class="nav-item">
                <a href="{{ route('payments', [
                    'month' => \Carbon\Carbon::now()->format('m'),
                    'year' => \Carbon\Carbon::now()->format('Y'),
                ]) }}" class="nav-link {{ (request()->is('payments*')) ? 'active' : '' }}">Платежи</a>
            </li>
            <li class="nav-item"><a href="{{ route('lawyers') }}" class="nav-link {{ (request()->is('lawyers*')) ? 'active' : '' }}">Юристы</a></li>
            @can ('manage-users')
                <li class="nav-item"><a href="{{ route('users.index') }}" class="nav-link {{ (request()->is('users/*')) ? 'active' : '' }}">Пользователи</a></li>
            @endcan
        </ul>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a href="\" class="nav-link {{(request()->is('home*')) ? 'active' : '' }}">{{ Auth::user()->name }}</a>
            </li>
            <img src="{{ Auth::user()->getAvatar() }}" style="width: 40px; height:40px" class="mx-5 rounded-circle" alt="...">
            <li class="nav-item">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link">Выйти</button>
                </form>
            </li>
        </ul>
    </header>
</div>
