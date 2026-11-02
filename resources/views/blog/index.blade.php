@extends('layouts.app', ['seo' => [
    'title' => 'Blog',
    'description' => 'Practical guides and tips on jobs, government schemes, education and home decor.',
    'canonical' => route('blog.index'),
]])

@section('hero')
    <x-page-hero
        title="Our Blog"
        lead="Practical guides and tips on jobs, government schemes, education and home decor."
        variant="dark"
        :crumbs="['Home' => route('home'), 'Blog' => null]"
    >
        <div class="chips">
            <a href="{{ route('blog.index') }}" @class(['active' => ! $activeCategory])>All</a>
            @foreach ($filters as $filter)
                <a href="{{ route('blog.index', ['category' => $filter->slug]) }}" @class(['active' => $activeCategory === $filter->slug])>{{ $filter->name }}</a>
            @endforeach
        </div>
    </x-page-hero>
@endsection

@section('content')
    <div class="sh"><h2>Latest Articles</h2></div>
    @include('blog.list')
@endsection
