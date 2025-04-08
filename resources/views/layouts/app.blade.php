<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" :class="{ 'theme-dark': dark }" x-data="data()">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow"/>
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}}</title>
    <wireui:scripts/>
    @vite('resources/css/app.css')
    @yield('styles')

    <script src="{{ asset('js/initalpine.js') }}"></script>
    @livewireStyles
</head>
<body class="font-sans antialiased light">
<x-notifications/>
@yield('page-content')
</body>
<footer>
    @livewireScriptConfig
    @vite('resources/js/app.js')
    @yield('scripts')
</footer>
</html>
