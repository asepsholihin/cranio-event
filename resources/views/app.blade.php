<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow" />

    <title>{{ config('app.name') }}</title>

    <!-- Splash Screen/Loader Styles -->
    <link rel="stylesheet" type="text/css" href="{{ asset(mix('css/loader.css')) }}" />

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset(mix('css/core.css')) }}">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <meta property="og:title" content="{{ config('app.name') }}" />
    <meta property="og:description" content="Sistem Manajemen Jejak Imani" />
    <meta property="og:url" content="https://sistemjejakimani.com/" />
    <meta property="og:image" content="{{ asset('logo.png') }}" />

    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}" />
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('logo.png') }}" />
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('logo.png') }}" />
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('logo.png') }}" />
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('logo.png') }}" />
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('logo.png') }}" />
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('logo.png') }}" />
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('logo.png') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo.png') }}" />

    <!-- Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap"
        rel="stylesheet">
</head>

<body>
    <noscript>
        <strong>We&lsquo;re sorry but you need to enable JavaScript to run this app.</strong>
    </noscript>
    <div id="loading-bg">
        <div class="loading-text">
            {{ config('app.name') }}<br />
            <center>{{ config('app.version') }}</center>
        </div>
        <div class="loading">
            <div class="effect-1 effects"></div>
            <div class="effect-2 effects"></div>
            <div class="effect-3 effects"></div>
        </div>
    </div>
    <div id="app">
    </div>

    <script src="{{ asset(mix('js/manifest.js')) }}"></script>
    <script src="{{ asset(mix('js/vendor.js')) }}"></script>
    <script src="{{ asset(mix('js/app.js')) }}"></script>

    @env('local')
    <script src="http://localhost:3000/browser-sync/browser-sync-client.js"></script>
    @endenv
</body>

</html>
