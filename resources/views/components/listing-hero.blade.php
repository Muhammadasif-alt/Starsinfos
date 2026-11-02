@props(['eyebrow', 'title', 'lead', 'crumbs'])

<section class="jobs-hero" style="--img:url('{{ asset('images/jobs-hero.jpg') }}')">
    <div class="wrap">
        @include('partials.breadcrumbs', ['crumbs' => $crumbs])
        <span class="jobs-hero__eyebrow">{{ $eyebrow }}</span>
        <h1>{{ $title }}</h1>
        <p class="jobs-hero__lead">{{ $lead }}</p>
        {{ $slot }}
    </div>
</section>
