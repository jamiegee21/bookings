<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book – {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-zinc-50 text-zinc-900 h-dvh antialiased">
    <div class="max-w-2xl mx-auto px-4 py-8">
        <main>
            <div class="flex justify-center mb-8">
                <img src="{{ asset('/images/logo.svg') }}" alt="" class="max-w-48">
            </div>

            @yield('content')
        </main>
    </div>
    @livewireScripts
    @fluxScripts
</body>
</html>
