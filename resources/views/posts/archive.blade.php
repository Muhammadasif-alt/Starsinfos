@extends('layouts.app', ['seo' => [
    'title' => $category->name,
    'description' => $category->description,
    'image' => $category->image_url,
    'canonical' => $category->url(),
]])

@section('content')
    @include('partials.breadcrumbs', ['crumbs' => $category->parent
        ? ['Home' => route('home'), $root->name => $root->url(), $category->name => null]
        : ['Home' => route('home'), $category->name => null]])

    <h1 class="page-title">{{ $category->name }}</h1>
    @if ($category->description)
        <p class="lead">{{ $category->description }}</p>
    @endif

    @if ($category->image_url)
        <img class="banner" src="{{ $category->image_url }}" alt="{{ $category->name }}">
    @endif

    <div class="chips">
        <a href="{{ $root->url() }}" @class(['active' => ! $category->parent])>All</a>
        @foreach ($subcategories as $subcategory)
            <a href="{{ $subcategory->url() }}" @class(['active' => $subcategory->is($category)])>{{ $subcategory->name }}</a>
        @endforeach
    </div>

    @if ($category->showsDisclaimer())
        @include('partials.disclaimer')
    @endif

    <h2>Latest in {{ $category->name }}</h2>
    @if ($posts->isEmpty())
        <p class="empty">There are no posts in this category yet.</p>
    @else
        <div class="posts">
            @foreach ($posts as $post)
                @include('partials.post-card')
            @endforeach
        </div>
        {{ $posts->links() }}
    @endif
@endsection
