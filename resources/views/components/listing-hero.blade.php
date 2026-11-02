@props(['icon', 'eyebrow', 'title', 'lead', 'crumbs', 'image' => 'schemes-hero.jpg'])

<section class="jobs-hero" style="--img:url('{{ asset('images/'.$image) }}')">
    <div class="wrap">
        @include('partials.breadcrumbs', ['crumbs' => $crumbs])
        <span class="jobs-hero__eyebrow"><x-site-icon :name="$icon" :size="18" /> {{ $eyebrow }}</span>
        <h1>{{ $title }}</h1>
        <p class="jobs-hero__lead">{{ $lead }}</p>
        {{ $slot }}
    </div>
</section>
