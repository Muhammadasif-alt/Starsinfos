@extends('layouts.app', ['seo' => [
    'title' => $category->name,
    'description' => $category->description,
    'image' => $category->image_url,
    'canonical' => $category->url(),
]])

@section('hero')
    <x-page-hero
        :title="$category->name"
        :lead="$category->description"
        variant="dark"
        :crumbs="$category->parent
            ? ['Home' => route('home'), $root->name => $root->url(), $category->name => null]
            : ['Home' => route('home'), $category->name => null]"
    >
        <form class="search" action="{{ route('search') }}" role="search">
            <input type="search" name="q" placeholder="Search {{ Str::lower($root->name) }}…" aria-label="Search">
            <button type="submit">Search</button>
        </form>
    </x-page-hero>
@endsection

@section('content')
    @if ($category->showsDisclaimer())
        @include('partials.disclaimer')
    @endif

    <div class="listing">
        <aside class="listing-nav" aria-label="{{ $root->name }} categories">
            <h2>Categories</h2>
            <a href="{{ $root->url() }}" @class(['active' => ! $category->parent])>All {{ $root->name }}</a>
            @foreach ($subcategories as $subcategory)
                <a href="{{ $subcategory->url() }}" @class(['active' => $subcategory->is($category)])>{{ $subcategory->name }}</a>
            @endforeach
        </aside>

        <section>
            <p class="listing-count">{{ $posts->total() }} {{ Str::plural('listing', $posts->total()) }}</p>

            @forelse ($posts as $post)
                <a class="listing-row" href="{{ $post->url() }}">
                    <span class="job-card__icon">{{ $post->category->icon ?? $root->icon }}</span>
                    <span class="listing-row__body">
                        <b>{{ $post->title }}</b>
                        <span>{{ $post->excerpt }}</span>
                        <small>{{ $post->category->name }} · Posted {{ $post->published_at->format('j M Y') }}</small>
                    </span>
                    @if ($post->closing_date)
                        <em @class(['job-card__badge', 'is-closed' => $post->closing_label === 'Closed'])>{{ $post->closing_label }}</em>
                    @endif
                    <span class="listing-row__arrow" aria-hidden="true">→</span>
                </a>
            @empty
                <p class="empty">There are no listings in this category yet.</p>
            @endforelse

            {{ $posts->links() }}
        </section>
    </div>
@endsection
