@extends('layouts.app', ['seo' => ['title' => 'Page not found']])

@section('content')
    <h1 class="page-title">Page not found</h1>
    <p class="lead">This page does not exist. Try a search or go back to the <a href="{{ route('home') }}" style="color:var(--p)">homepage</a>.</p>
    <form class="search" action="{{ route('search') }}" role="search">
        <input type="search" name="q" placeholder="Search jobs, BISP, admissions…" aria-label="Search">
        <button type="submit">Search</button>
    </form>
@endsection
