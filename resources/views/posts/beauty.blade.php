@extends('layouts.app', ['seo' => [
    'title' => $category->parent ? $category->name.' Tips' : 'Beauty & Style Tips',
    'description' => $category->description ?? $root->description,
    'image' => $category->image_url,
    'canonical' => $category->url(),
]])

@section('hero')
    <x-listing-hero
        icon="sparkles"
        eyebrow="Beauty & Style"
        :title="$category->name"
        :lead="$category->parent ? $category->name.' tips, trends and easy routines' : 'Skincare, makeup, hair, nails and fashion tips for everyday life in Pakistan'"
        image="beauty-hero.jpg"
        :crumbs="$category->parent
            ? ['Home' => route('home'), $root->name => $root->url(), $category->name => null]
            : ['Home' => route('home'), $category->name => null]"
    >
        <form class="search" action="{{ route('search') }}" role="search">
            <input type="search" name="q" placeholder="Search skincare, makeup, hair tips…" aria-label="Search beauty and style">
            <button type="submit">Search</button>
        </form>
    </x-listing-hero>

    <div class="wrap">
        <nav class="scheme-strip beauty-strip" aria-label="Beauty and style topics">
            <a href="{{ $root->url() }}" @class(['active' => ! $category->parent])><x-site-icon name="sparkles" :size="26" />All</a>
            @foreach ($subcategories as $subcategory)
                <a href="{{ $subcategory->url() }}" @class(['active' => $subcategory->is($category)])>
                    <x-site-icon :name="$subcategory->icon" :size="26" />{{ $subcategory->name }}
                </a>
            @endforeach
        </nav>
    </div>
@endsection

@section('content')
    @unless ($category->parent)
        <section class="beauty-topics" aria-labelledby="beauty-topics">
            <div class="sh jobs-layout__head"><h2 id="beauty-topics">Explore Topics</h2></div>
            <div class="beauty-tiles">
                @foreach ($subcategories as $subcategory)
                    <a class="beauty-tile" href="{{ $subcategory->url() }}">
                        <span class="beauty-tile__img">
                            @if ($subcategory->image_url)
                                <img src="{{ $subcategory->image_url }}" alt="{{ $subcategory->name }}" loading="lazy">
                            @else
                                <x-site-icon :name="$subcategory->icon" :size="44" />
                            @endif
                        </span>
                        <span class="beauty-tile__body">
                            <b>{{ $subcategory->name }}</b>
                            <small>{{ $subcategory->published_posts_count }} {{ Str::plural('article', $subcategory->published_posts_count) }}</small>
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endunless

    <section aria-labelledby="beauty-latest">
        <div class="sh jobs-layout__head">
            <h2 id="beauty-latest">{{ $category->parent ? $category->name : 'Latest Beauty & Style' }}</h2>
            <span class="listing-count">{{ $posts->total() }} {{ Str::plural('article', $posts->total()) }}</span>
        </div>

        @if ($posts->isEmpty())
            <div class="beauty-empty">
                <x-site-icon name="sparkles" :size="40" />
                <h3>New {{ Str::lower($category->name) }} articles are coming soon</h3>
                <p>We are preparing simple, practical tips. Meanwhile, explore our other sections.</p>
                <div class="cta-band__actions">
                    <a class="button" href="{{ url('jobs') }}">Browse Jobs</a>
                    <a class="button button--ghost" href="{{ route('blog.index') }}">Career Advice</a>
                </div>
            </div>
        @else
            <div class="posts">
                @foreach ($posts as $post)
                    @include('partials.post-card')
                @endforeach
            </div>
            {{ $posts->links() }}
        @endif
    </section>
@endsection
