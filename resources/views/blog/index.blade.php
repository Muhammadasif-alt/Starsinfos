@extends('layouts.app', ['seo' => [
    'title' => $activeTopic ? $topics[$activeTopic]['label'].' Tips' : 'Career Advice & Guidance',
    'description' => 'Expert tips on CV writing, job applications, interviews, career development and professional growth for job seekers in Pakistan.',
    'canonical' => $activeTopic ? route('blog.index', ['topic' => $activeTopic]) : route('blog.index'),
]])

@section('hero')
    <x-listing-hero
        icon="briefcase"
        eyebrow="Career Advice"
        title="Career Advice & Guidance"
        lead="Expert tips on CV writing, job applications, interviews, career development and professional growth."
        image="career-hero.jpg"
        :crumbs="['Home' => route('home'), 'Career Advice' => null]"
    >
        <form class="search" action="{{ route('blog.index') }}" role="search">
            <input type="search" name="q" value="{{ $term }}" placeholder="Search career guides, interview tips, CV writing…" aria-label="Search career advice">
            <button type="submit">Search</button>
        </form>
    </x-listing-hero>

    <div class="wrap">
        <nav class="scheme-strip" aria-label="Career advice topics">
            @foreach ($topics as $slug => $topic)
                <a href="{{ route('blog.index', ['topic' => $slug]) }}" @class(['active' => $activeTopic === $slug])>
                    <x-site-icon :name="$topic['icon']" :size="28" />{{ $topic['label'] }}
                </a>
            @endforeach
        </nav>
    </div>
@endsection

@section('content')
    @if ($featured->isNotEmpty())
        <section class="career-featured-wrap" aria-labelledby="featured-guides">
            <div class="sh jobs-layout__head"><h2 id="featured-guides">Featured Career Guides</h2></div>
            <div class="career-featured">
                @foreach ($featured as $post)
                    <a @class(['career-featured__item', 'is-lead' => $loop->first]) href="{{ $post->url() }}">
                        <span class="career-featured__img" style="--c:{{ $post->category?->color ?? '#0B6B4F' }}">
                            @if ($post->image_url)
                                <img src="{{ $post->image_url }}" alt="" @unless ($loop->first) loading="lazy" @endunless>
                            @endif
                        </span>
                        <span class="career-featured__body">
                            @if ($post->topic_label)
                                <span class="tag">{{ $post->topic_label }}</span>
                            @endif
                            <b>{{ $post->title }}</b>
                            @if ($loop->first)
                                <span class="career-featured__excerpt">{{ $post->excerpt }}</span>
                            @endif
                            <small>{{ $post->published_at->format('j M Y') }} · {{ $post->read_time }} min read</small>
                        </span>
                        <span class="job-row__go" aria-hidden="true">→</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <div class="jobs-layout">
        <form class="jobs-filter scheme-cats" action="{{ route('blog.index') }}">
            <h2>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 4h18l-7 9v6l-4 2v-8z"/></svg>
                Filter Articles
            </h2>
            <label class="sr-only" for="career-q">Search articles</label>
            <input id="career-q" class="filter-search" type="search" name="q" value="{{ $term }}" placeholder="Search articles…">
            @if ($activeTopic)
                <input type="hidden" name="topic" value="{{ $activeTopic }}">
            @endif

            <a href="{{ route('blog.index', array_filter(['q' => $term])) }}" @class(['active' => ! $activeTopic])>
                <span>All Career Advice</span><em>{{ $topicCounts->sum() }}</em>
            </a>
            @foreach ($topics as $slug => $topic)
                @if ($topicCounts->get($slug))
                    <a href="{{ route('blog.index', array_filter(['topic' => $slug, 'q' => $term])) }}" @class(['active' => $activeTopic === $slug])>
                        <span><x-site-icon :name="$topic['icon']" :size="18" /> {{ $topic['label'] }}</span><em>{{ $topicCounts->get($slug) }}</em>
                    </a>
                @endif
            @endforeach

            <button type="submit" class="button">Search</button>
            <a class="button button--ghost" href="{{ route('blog.index') }}">↻ Clear Filters</a>
        </form>

        <section>
            <div class="sh jobs-layout__head">
                <h2>{{ $activeTopic ? $topics[$activeTopic]['label'] : 'Latest Career Advice' }}</h2>
                <span class="listing-count">{{ $posts->total() }} {{ Str::plural('article', $posts->total()) }}</span>
            </div>

            @if ($posts->isEmpty())
                <p class="empty">No articles match your search. <a href="{{ route('blog.index') }}">Clear filters</a></p>
            @else
                <div class="career-grid">
                    @foreach ($posts as $post)
                        @include('partials.blog-card')
                    @endforeach
                </div>
                {{ $posts->links() }}
            @endif
        </section>
    </div>

    @if ($resources->isNotEmpty())
        <section class="career-resources" aria-labelledby="career-resources">
            <h2 id="career-resources"><x-site-icon name="book" /> Career Resources</h2>
            <div>
                @foreach ($resources as $resource)
                    <a href="{{ $resource['post']->url() }}">
                        <span class="job-card__icon"><x-site-icon :name="$resource['icon']" /></span>
                        <span><b>{{ $resource['title'] }}</b><small>{{ $resource['text'] }}</small></span>
                        <span aria-hidden="true">→</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <div class="jobs-cta">
        <span class="jobs-cta__icon"><x-site-icon name="award" :size="44" /></span>
        <div>
            <h2>Take the Next Step in Your Career</h2>
            <p>Explore the latest job opportunities and prepare for your professional future.</p>
        </div>
        <a class="button button--gold" href="{{ url('jobs') }}">Explore Latest Jobs →</a>
    </div>
@endsection
