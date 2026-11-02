<header><div class="wrap bar">
    <a class="logo" href="{{ route('home') }}"><img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" width="180" height="48"></a>
    <button class="burger" type="button" aria-label="Menu" aria-controls="m" aria-expanded="false">☰</button>
    <nav id="m" aria-label="Main menu">
        @foreach ($menu as $item)
            <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
        @endforeach
    </nav>
</div></header>
