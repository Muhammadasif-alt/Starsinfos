@extends('layouts.app', ['seo' => ['description' => $settings['hero_text'] ?? null]])

@push('schema')
    @if ($faqs->isNotEmpty())
        <script type="application/ld+json">@json(\App\Support\JsonLd::faq($faqs))</script>
    @endif
@endpush

@section('hero')
    <section class="hero"><div class="wrap">
        <h1>{{ $settings['hero_title'] ?? config('app.name') }}</h1>
        <p>{{ $settings['hero_text'] ?? '' }}</p>
        <form class="search" action="{{ route('search') }}" role="search">
            <input type="search" name="q" placeholder="Search jobs, BISP, admissions…" aria-label="Search">
            <button type="submit">Search</button>
        </form>
        <div class="chips">
            @foreach ($settings['hero_chips'] ?? [] as $chip)
                <a href="{{ $chip['url'] }}">{{ $chip['label'] }}</a>
            @endforeach
        </div>
    </div></section>

    <div class="stats"><div class="wrap">
        @foreach ($settings['stats'] ?? [] as $stat)
            <div class="stat"><b>{{ $stat['value'] }}</b><span>{{ $stat['label'] }}</span></div>
        @endforeach
    </div></div>
@endsection

@section('content')
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
                <a class="li" href="{{ $post->url() }}"><span class="num">{{ $loop->iteration }}</span>{{ $post->title }}</a>
            @endforeach
        </div>
    </div>

    <div class="sh"><h2>Latest Jobs &amp; BISP Tools</h2></div>
    <div class="two">
        <div class="panel">
            <h3>💼 Latest Jobs</h3>
            @foreach ($jobs as $job)
                <div class="job"><span><a href="{{ $job->url() }}">{{ $job->title }}</a></span><em>{{ $job->closing_label }}</em></div>
            @endforeach
        </div>
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

    <div class="sh"><h2>🎓 Admissions Calendar</h2><a href="{{ url('education') }}">All admissions →</a></div>
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

    <div class="sh"><h2>🛋️ Home &amp; Decor Ideas</h2>@if ($decor)<a href="{{ $decor->url() }}">More ideas →</a>@endif</div>
    <div class="gal">
        @foreach ($decorTiles as $tile)
            <a @class(['tile', 'has-img' => $tile->image_url]) style="--c:{{ $tile->color }}@if ($tile->image_url);--img:url('{{ $tile->image_url }}')@endif" href="{{ $tile->url() }}">{{ $tile->name }}</a>
        @endforeach
    </div>

    <div class="sh"><h2>✍️ From Our Blog</h2><a href="{{ route('blog.index') }}">All blog posts →</a></div>
    <div class="blog">
        @if ($featuredBlog)
            <a class="bfeat" style="--c:{{ $featuredBlog->category?->color ?? '#0B6B4F' }}" href="{{ $featuredBlog->url() }}">
                <div class="bi">
                    @if ($featuredBlog->image_url)
                        <img src="{{ $featuredBlog->image_url }}" alt="{{ $featuredBlog->title }}" loading="lazy">
                    @endif
                </div>
                <div class="bd">
                    @if ($featuredBlog->category)
                        <span class="tag">{{ $featuredBlog->category->name }}</span>
                    @endif
                    <h3>{{ $featuredBlog->title }}</h3>
                    <p>{{ $featuredBlog->excerpt }}</p>
                    <div class="meta">
                        @include('partials.avatar', ['user' => $featuredBlog->author])
                        <span>{{ $featuredBlog->author->name }}</span>
                        <span>· {{ $featuredBlog->published_at->format('j M Y') }}</span>
                        <span>· {{ $featuredBlog->read_time }} min read</span>
                    </div>
                </div>
            </a>
        @endif
        <div class="bl">
            @foreach ($latestBlogs as $post)
                @include('partials.blog-row')
            @endforeach
        </div>
    </div>

    <div class="sh"><h2>Frequently Asked Questions</h2></div>
    @foreach ($faqs as $faq)
        <details><summary>{{ $faq->question }}</summary><p>{{ $faq->answer }}</p></details>
    @endforeach
@endsection
