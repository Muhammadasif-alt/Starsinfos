@extends('layouts.app', ['seo' => [
    'title' => $category->name,
    'description' => $category->description,
    'canonical' => $category->url(),
]])

@section('hero')
    <x-listing-hero
        icon="landmark"
        image="home-hero.jpg"
        eyebrow="Government Schemes"
        :title="$category->name"
        lead="Loans, housing, solar, scholarships and welfare programs"
        :crumbs="$category->parent
            ? ['Home' => route('home'), $root->name => $root->url(), $category->name => null]
            : ['Home' => route('home'), $category->name => null]"
    >
        <form class="search" action="{{ route('search') }}" role="search">
            <input type="search" name="q" placeholder="Search government schemes…" aria-label="Search government schemes">
            <button type="submit">Search</button>
        </form>
    </x-listing-hero>

    <div class="wrap">
        <nav class="scheme-strip" aria-label="Popular scheme categories">
            @foreach ($subcategories->where('published_posts_count', '>', 0) as $subcategory)
                <a href="{{ $subcategory->url() }}" @class(['active' => $subcategory->is($category)])>
                    <x-site-icon :name="$subcategory->icon" :size="28" />{{ $subcategory->name }}
                </a>
            @endforeach
        </nav>
    </div>
@endsection

@section('content')
    @include('partials.disclaimer')

    <div class="jobs-layout">
        <nav class="jobs-filter scheme-cats" aria-label="Scheme categories">
            <h2>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 4h18l-7 9v6l-4 2v-8z"/></svg>
                Categories
            </h2>
            <a href="{{ $root->url() }}" @class(['active' => ! $category->parent])>
                <span>All Schemes</span><em>{{ $subcategories->sum('published_posts_count') }}</em>
            </a>
            @foreach ($subcategories->where('published_posts_count', '>', 0) as $subcategory)
                <a href="{{ $subcategory->url() }}" @class(['active' => $subcategory->is($category)])>
                    <span><x-site-icon :name="$subcategory->icon" :size="18" /> {{ $subcategory->name }}</span><em>{{ $subcategory->published_posts_count }}</em>
                </a>
            @endforeach
        </nav>

        <section>
            <div class="sh jobs-layout__head">
                <h2>{{ $category->parent ? $category->name : 'Latest Government Schemes' }}</h2>
                <span class="listing-count">{{ $posts->total() }} {{ Str::plural('scheme', $posts->total()) }}</span>
            </div>

            @forelse ($posts as $post)
                <a class="job-row" href="{{ $post->url() }}">
                    <span class="job-row__icon"><x-site-icon :name="$post->category->icon ?? $root->icon" :size="30" /></span>
                    <span class="job-row__body">
                        <b>{{ $post->title }}</b>
                        <span>{{ $post->excerpt }}</span>
                        <small>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4m8-4v4"/></svg>
                            Posted: {{ $post->published_at->format('j M Y') }} · {{ $post->category->name }}
                        </small>
                    </span>
                    @if ($status = $post->application_label)
                        <span @class(['status-pill', 'is-closed' => $status === 'Closed'])>{{ $status === 'Closed' ? '✕' : '✓' }} {{ $status }}</span>
                    @endif
                    <span class="job-row__go" aria-hidden="true">→</span>
                </a>
            @empty
                <p class="empty">There are no schemes in this category yet.</p>
            @endforelse

            {{ $posts->links() }}
        </section>
    </div>
@endsection
