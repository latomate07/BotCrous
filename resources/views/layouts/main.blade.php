<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>BotCrous</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
        <script
        src="https://code.jquery.com/jquery-3.6.2.js"
        integrity="sha256-pkn2CUZmheSeyssYw3vMp1+xyub4m+e+QK4sQskvuo4="
        crossorigin="anonymous"></script>
        <script src="{{ asset('assets/js/script.js') }}" defer></script>
    </head>
    <body>
        @include('partials.loading')
        <div id="app">
            @yield('content')
        </div>
        <div id="overlay"></div>
        <img id="botLogoOnMobile" src="{{ asset('storage/svgs/bot-svg.svg') }}" width="50px" style="display:none">
    </body>
</html>
