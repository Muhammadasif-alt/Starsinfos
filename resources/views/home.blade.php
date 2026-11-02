@extends('layouts.app', ['seo' => ['description' => $settings['hero_text'] ?? null]])

@push('schema')
    @if ($faqs->isNotEmpty())
        <script type="application/ld+json">@json(\App\Support\JsonLd::faq($faqs))</script>
    @endif
@endpush

@section('hero')
    <section class="hero" style="--img:url('{{ asset('images/home-hero.jpg') }}')"><div class="wrap hero__inner">
        <div>
            @php($highlight = e($settings['hero_highlight'] ?? ''))
            <h1>{!! $highlight !== '' ? str_replace($highlight, '<mark>'.$highlight.'</mark>', e($settings['hero_title'] ?? config('app.name'))) : e($settings['hero_title'] ?? config('app.name')) !!}</h1>
            <p>{{ $settings['hero_text'] ?? '' }}</p>
            <form class="search" action="{{ route('search') }}" role="search">
                <input type="search" name="q" placeholder="Search jobs, schemes, admissions…" aria-label="Search">
                <button type="submit">Search</button>
            </form>
            <div class="chips">
                @foreach ($settings['hero_chips'] ?? [] as $chip)
                    <a href="{{ $chip['url'] }}">{{ $chip['label'] }}</a>
                @endforeach
            </div>
        </div>

        @if ($latestUpdates->isNotEmpty())
            <aside class="hero-card" aria-label="Latest updates">
                <h2>Latest Updates</h2>
                @foreach ($latestUpdates as $job)
                    <a class="hero-card__job" href="{{ $job->url() }}">
                        <span class="new-badge">New</span>
                        <span>
                            <b>{{ $job->title }}</b>
                            <small>{{ $job->category->root()->name }} · {{ $job->closing_date ? $job->closing_label : $job->published_at->format('j M Y') }}</small>
                        </span>
                    </a>
                @endforeach
                <a class="hero-card__all" href="{{ route('search') }}">View All Updates →</a>
            </aside>
        @endif
    </div></section>

    <div class="stats"><div class="wrap"><div class="stats__bar">
        @foreach ($settings['stats'] ?? [] as $stat)
            <div class="stat">
                <span class="stat__icon"><x-site-icon :name="['briefcase', 'book', 'bell', 'check'][$loop->index] ?? 'check'" :size="30" /></span>
                <span><b>{{ $stat['value'] }}</b><span>{{ $stat['label'] }}</span></span>
            </div>
        @endforeach
    </div></div></div>
@endsection

@section('content')
    @if ($jobCategories->isNotEmpty())
        <div class="sh"><h2>Find Jobs by Category</h2>@if ($jobsCategory)<a href="{{ $jobsCategory->url() }}">All jobs →</a>@endif</div>
        <div class="job-cats">
            @foreach ($jobCategories as $jobCategory)
                <a class="job-cat" href="{{ $jobCategory->url() }}">
                    <span class="job-cat__icon"><x-site-icon :name="$jobCategory->icon" /></span>
                    <b>{{ $jobCategory->name }}</b>
                    <span class="job-cat__arrow" aria-hidden="true">→</span>
                </a>
            @endforeach
        </div>
    @endif

    <div class="sh"><h2>Latest Jobs</h2>@if ($jobsCategory)<a href="{{ $jobsCategory->url() }}">View all →</a>@endif</div>
    <div class="job-grid">
        @foreach ($jobs as $job)
            <a class="job-card" href="{{ $job->url() }}">
                <div class="job-card__top">
                    <span class="job-card__icon"><x-site-icon :name="$job->category->icon" /></span>
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
                <div class="ic"><x-site-icon :name="$category->icon" :size="26" /></div>
                <h3>{{ $category->name }}</h3>
                <p>{{ $category->description }}</p>
            </a>
        @endforeach
    </div>

    @include('partials.disclaimer')

    @if ($featured || $trending->isNotEmpty())
    <div class="sh"><h2>Trending Now</h2><a href="{{ route('search') }}">View all →</a></div>
    <div class="feat">
        @if ($featured)
            <a @class(['big', 'has-img' => $featured->image_url]) href="{{ $featured->url() }}">
                @if ($featured->image_url)
                    <img class="big__img" src="{{ $featured->image_url }}" alt="{{ $featured->title }}" loading="lazy">
                @endif
                <span class="big__body">
                    <span class="tag">Featured · {{ $featured->category->root()->name }}</span>
                    <h3>{{ $featured->title }}</h3>
                    <small>{{ $featured->excerpt }} · {{ $featured->published_at->format('j M Y') }}</small>
                </span>
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
    @endif

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

    <div class="sh"><h2>Admissions Calendar &amp; BISP Tools</h2><a href="{{ url('education') }}">All admissions →</a></div>
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
            <h3 class="icon-title"><x-site-icon name="card" /> BISP 8171 Quick Tools</h3>
            @foreach ($bispTools as $tool)
                <p>• <a href="{{ $tool->url() }}">{{ $tool->name }}</a></p>
            @endforeach
            @if ($bispEligibility)
                <a class="btn" href="{{ $bispEligibility->url() }}">Check Eligibility Guide</a>
            @endif
        </div>
    </div>

    <div class="sh"><h2>Career Advice</h2><a href="{{ route('blog.index') }}">All career advice →</a></div>
    <div class="feat">
        @if ($leadCareerPost = $careerPosts->first())
            <a @class(['big', 'has-img' => $leadCareerPost->image_url]) href="{{ $leadCareerPost->url() }}">
                @if ($leadCareerPost->image_url)
                    <img class="big__img" src="{{ $leadCareerPost->image_url }}" alt="{{ $leadCareerPost->title }}" loading="lazy">
                @endif
                <span class="big__body">
                    @if ($leadCareerPost->topic_label ?? $leadCareerPost->category)
                        <span class="tag">{{ $leadCareerPost->topic_label ?? $leadCareerPost->category->name }}</span>
                    @endif
                    <h3>{{ $leadCareerPost->title }}</h3>
                    <small>{{ $leadCareerPost->excerpt }} · {{ $leadCareerPost->read_time }} min read</small>
                </span>
            </a>
        @endif
        <div class="list">
            @foreach ($careerPosts->skip(1) as $post)
                <a class="li" style="--c:{{ $post->category?->color ?? '#0B6B4F' }}" href="{{ $post->url() }}">
                    <span class="li__thumb">
                        @if ($post->image_url)
                            <img src="{{ $post->image_url }}" alt="" loading="lazy">
                        @endif
                    </span>
                    <span>
                        @if ($post->category)
                            <span class="tag">{{ $post->category->name }}</span>
                        @endif
                        <b>{{ $post->title }}</b>
                        <small>{{ $post->published_at->format('j M Y') }} · {{ $post->read_time }} min read</small>
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    <div class="sh"><h2>Frequently Asked Questions</h2></div>
    @foreach ($faqs as $faq)
        <details><summary>{{ $faq->question }}</summary><p>{{ $faq->answer }}</p></details>
    @endforeach
@endsection
