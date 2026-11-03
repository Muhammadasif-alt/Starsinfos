@extends('layouts.app', ['seo' => [
    'title' => $page->title,
    'description' => $page->excerpt,
    'canonical' => $page->url(),
]])

@section('hero')
    @if ($page->slug === 'about-us')
        <x-listing-hero icon="users" :eyebrow="'About '.config('app.name')" :title="$page->title" :lead="$page->excerpt" :crumbs="['Home' => route('home'), $page->title => null]" />
    @elseif ($page->slug === 'contact-us')
        <x-listing-hero icon="mail" eyebrow="We are here to help" :title="$page->title" :lead="$page->excerpt" :crumbs="['Home' => route('home'), $page->title => null]" />
    @else
    <x-page-hero :title="$page->title" :lead="$page->excerpt" :crumbs="['Home' => route('home'), $page->title => null]" />
    @endif
@endsection

@section('content')
    @include(match ($page->slug) {
        'about-us' => 'pages.about',
        'contact-us' => 'pages.contact',
        default => 'pages.default',
    })
@endsection
