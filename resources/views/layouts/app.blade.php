<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if( Auth::check() )
    <meta name="token" content="{{ auth()->user()->api_token }}">

    <script>
        window.INITIAL_STATE = {!! isset($initial_state) ? $initial_state->toJson() : '{}' !!}
    </script>
    @endif

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        @include('partials.nav')

        @yield('content')
        @if( Auth::check() )


        <item-detail></item-detail>
        <in-out-board></in-out-board>
        <flash message="{{ session('flash') }}"></flash>
        @endif
    </div>


    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
