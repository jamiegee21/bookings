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
<body class="bg-zinc-50 text-zinc-900 min-h-dvh antialiased flex py-20 px-4 justify-center">

    <main class="w-full max-w-md">
        <div class="flex justify-center mb-8">
            <img src="{{ asset('/images/logo.svg') }}" alt="" class="max-w-48">
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-10">
            @yield('content')
        </div>
    </main>

    @livewireScripts
    @fluxScripts
</body>
</html>
