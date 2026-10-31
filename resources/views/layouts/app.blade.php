<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    @include('partials.seo', $seo ?? [])
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('schema')
</head>
<body>
    @include('partials.header')

    @yield('hero')

    <main class="wrap">
        @yield('content')

        @include('partials.newsletter')
    </main>

    @include('partials.footer')
</body>
</html>
