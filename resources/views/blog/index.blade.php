@extends('layouts.app', ['seo' => ['title' => 'Blog', 'canonical' => route('blog.index')]])

@section('content')
    @include('partials.breadcrumbs', ['crumbs' => ['Home' => route('home'), 'Blog' => null]])

    <h1 class="page-title">Blog</h1>

    <div class="chips">
        <a href="{{ route('blog.index') }}" @class(['active' => ! $activeCategory])>All</a>
        @foreach ($filters as $filter)
            <a href="{{ route('blog.index', ['category' => $filter->slug]) }}" @class(['active' => $activeCategory === $filter->slug])>{{ $filter->name }}</a>
        @endforeach
    </div>

    <h2>Latest Articles</h2>
    @include('blog.list')
@endsection
