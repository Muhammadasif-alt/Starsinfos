@extends('layouts.app', ['seo' => ['title' => $term !== '' ? 'Search: '.$term : 'All Posts']])

@push('schema')
    <meta name="robots" content="noindex,follow">
@endpush

@section('content')
    <h1 class="page-title">{{ $term !== '' ? 'Search results for "'.$term.'"' : 'All Posts' }}</h1>

    <form class="search" action="{{ route('search') }}" role="search">
        <input type="search" name="q" value="{{ $term }}" placeholder="Search jobs, BISP, admissions…" aria-label="Search">
        <button type="submit">Search</button>
    </form>

    <h2>Posts ({{ $posts->total() }})</h2>
    @if ($posts->isEmpty())
        <p class="empty">Koi post nahi mili.</p>
    @else
        <div class="posts">
            @foreach ($posts as $post)
                @include('partials.post-card')
            @endforeach
        </div>
        {{ $posts->links() }}
    @endif

    <h2>Blog ({{ $blogPosts->total() }})</h2>
    @include('blog.list', ['posts' => $blogPosts])
@endsection
