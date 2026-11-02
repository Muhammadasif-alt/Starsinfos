@extends('layouts.app', ['seo' => [
    'title' => 'Career Advice',
    'description' => 'Practical tips on CVs, job applications, interviews and entry tests.',
    'canonical' => route('blog.index'),
]])

@section('hero')
    <x-page-hero
        title="Career Advice"
        lead="Practical tips on CVs, job applications, interviews and entry tests."
        variant="dark"
        :crumbs="['Home' => route('home'), 'Career Advice' => null]"
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
