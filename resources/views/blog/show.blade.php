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
    @include('partials.breadcrumbs', ['crumbs' => ['Home' => route('home'), 'Blog' => route('blog.index'), $post->title => null]])

    <article class="article">
        <h1 class="page-title">{{ $post->title }}</h1>
        <div class="meta">
            @include('partials.avatar', ['user' => $post->author])
            <a href="{{ route('blog.author', $post->author) }}">{{ $post->author->name }}</a>
            <span>· {{ $post->published_at->format('j M Y') }}</span>
            <span>· {{ $post->read_time }} min read</span>
        </div>

        @if ($post->image_url)
            <img class="banner" src="{{ $post->image_url }}" alt="{{ $post->title }}">
        @endif

        <div class="prose">{!! $post->safe_body !!}</div>

        @include('partials.share', ['title' => $post->title, 'url' => $post->url()])

        <div class="author-box">
            @include('partials.avatar', ['user' => $post->author])
            <div>
                <h4><a href="{{ route('blog.author', $post->author) }}">{{ $post->author->name }}</a></h4>
                <p>{{ $post->author->bio }}</p>
            </div>
        </div>
    </article>

    @if ($related->isNotEmpty())
        <div class="sh"><h2>Related Articles</h2></div>
        <div class="bl">
            @foreach ($related as $post)
                @include('partials.blog-row')
            @endforeach
        </div>
    @endif
@endsection
