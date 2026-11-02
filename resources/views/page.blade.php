@extends('layouts.app', ['seo' => [
    'title' => $page->title,
    'description' => $page->excerpt,
    'canonical' => $page->url(),
]])

@section('hero')
    <x-page-hero :title="$page->title" :lead="$page->excerpt" :crumbs="['Home' => route('home'), $page->title => null]" />
@endsection

@section('content')
    @include(match ($page->slug) {
        'about-us' => 'pages.about',
        'contact-us' => 'pages.contact',
        default => 'pages.default',
    })
@endsection
