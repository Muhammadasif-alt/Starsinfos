@extends('layouts.app', ['seo' => ['title' => 'Page not found']])

@section('content')
    <h1 class="page-title">Page not found</h1>
    <p class="lead">Yeh page mojood nahi. Search karein ya <a href="{{ route('home') }}" style="color:var(--p)">homepage</a> par jayein.</p>
    <form class="search" action="{{ route('search') }}" role="search">
        <input type="search" name="q" placeholder="Search jobs, BISP, admissions…" aria-label="Search">
        <button type="submit">Search</button>
    </form>
@endsection
