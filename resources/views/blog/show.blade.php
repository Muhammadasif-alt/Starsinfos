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

@section('hero')
    <x-page-hero
        :title="$post->title"
        :lead="$post->excerpt"
        variant="dark"
        :crumbs="['Home' => route('home'), 'Career Advice' => route('blog.index'), $post->title => null]"
    >
        <div class="meta">
            @include('partials.avatar', ['user' => $post->author])
            <a href="{{ route('blog.author', $post->author) }}">{{ $post->author->name }}</a>
            <span>· {{ $post->published_at->format('j M Y') }}</span>
            <span>· {{ $post->read_time }} min read</span>
        </div>
    </x-page-hero>
@endsection

@section('content')
    <div class="detail">
        <article class="detail__main">
            @if ($post->image_url)
                <img class="detail__banner" src="{{ $post->image_url }}" alt="{{ $post->title }}">
            @endif

            <div class="prose">{!! $post->safe_body !!}</div>

            @include('partials.post-extras')
            @include('partials.share', ['title' => $post->title, 'url' => $post->url()])
        </article>

        <aside class="detail__side">
            <div class="fact-card author-card">
                @include('partials.avatar', ['user' => $post->author])
                <h2><a href="{{ route('blog.author', $post->author) }}">{{ $post->author->name }}</a></h2>
                <p>{{ $post->author->bio }}</p>
                <a class="button button--ghost fact-card__cta" href="{{ route('blog.author', $post->author) }}">More from {{ $post->author->name }}</a>
            </div>

            @if ($related->isNotEmpty())
                <div class="side-list">
                    <h2>More Career Advice</h2>
                    @foreach ($related as $relatedPost)
                        <a href="{{ $relatedPost->url() }}">
                            <span class="side-list__thumb" style="--c:{{ $relatedPost->category?->color ?? '#0B6B4F' }}">
                                @if ($relatedPost->image_url)
                                    <img src="{{ $relatedPost->image_url }}" alt="" loading="lazy">
                                @endif
                            </span>
                            <span><b>{{ $relatedPost->title }}</b><small>{{ $relatedPost->read_time }} min read</small></span>
                        </a>
                    @endforeach
                </div>
            @endif

            <a class="side-cta" href="{{ url('jobs') }}">
                <x-site-icon name="briefcase" :size="28" />
                <span><b>Looking for a job?</b><small>Browse the latest jobs with last dates.</small></span>
            </a>
        </aside>
    </div>
@endsection
