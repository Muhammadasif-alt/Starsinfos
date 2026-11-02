@extends('layouts.app', ['seo' => ['title' => 'Page not found']])

@section('hero')
    <x-page-hero title="Page Not Found" lead="This page does not exist. Try a search or go back to the homepage.">
        <form class="search" action="{{ route('search') }}" role="search">
            <input type="search" name="q" placeholder="Search jobs, BISP, admissions…" aria-label="Search">
            <button type="submit">Search</button>
        </form>
    </x-page-hero>
@endsection

@section('content')
    <p class="empty" style="text-align:center"><a class="button" href="{{ route('home') }}">Back to Homepage</a></p>
@endsection
