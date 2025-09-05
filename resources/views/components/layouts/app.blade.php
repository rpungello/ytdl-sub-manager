<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:main :container="true">
            {{ $slot }}
        </flux:main>

        @fluxScripts
        <flux:toast />
    </body>
</html>
