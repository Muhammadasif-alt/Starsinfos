@extends('layouts.app', ['seo' => ['description' => $settings['hero_text'] ?? null]])

@push('schema')
    @if ($faqs->isNotEmpty())
        <script type="application/ld+json">@json(\App\Support\JsonLd::faq($faqs))</script>
    @endif
@endpush

@section('hero')
    <section class="hero"><div class="wrap hero__inner">
        <div>
            <h1>{{ $settings['hero_title'] ?? config('app.name') }}</h1>
            <p>{{ $settings['hero_text'] ?? '' }}</p>
            <form class="search" action="{{ route('search') }}" role="search">
                <input type="search" name="q" placeholder="Search jobs by title or department…" aria-label="Search">
                <button type="submit">Search</button>
            </form>
            <div class="chips">
                @foreach ($settings['hero_chips'] ?? [] as $chip)
                    <a href="{{ $chip['url'] }}">{{ $chip['label'] }}</a>
                @endforeach
            </div>
        </div>

        @if ($newJobs->isNotEmpty())
            <aside class="hero-card" aria-label="New jobs">
                <h2>New Jobs</h2>
                @foreach ($newJobs as $job)
                    <a class="hero-card__job" href="{{ $job->url() }}">
                        <span class="new-badge">New</span>
                        <span>
                            <b>{{ $job->title }}</b>
                            <small>{{ $job->category->name }} · {{ $job->closing_label }}</small>
                        </span>
                    </a>
                @endforeach
                @if ($jobsCategory)
                    <a class="hero-card__all" href="{{ $jobsCategory->url() }}">Browse All Jobs →</a>
                @endif
            </aside>
        @endif
    </div></section>

    <div class="stats"><div class="wrap"><div class="stats__bar">
        @foreach ($settings['stats'] ?? [] as $stat)
            <div class="stat"><b>{{ $stat['value'] }}</b><span>{{ $stat['label'] }}</span></div>
        @endforeach
    </div></div></div>
@endsection

@section('content')
    @if ($jobCategories->isNotEmpty())
        <div class="sh"><h2>Find Jobs by Category</h2>@if ($jobsCategory)<a href="{{ $jobsCategory->url() }}">All jobs →</a>@endif</div>
        <div class="job-cats">
            @foreach ($jobCategories as $jobCategory)
                <a class="job-cat" href="{{ $jobCategory->url() }}"><span aria-hidden="true">{{ $jobCategory->icon ?? '💼' }}</span>{{ $jobCategory->name }}</a>
            @endforeach
        </div>
    @endif

    <div class="sh"><h2>Latest Jobs</h2>@if ($jobsCategory)<a href="{{ $jobsCategory->url() }}">View all →</a>@endif</div>
    <div class="job-grid">
        @foreach ($jobs as $job)
            <a class="job-card" href="{{ $job->url() }}">
                <div class="job-card__top">
                    <span class="job-card__icon">{{ $job->category->icon ?? '💼' }}</span>
                    <em @class(['job-card__badge', 'is-open' => ! $job->closing_date])>{{ $job->closing_label }}</em>
                </div>
                <h3>{{ $job->title }}</h3>
                <small>{{ $job->category->name }}</small>
                <span class="job-card__cta">View details →</span>
            </a>
        @endforeach
    </div>

    <div class="sh"><h2>Browse Categories</h2></div>
    <div class="grid">
        @foreach ($categories as $category)
            <a class="cat" style="--c:{{ $category->color }}" href="{{ $category->url() }}">
                <div class="ic">{{ $category->icon }}</div>
                <h3>{{ $category->name }}</h3>
                <p>{{ $category->description }}</p>
            </a>
        @endforeach
    </div>

    @include('partials.disclaimer')

    <div class="sh"><h2>Trending Now</h2><a href="{{ route('search') }}">View all →</a></div>
    <div class="feat">
        @if ($featured)
            <a @class(['big', 'has-img' => $featured->image_url]) @if ($featured->image_url) style="--img:url('{{ $featured->image_url }}')" @endif href="{{ $featured->url() }}">
                <span class="tag">Featured · {{ $featured->category->root()->name }}</span>
                <h3>{{ $featured->title }}</h3>
                <small>{{ $featured->excerpt }} · {{ $featured->published_at->format('j M Y') }}</small>
            </a>
        @endif
        <div class="list">
            @foreach ($trending as $post)
                <a class="li" style="--c:{{ $post->category->color }}" href="{{ $post->url() }}">
                    <span class="li__thumb">
                        @if ($post->image_url)
                            <img src="{{ $post->image_url }}" alt="" loading="lazy">
                        @endif
                    </span>
                    <span>
                        <span class="tag">{{ $post->category->root()->name }}</span>
                        <b>{{ $post->title }}</b>
                        <small>{{ $post->published_at->format('j M Y') }}</small>
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    <div class="sh"><h2>How to Apply (Simple Steps)</h2></div>
    <div class="steps">
        @foreach ($settings['steps'] ?? [] as $step)
            <div class="step"><i>{{ $loop->iteration }}</i><h4>{{ $step['title'] }}</h4><p>{{ $step['text'] }}</p></div>
        @endforeach
    </div>

    <div class="sh"><h2>Latest Posts</h2><a href="{{ route('search') }}">View all →</a></div>
    <div class="posts">
        @foreach ($latestPosts as $post)
            @include('partials.post-card')
        @endforeach
    </div>

    <div class="sh"><h2>🎓 Admissions Calendar &amp; BISP Tools</h2><a href="{{ url('education') }}">All admissions →</a></div>
    <div class="two">
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
        <div class="panel bisp">
            <h3>💳 BISP 8171 Quick Tools</h3>
            @foreach ($bispTools as $tool)
                <p>• <a href="{{ $tool->url() }}">{{ $tool->name }}</a></p>
            @endforeach
            @if ($bispEligibility)
                <a class="btn" href="{{ $bispEligibility->url() }}">Check Eligibility Guide</a>
            @endif
        </div>
    </div>

    <div class="sh"><h2>Career Advice</h2><a href="{{ route('blog.index') }}">All career advice →</a></div>
    <div class="posts">
        @foreach ($careerPosts as $post)
            @include('partials.blog-card')
        @endforeach
    </div>

    <div class="sh"><h2>Frequently Asked Questions</h2></div>
    @foreach ($faqs as $faq)
        <details><summary>{{ $faq->question }}</summary><p>{{ $faq->answer }}</p></details>
    @endforeach
@endsection
