@props(['title', 'lead' => null, 'crumbs' => [], 'variant' => 'light', 'image' => null, 'color' => null])

<section @class(['page-hero', "page-hero--{$variant}", 'page-hero--split' => $image]) @if ($color) style="--c:{{ $color }}" @endif>
    <div class="wrap page-hero__inner">
        <div class="page-hero__copy">
            @if ($crumbs)
                @include('partials.breadcrumbs', ['crumbs' => $crumbs])
            @endif
            <h1>{{ $title }}</h1>
            @if ($lead)
                <p class="page-hero__lead">{{ $lead }}</p>
            @endif
            {{ $slot }}
        </div>
        @if ($image)
            <img class="page-hero__image" src="{{ $image }}" alt="{{ $title }}">
        @endif
    </div>
</section>
