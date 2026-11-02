@extends('layouts.app', ['seo' => [
    'title' => $user->name,
    'description' => $user->bio,
    'canonical' => route('blog.author', $user),
]])

@section('hero')
    <x-page-hero
        :title="$user->name"
        :lead="$user->bio"
        variant="dark"
        :crumbs="['Home' => route('home'), 'Career Advice' => route('blog.index'), $user->name => null]"
    />
@endsection

@section('content')
    <div class="sh"><h2>Articles by {{ $user->name }}</h2></div>
    @include('blog.list')
@endsection
