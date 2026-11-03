@extends('layouts.app', ['seo' => [
    'title' => $category->parent ? $category->name : 'Latest Jobs in Pakistan',
    'description' => $category->description,
    'canonical' => $category->url(),
]])

@section('hero')
    <x-listing-hero
        icon="briefcase"
        image="home-hero.jpg"
        eyebrow="Career Opportunities"
        :title="$category->name"
        lead="Find Your Next Job in Pakistan"
        :crumbs="$category->parent
            ? ['Home' => route('home'), $root->name => $root->url(), $category->name => null]
            : ['Home' => route('home'), $category->name => null]"
    >
        <p>Explore the latest government jobs with last dates and official sources, and take the next step in your career.</p>
        <a class="button" href="{{ route('blog.index') }}">Career Advice →</a>
    </x-listing-hero>

    <div class="wrap">
        <form class="jobs-search" action="{{ $root->url() }}" role="search">
            <label class="jobs-search__q">
                <span class="sr-only">Search jobs</span>
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Search jobs… e.g. Assistant, Auditor, Officer">
            </label>
            <select name="department" aria-label="Department">
                <option value="">All Departments</option>
                @foreach ($options['departments'] as $department)
                    <option @selected($filters['department'] === $department)>{{ $department }}</option>
                @endforeach
            </select>
            <select name="location" aria-label="Location">
                <option value="">All Locations</option>
                @foreach ($options['locations'] as $location)
                    <option @selected($filters['location'] === $location)>{{ $location }}</option>
                @endforeach
            </select>
            <button type="submit">Search</button>

            <p class="jobs-search__divider"><span>Or explore by category</span></p>
            <div class="jobs-search__cats">
                @foreach ($subcategories as $subcategory)
                    <a href="{{ $subcategory->url() }}" @class(['active' => $subcategory->is($category)])><x-site-icon :name="$subcategory->icon" :size="18" />{{ $subcategory->name }}</a>
                @endforeach
            </div>
        </form>
    </div>
@endsection

@section('content')
    <div class="jobs-layout">
        <form class="jobs-filter" action="{{ $category->url() }}">
            <h2>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 4h18l-7 9v6l-4 2v-8z"/></svg>
                Filter Jobs
            </h2>
            @if ($filters['q'] !== '')
                <input type="hidden" name="q" value="{{ $filters['q'] }}">
            @endif

            <fieldset>
                <legend>Job Type</legend>
                @foreach (\App\Models\Post::JOB_TYPES as $type)
                    <label class="check"><input type="checkbox" name="type[]" value="{{ $type }}" @checked(in_array($type, $filters['type'], true))> {{ $type }}</label>
                @endforeach
            </fieldset>

            <label>Location
                <select name="location">
                    <option value="">Select Location</option>
                    @foreach ($options['locations'] as $location)
                        <option @selected($filters['location'] === $location)>{{ $location }}</option>
                    @endforeach
                </select>
            </label>

            <label>Department
                <select name="department">
                    <option value="">Select Department</option>
                    @foreach ($options['departments'] as $department)
                        <option @selected($filters['department'] === $department)>{{ $department }}</option>
                    @endforeach
                </select>
            </label>

            <button type="submit" class="button">Apply Filters</button>
            <a class="button button--ghost" href="{{ $category->url() }}">↻ Clear Filters</a>
        </form>

        <section>
            <div class="sh jobs-layout__head">
                <h2>{{ $category->parent ? $category->name : 'Latest Jobs' }}</h2>
                <span class="listing-count">{{ $posts->total() }} {{ Str::plural('job', $posts->total()) }}</span>
            </div>

            @forelse ($posts as $post)
                <a class="job-row" href="{{ $post->url() }}">
                    <span class="job-row__icon"><x-site-icon name="landmark-solid" :size="34" /></span>
                    <span class="job-row__body">
                        <b>{{ $post->title }}</b>
                        <span>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 21h18M5 21V10m14 11V10M4 10l8-6 8 6M9 21v-7h6v7"/></svg>
                            {{ $post->department ?? $post->category->name }}@if ($post->location) · {{ $post->location }}@endif @if ($post->job_type) · {{ $post->job_type }}@endif
                        </span>
                        <small>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4m8-4v4"/></svg>
                            Published: {{ $post->published_at->format('j M, Y') }}
                        </small>
                    </span>
                    @if ($post->closing_date)
                        <span @class(['job-row__date', 'is-closed' => $post->closing_label === 'Closed'])>
                            <small>{{ $post->closing_label === 'Closed' ? 'Closed on' : 'Last Date' }}:</small>
                            {{ $post->closing_date->format('j M, Y') }}
                        </span>
                    @else
                        <span class="job-row__date is-open"><small>Status:</small> Open</span>
                    @endif
                    <span class="job-row__go" aria-hidden="true">→</span>
                </a>
            @empty
                <p class="empty">No jobs match these filters. <a href="{{ $category->url() }}">Clear filters</a></p>
            @endforelse

            {{ $posts->links() }}
        </section>
    </div>

    <div class="jobs-cta">
        <span class="jobs-cta__icon"><x-site-icon name="handshake" :size="44" /></span>
        <div>
            <h2>Build Your Career</h2>
            <p>Read practical tips on CVs, applications and interviews.</p>
        </div>
        <a class="button button--ghost" href="{{ route('blog.index') }}">Career Advice →</a>
    </div>
@endsection
