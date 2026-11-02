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
        :image="$category->image_url"
        :color="$category->color"
        variant="tint"
        :crumbs="$category->parent
            ? ['Home' => route('home'), $root->name => $root->url(), $category->name => null]
            : ['Home' => route('home'), $category->name => null]"
    >
        <div class="chips">
            <a href="{{ $root->url() }}" @class(['active' => ! $category->parent])>All</a>
            @foreach ($subcategories as $subcategory)
                <a href="{{ $subcategory->url() }}" @class(['active' => $subcategory->is($category)])>{{ $subcategory->name }}</a>
            @endforeach
        </div>
    </x-page-hero>
@endsection

@section('content')
    @if ($category->showsDisclaimer())
        @include('partials.disclaimer')
    @endif

    <div class="sh"><h2>Latest in {{ $category->name }}</h2></div>
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
