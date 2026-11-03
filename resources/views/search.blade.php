@extends('layouts.app', ['seo' => ['title' => $term !== '' ? 'Search: '.$term : 'All Posts']])

@push('schema')
    <meta name="robots" content="noindex,follow">
@endpush

@section('hero')
    <x-page-hero
        :title="$term !== '' ? 'Results for: '.$term : 'All Posts'"
        :crumbs="['Home' => route('home'), 'Search' => null]"
    >
        <form class="search" action="{{ route('search') }}" role="search">
            <input type="search" name="q" value="{{ $term }}" placeholder="Search jobs, schemes, style tips…" aria-label="Search">
            <button type="submit">Search</button>
        </form>
    </x-page-hero>
@endsection

@section('content')
    <div class="sh"><h2>Posts ({{ $posts->total() }})</h2></div>
    @if ($posts->isEmpty())
        <p class="empty">No posts found.</p>
    @else
        <div class="posts">
            @foreach ($posts as $post)
                @include('partials.post-card')
            @endforeach
        </div>
        {{ $posts->links() }}
    @endif

    <div class="sh"><h2>Career Advice ({{ $blogPosts->total() }})</h2></div>
    @include('blog.list', ['posts' => $blogPosts])
@endsection
