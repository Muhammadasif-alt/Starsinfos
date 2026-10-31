<header><div class="wrap bar">
    <a class="logo" href="{{ route('home') }}">{{ Str::beforeLast(config('app.name'), ' ') }}<span>{{ Str::contains(config('app.name'), ' ') ? Str::afterLast(config('app.name'), ' ') : '' }}</span></a>
    <button class="burger" type="button" aria-label="Menu" aria-controls="m" aria-expanded="false">☰</button>
    <nav id="m" aria-label="Main menu">
        @foreach ($menu as $item)
            <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
        @endforeach
    </nav>
</div></header>
