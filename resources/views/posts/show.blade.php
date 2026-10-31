@extends('layouts.app', ['seo' => [
    'title' => $post->seo_title,
    'description' => $post->seo_description,
    'image' => $post->image_url,
    'canonical' => $post->url(),
    'type' => 'article',
]])

@push('schema')
    <script type="application/ld+json">@json(\App\Support\JsonLd::article($post))</script>
@endpush

@section('content')
    @include('partials.breadcrumbs', ['crumbs' => array_merge(
        ['Home' => route('home'), $root->name => $root->url()],
        $post->category->parent ? [$post->category->name => $post->category->url()] : [],
        [$post->title => null],
    )])

    <article class="article">
        <h1 class="page-title">{{ $post->title }}</h1>
        <div class="meta">
            @include('partials.avatar', ['user' => $post->author])
            <span>{{ $post->author->name }}</span>
            <span>· Published {{ $post->published_at->format('j M Y') }}</span>
            <span>· Last updated {{ $post->updated_at->format('j M Y') }}</span>
            @if ($post->is_outdated)
                <span class="badge-out">Outdated</span>
            @endif
        </div>

        @if ($post->category->showsDisclaimer())
            @include('partials.disclaimer')
        @endif

        @if ($post->closing_date)
            <div @class(['closing', 'closed' => $post->closing_date->isPast() && ! $post->closing_date->isToday()])>
                <b>Closing date:</b> {{ $post->closing_date->format('j M Y') }}
                @if ($post->closing_date->isPast() && ! $post->closing_date->isToday()) (closed) @endif
            </div>
        @endif

        @if ($post->image_url)
            <img class="banner" src="{{ $post->image_url }}" alt="{{ $post->title }}">
        @endif

        <div class="prose">{!! $post->safe_body !!}</div>

        @if ($post->source_url)
            <p class="source"><b>Source:</b> <a href="{{ $post->source_url }}" target="_blank" rel="nofollow noopener">{{ parse_url($post->source_url, PHP_URL_HOST) }}</a></p>
        @endif

        @include('partials.share', ['title' => $post->title, 'url' => $post->url()])
    </article>

    @if ($related->isNotEmpty())
        <div class="sh"><h2>Related Posts</h2></div>
        <div class="posts">
            @foreach ($related as $relatedPost)
                @include('partials.post-card', ['post' => $relatedPost])
            @endforeach
        </div>
    @endif
@endsection
