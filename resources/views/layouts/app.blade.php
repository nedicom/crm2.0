<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="{{ asset('/resources/js/main.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link href="/style.css" rel="stylesheet">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml"/>

    @yield('head')
</head>

<body class="fw-light">
    @guest
        @include('inc.navguest')
    @endguest

    @auth
        @include('inc.leftmenu')

        <div style="margin-left: 13%; width: 86%;min-height:1000px;background: linear-gradient(to right, #F0F8FF, #FFF);">
            @include('inc.navauth')
            @include('inc.maincontent')

            @if (request()->is('clients/*'))
                @include('inc./modal/editclient')
            @endif

            @if (request()->is('services'))
                @include('inc./modal/addservice')
            @endif

            @if (request()->is('payments'))
                @include('inc./modal/addpayment')
            @endif

            @if (request()->is('payments/*'))
                @include('inc./modal/editpayment')
            @endif

            @if (request()->is('meetings'))
                @include('inc./modal/addmeeting')
            @endif

            @if (request()->is('meetings/*'))
                @include('inc./modal/editmeeting')
            @endif

            @if (request()->is('tasks/*'))
                @include('inc./modal/edittask')
            @endif

            @include('inc/messages')
    @endauth

    @yield('content') {{--user register form--}}
</body>

@yield('footerscript')

</html>
