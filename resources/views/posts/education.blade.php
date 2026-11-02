@extends('layouts.app', ['seo' => [
    'title' => $category->name,
    'description' => $category->description,
    'canonical' => $category->url(),
]])

@section('hero')
    <x-listing-hero
        icon="graduation"
        image="education-hero.jpg"
        eyebrow="Education & Admissions"
        :title="$category->name"
        lead="Your gateway to quality education and higher learning in Pakistan"
        :crumbs="$category->parent
            ? ['Home' => route('home'), $root->name => $root->url(), $category->name => null]
            : ['Home' => route('home'), $category->name => null]"
    >
        <form class="search" action="{{ route('search') }}" role="search">
            <input type="search" name="q" placeholder="Search scholarships, admissions or programs…" aria-label="Search education">
            <button type="submit">Search</button>
        </form>
    </x-listing-hero>

    <div class="wrap">
        <nav class="scheme-strip" aria-label="Education categories">
            @foreach ($subcategories as $subcategory)
                <a href="{{ $subcategory->url() }}" @class(['active' => $subcategory->is($category)])>
                    <x-site-icon :name="$subcategory->icon" :size="28" />{{ $subcategory->name }}
                </a>
            @endforeach
        </nav>
    </div>
@endsection

@section('content')
    @unless ($category->parent)
        <div class="edu-cards">
            @foreach ($highlights as $highlight)
                <a class="edu-card" href="{{ $highlight['category']->url() }}">
                    <span class="edu-card__icon"><x-site-icon :name="$highlight['category']->icon" :size="30" /></span>
                    <h2>{{ $highlight['title'] }}</h2>
                    <p>{{ $highlight['text'] }}</p>
                    <span class="edu-card__cta">{{ $highlight['cta'] }} →</span>
                </a>
            @endforeach
        </div>
    @endunless

    <div class="edu-grid">
        <section class="edu-updates" aria-labelledby="edu-updates-title">
            <header>
                <h2 id="edu-updates-title"><x-site-icon name="bell" /> {{ $category->parent ? $category->name : 'Latest Updates' }}</h2>
                <span>{{ $posts->total() }} {{ Str::plural('update', $posts->total()) }}</span>
            </header>
            @forelse ($posts as $post)
                <a class="edu-update" href="{{ $post->url() }}">
                    <span class="edu-update__date"><b>{{ $post->published_at->format('j') }}</b>{{ $post->published_at->format('M') }}</span>
                    <span class="edu-update__body">
                        <b>{{ $post->title }}</b>
                        <small>
                            {{ $post->category->name }}
                            @if ($post->closing_date)
                                · {{ $post->closing_label === 'Closed' ? 'Closed' : 'Apply before '.$post->closing_date->format('j M Y') }}
                            @endif
                        </small>
                    </span>
                    <span class="edu-update__go" aria-hidden="true">›</span>
                </a>
            @empty
                <p class="empty">There are no updates in this category yet.</p>
            @endforelse
            {{ $posts->links() }}
        </section>

        <section class="edu-calendar" aria-labelledby="edu-calendar-title">
            <div class="sh jobs-layout__head">
                <h2 id="edu-calendar-title">Admissions Calendar</h2>
            </div>
            @if ($admissions->isEmpty())
                <p class="empty">No upcoming admission dates right now.</p>
            @else
                <div class="tw"><table>
                    <tr><th>Program</th><th>Type</th><th>Last Date</th><th>Entry Test</th></tr>
                    @foreach ($admissions as $admission)
                        <tr>
                            <td>@if ($admission->link)<a href="{{ $admission->link }}" target="_blank" rel="nofollow noopener">{{ $admission->program }}</a>@else{{ $admission->program }}@endif</td>
                            <td>{{ $admission->type }}</td>
                            <td>{{ $admission->last_date->format('j M Y') }}</td>
                            <td>{{ $admission->test_date?->format('j M Y') ?? 'No test' }}</td>
                        </tr>
                    @endforeach
                </table></div>
            @endif
        </section>
    </div>

    @if ($scholarships)
        <div class="jobs-cta">
            <span class="jobs-cta__icon"><x-site-icon name="book" :size="44" /></span>
            <div>
                <h2>Build a Brighter Future Through Education</h2>
                <p>Explore scholarships, admissions and entry test guides in one place.</p>
            </div>
            <a class="button button--gold" href="{{ $scholarships->url() }}">Explore Scholarships →</a>
        </div>
    @endif
@endsection
