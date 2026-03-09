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
<body class="bg-zinc-50 text-zinc-900 min-h-dvh antialiased flex py-20 justify-center">
    <header class="mb-8 flex justify-between items-center">
        <a href="{{ route('dashboard') }}" class="inline-block">
            <img src="{{ asset('/images/logo.svg') }}" alt="" class="w-32">
        </a>
{{--            @auth--}}
{{--                <div class="flex gap-2 text-sm items-center">--}}
{{--                    <a href="{{ route('dashboard') }}">--}}
{{--                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">--}}
{{--                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />--}}
{{--                        </svg>--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--            @endauth--}}
    </header>
    <main class="w-full max-w-md">
        @yield('content')
    </main>
    <flux:toast />
    @livewireScripts
    @fluxScripts
</body>
</html>
