@extends('layouts.app', ['seo' => [
    'title' => $post->seo_title,
    'description' => $post->seo_description,
    'image' => $post->image_url,
    'canonical' => $post->url(),
    'type' => 'article',
]])

@php($isJob = $root->slug === 'jobs')

@push('schema')
    <script type="application/ld+json">@json(\App\Support\JsonLd::article($post))</script>
    @if ($isJob && ($jobPosting = \App\Support\JsonLd::jobPosting($post)))
        <script type="application/ld+json">@json($jobPosting)</script>
    @endif
@endpush

@section('hero')
    <x-page-hero
        :title="$post->title"
        :color="$post->category->color"
        variant="tint"
        :crumbs="array_merge(
            ['Home' => route('home'), $root->name => $root->url()],
            $post->category->parent ? [$post->category->name => $post->category->url()] : [],
            [$post->title => null],
        )"
    >
        <div class="meta">
            <a class="tag" style="--c:{{ $post->category->color }}" href="{{ $post->category->url() }}">{{ $post->category->name }}</a>
            <span>By {{ $post->author->name }}</span>
            <span>· Published {{ $post->published_at->format('j M Y') }}</span>
            <span>· Updated {{ $post->updated_at->format('j M Y') }}</span>
            @if ($post->is_outdated)
                <span class="badge-out">Outdated</span>
            @endif
        </div>
    </x-page-hero>
@endsection

@section('content')
    <div class="detail">
        <article class="detail__main">
            @if ($post->category->showsDisclaimer())
                @include('partials.disclaimer')
            @endif

            @if ($post->image_url)
                <img class="detail__banner" src="{{ $post->image_url }}" alt="{{ $post->title }}">
            @endif

            <div class="prose">{!! $post->safe_body !!}</div>

            @include('partials.post-extras')
            @include('partials.share', ['title' => $post->title, 'url' => $post->url()])
        </article>

        <aside class="detail__side">
            <div class="fact-card">
                <h2>{{ $isJob ? 'Job Summary' : 'Scheme Summary' }}</h2>
                <dl>
                    @if ($post->department)
                        <div><dt><x-site-icon name="landmark" :size="18" /> Organization</dt><dd>{{ $post->department }}</dd></div>
                    @endif
                    @if ($post->location)
                        <div><dt><x-site-icon name="home" :size="18" /> Location</dt><dd>{{ $post->location }}</dd></div>
                    @endif
                    @if ($post->job_type)
                        <div><dt><x-site-icon name="briefcase" :size="18" /> Job Type</dt><dd>{{ $post->job_type }}</dd></div>
                    @endif
                    <div><dt><x-site-icon :name="$post->category->icon" :size="18" /> Category</dt><dd>{{ $post->category->name }}</dd></div>
                    @if ($post->closing_date)
                        <div><dt><x-site-icon name="bell" :size="18" /> Last Date</dt><dd>{{ $post->closing_date->format('j M Y') }}</dd></div>
                    @endif
                    @if ($status = ($post->application_label ?? ($post->closing_date ? ($post->closing_label === 'Closed' ? 'Closed' : 'Open') : null)))
                        <div><dt><x-site-icon name="check" :size="18" /> Status</dt><dd><span @class(['status-pill', 'is-closed' => $status === 'Closed'])>{{ $status }}</span></dd></div>
                    @endif
                </dl>
                @if ($post->source_url)
                    <a class="button fact-card__cta" href="{{ $post->source_url }}" target="_blank" rel="nofollow noopener">Visit Official Website →</a>
                    <small>Source: {{ parse_url($post->source_url, PHP_URL_HOST) }}</small>
                @endif
            </div>

            @if ($related->isNotEmpty())
                <div class="side-list">
                    <h2>Related {{ $isJob ? 'Jobs' : 'Posts' }}</h2>
                    @foreach ($related as $relatedPost)
                        <a href="{{ $relatedPost->url() }}">
                            <span class="side-list__thumb" style="--c:{{ $relatedPost->category->color }}">
                                @if ($relatedPost->image_url)
                                    <img src="{{ $relatedPost->image_url }}" alt="" loading="lazy">
                                @endif
                            </span>
                            <span><b>{{ $relatedPost->title }}</b><small>{{ $relatedPost->closing_date ? $relatedPost->closing_label : $relatedPost->published_at->format('j M Y') }}</small></span>
                        </a>
                    @endforeach
                </div>
            @endif
        </aside>
    </div>
@endsection
