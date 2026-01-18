<nav class="navbar navbar-expand-lg navbar-dark bd-navbar shadow">

        <a href="{{ route('home') }}" class="navbar-brand col-md-3 col-lg-2 me-0 px-3 text-center {{ (request()->is('home*')) ? 'text-white' : 'text-white-50' }}" aria-current="page">
            <i class="bi-house"></i>
        </a>

        <button class="navbar-toggler my-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
            
            <ul class="navbar-nav flex-row flex-wrap me-auto my-2 my-lg-0 px-3">            

                <li class="nav-item dropdown col-6 col-md-auto">
                    <a href="{{route('leads.filter', 'new')}}" data-bs-toggle="dropdown" role="button" aria-expanded="false"
                        class="nav-link dropdown-toggle {{ (request()->is('leads*')) ? 'active' : '' }}">Лиды
                    </a>

                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{route('leads.filter', 'new')}}">Лиды</a></li>
                        <li><a class="dropdown-item" href="{{route('leadanalitics')}}">Аналитика</a></li>
                        <li><a class="dropdown-item" href="{{route('avito.chats')}}">Авито</a></li>
                    </ul>
                </li>

                <li class="nav-item  dropdown col-6 col-md-auto">
                    <a href="#" data-bs-toggle="dropdown" role="button" aria-expanded="false"
                    class="nav-link dropdown-toggle {{ (request()->is('clients*')) ? 'active' : '' }} {{ (request()->is('dogovor*')) ? 'active' : '' }}">Клиенты</a>
                    <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('clients') }}?checkedlawyer={{Auth::user()->id}}&status=1">Все клиенты</a></li>
                    <li><a class="dropdown-item" href="{{ route('contracts.index') }}">Договоры</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown col-6 col-md-auto">
                    <a href="#" data-bs-toggle="dropdown" role="button" aria-expanded="false"
                    class="nav-link dropdown-toggle {{ (request()->is('tasks*')) ? 'active' : '' }}">Задачи</a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('tasks', [
                                'checkedlawyer' => Auth::user()->id,
                                'calendar' => \App\Models\Enums\Tasks\DateInterval::Today->name
                            ])}}">Сегодня</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('tasks', [
                                'checkedlawyer' => Auth::user()->id,
                                'calendar' => \App\Models\Enums\Tasks\DateInterval::Month->name
                            ])}}">Месяц</a>
                        </li>
                    </ul>
                </li>

                @can ('manage-services')
                    <li class="nav-item col-6 col-md-auto"><a href="{{ route('services.index') }}" class="nav-link {{ (request()->is('services*')) ? 'active' : '' }}">Услуги</a></li>
                @endcan

                <li class="nav-item col-6 col-md-auto">
                    <a href="{{ route('payments', [
                    'month' => \Carbon\Carbon::now()->format('m')
                    ]) }}" class="nav-link {{ (request()->is('payments*')) ? 'active' : '' }}">Платежи</a>
                </li>

                @can ('manage-users')
                    <li class="nav-item col-6 col-md-auto">
                        <a href="{{ route('users.index') }}" class="nav-link {{ (request()->is('users*')) ? 'active' : '' }}">
                            Пользователи
                        </a>
                    </li>
                @endcan
                   
                <hr class="d-md-none text-white">

            </ul>

            <ul class="navbar-nav flex-row flex-wrap me-auto my-2 my-lg-0 px-3">
                <li class="nav-item dropdown float-left col-6 col-md-auto">
                    <a 
                        class="nav-link dropdown-toggle {{ (request()->is('home*')) ? 'active' : '' }}"
                        href="#" id="navbarScrollingDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">                  
                        <img src="{{ Auth::user()->getAvatar() }}" style="width: 40px; height:40px" class="rounded-circle" alt="...">
                    </a>
                
                    <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
                        <li>                
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="nav-link text-black">Выйти</button>
                            </form>                       
                        </li>
                    </ul>

                </li>
            </ul>
        </div>
    
</nav> 