<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="h-screen antialiased">
        <div class="mx-auto h-full max-w-4xl p-4 sm:p-6">
            {{ $slot }}
        </div>
    @fluxScripts
</body>

</html>
