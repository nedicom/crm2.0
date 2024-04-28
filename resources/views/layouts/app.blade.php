<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    
    <script src="/js/jquery-3.7.1.min.js"></script>
    <link href="/js/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="/js/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    
    <!--bootstrap and jq composer
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>
    
    -->
    <script type="text/javascript" src="{{ asset('/resources/js/main.js') }}"></script>
    
    <link href="/style.css" rel="stylesheet">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml"/>

    @yield('head')
</head>

<body class="">
    <div class="row gx-0">
    @guest
        @include('inc.navguest')
    @endguest

    @auth

        <!--<div class="col-md-10 gx-0" style="min-height:1000px;background: linear-gradient(to right, #F0F8FF, #FFF);">
        --> 
            @include('inc.navauth')

            @include('inc.leftmenu')

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
            
        <!--</div>-->   
    @endauth

    @yield('content') {{--user register form--}}
    
</body>

@yield('footerscript')

</html>
