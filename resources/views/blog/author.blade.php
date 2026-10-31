@extends('layouts.app', ['seo' => [
    'title' => $user->name,
    'description' => $user->bio,
    'canonical' => route('blog.author', $user),
]])

@section('content')
    @include('partials.breadcrumbs', ['crumbs' => ['Home' => route('home'), 'Blog' => route('blog.index'), $user->name => null]])

    <div class="author-box">
        @include('partials.avatar', ['user' => $user])
        <div>
            <h1 style="margin:0 0 4px;font-size:22px">{{ $user->name }}</h1>
            <p>{{ $user->bio }}</p>
        </div>
    </div>

    @include('blog.list')
@endsection
