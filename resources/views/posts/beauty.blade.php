@extends('layouts.app', ['seo' => [
    'title' => $category->parent ? $category->name.' Tips' : 'Beauty & Style Tips',
    'description' => $category->description ?? $root->description,
    'image' => $category->image_url,
    'canonical' => $category->url(),
]])

@section('hero')
    <x-listing-hero
        icon="sparkles"
        eyebrow="Beauty & Style"
        :title="$category->name"
        :lead="$category->parent ? $category->name.' tips, trends and easy routines' : 'Skincare, makeup, hair, nails and fashion tips for everyday life in Pakistan'"
        image="beauty-hero.jpg"
        :crumbs="$category->parent
            ? ['Home' => route('home'), $root->name => $root->url(), $category->name => null]
            : ['Home' => route('home'), $category->name => null]"
    >
        <form class="search" action="{{ route('search') }}" role="search">
            <input type="search" name="q" placeholder="Search skincare, makeup, hair tips…" aria-label="Search beauty and style">
            <button type="submit">Search</button>
        </form>
    </x-listing-hero>

    <div class="wrap">
        <nav class="scheme-strip beauty-strip" aria-label="Beauty and style topics">
            <a href="{{ $root->url() }}" @class(['active' => ! $category->parent])><x-site-icon name="sparkles" :size="26" />All</a>
            @foreach ($subcategories as $subcategory)
                <a href="{{ $subcategory->url() }}" @class(['active' => $subcategory->is($category)])>
                    <x-site-icon :name="$subcategory->icon" :size="26" />{{ $subcategory->name }}
                </a>
            @endforeach
        </nav>
    </div>
@endsection

@section('content')
    @unless ($category->parent)
        <section class="beauty-topics" aria-labelledby="beauty-topics">
            <div class="sh jobs-layout__head"><h2 id="beauty-topics">Explore Topics</h2><span class="listing-count">{{ $subcategories->count() }} topics</span></div>
            <div class="beauty-tiles">
                @foreach ($subcategories as $subcategory)
                    <a class="beauty-tile" href="{{ $subcategory->url() }}">
                        @if ($subcategory->image_url)
                            <img class="beauty-tile__img" src="{{ $subcategory->image_url }}" alt="{{ $subcategory->name }}" loading="lazy">
                        @endif
                        <span class="beauty-tile__icon"><x-site-icon :name="$subcategory->icon" :size="22" /></span>
                        <span class="beauty-tile__body">
                            <b>{{ $subcategory->name }}</b>
                            <small>{{ $subcategory->published_posts_count }} {{ Str::plural('article', $subcategory->published_posts_count) }} <span aria-hidden="true">→</span></small>
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endunless

    @unless ($category->parent)
        @foreach ([
            ['skincare', 'beauty-split-skin.jpg', 'Skincare', 'Healthy skin starts with a simple routine', 'Heat, dust and strong sun are part of daily life in Pakistan. A gentle cleanser, a light moisturiser and sunscreen every morning do more for your skin than a shelf full of products.', ['Cleanse twice a day with a mild face wash', 'Use SPF 30 or higher sunscreen every morning', 'Try one new product at a time and patch test it first']],
            ['hair-care', 'beauty-split-hair.jpg', 'Hair Care', 'Soft, frizz-free hair in every season', 'Hard water, humidity and heat styling can leave hair dry and dull. Regular conditioning, light oiling before washing and less heat keep hair healthy and easy to manage.', ['Condition the lengths after every wash', 'Use a heat protectant before straightening or curling', 'Cover hair from strong sun and dust outdoors']],
            ['nail-care', 'beauty-split-nails.jpg', 'Nail Care', 'Neat, strong nails without a salon', 'A short weekly manicure at home keeps nails clean and less likely to break. Gloves for housework and hand cream after washing make the biggest difference.', ['Trim straight across and file in one direction', 'Push back cuticles gently instead of cutting them', 'Give nails a break between polish layers']],
            ['bridal', 'beauty-split-bridal.jpg', 'Bridal', 'Look and feel your best on your wedding day', 'Bridal beauty works best when you start early. Plan skincare, hair and makeup trials weeks ahead, and avoid trying anything new in the last few days before the mehndi, barat and walima.', ['Book your makeup artist two to three months ahead', 'Have a full makeup and hair trial a month before', 'No new products or facials in the final week']],
        ] as [$slug, $image, $eyebrow, $heading, $text, $points])
            @php($topic = $subcategories->firstWhere('slug', $slug))
            <section @class(['beauty-split', 'beauty-split--flip' => $loop->even])>
                <img class="beauty-split__img" src="{{ asset('images/'.$image) }}" alt="{{ $eyebrow }} tips" loading="lazy">
                <div class="beauty-split__body">
                    <span class="beauty-split__eyebrow"><x-site-icon :name="$topic?->icon ?? 'sparkles'" :size="18" />{{ $eyebrow }}</span>
                    <h2>{{ $heading }}</h2>
                    <p>{{ $text }}</p>
                    <ul>
                        @foreach ($points as $point)
                            <li><x-site-icon name="check" :size="18" />{{ $point }}</li>
                        @endforeach
                    </ul>
                    @if ($topic)
                        <a class="button" href="{{ $topic->url() }}">Read {{ $eyebrow }} Tips</a>
                    @endif
                </div>
            </section>
        @endforeach
    @endunless

    @if ($featured->isNotEmpty())
        <section class="beauty-featured" aria-labelledby="beauty-featured">
            <div class="sh"><h2 id="beauty-featured">Featured Articles</h2></div>
            <div class="feat">
                @php($lead = $featured->first())
                <a @class(['big', 'has-img' => $lead->image_url]) href="{{ $lead->url() }}">
                    @if ($lead->image_url)
                        <img class="big__img" src="{{ $lead->image_url }}" alt="{{ $lead->title }}" loading="lazy">
                    @endif
                    <span class="big__body">
                        <span class="tag">{{ $lead->category->name }}</span>
                        <h3>{{ $lead->title }}</h3>
                        <small>{{ $lead->excerpt }} · {{ $lead->published_at->format('j M Y') }}</small>
                    </span>
                </a>
                <div class="list">
                    @foreach ($featured->skip(1) as $post)
                        <a class="li" style="--c:{{ $post->category->color }}" href="{{ $post->url() }}">
                            <span class="li__thumb">
                                @if ($post->image_url)
                                    <img src="{{ $post->image_url }}" alt="" loading="lazy">
                                @endif
                            </span>
                            <span>
                                <span class="tag">{{ $post->category->name }}</span>
                                <b>{{ $post->title }}</b>
                                <small>{{ $post->published_at->format('j M Y') }}</small>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($posts->isNotEmpty() || $featured->isEmpty())
        <section class="beauty-latest" aria-labelledby="beauty-latest">
            <div class="sh jobs-layout__head">
                <h2 id="beauty-latest">{{ $category->parent ? $category->name : 'More Beauty & Style Articles' }}</h2>
                <span class="listing-count">{{ $posts->total() }} {{ Str::plural('article', $posts->total()) }}</span>
            </div>

            @if ($posts->isEmpty())
                <div class="beauty-empty">
                    <x-site-icon name="sparkles" :size="40" />
                    <h3>New {{ Str::lower($category->name) }} articles are coming soon</h3>
                    <p>We are preparing simple, practical tips. Meanwhile, explore our other sections.</p>
                    <div class="cta-band__actions">
                        <a class="button" href="{{ url('jobs') }}">Browse Jobs</a>
                        <a class="button button--ghost" href="{{ route('blog.index') }}">Career Advice</a>
                    </div>
                </div>
            @else
                <div class="posts">
                    @foreach ($posts as $post)
                        @include('partials.post-card')
                    @endforeach
                </div>
                {{ $posts->links() }}
            @endif
        </section>
    @endif

    @unless ($category->parent)
        @php($beautyFaqs = [
            ['question' => 'What is the most important step in a skincare routine?', 'answer' => 'Sunscreen. A broad-spectrum SPF 30 or higher sunscreen every morning protects against tanning, dark spots and early ageing, which are common in Pakistan’s strong sun.'],
            ['question' => 'How can I find out my skin type?', 'answer' => 'Wash your face with a mild cleanser and wait an hour. If it looks shiny all over, it is oily; tight or flaky means dry; shiny only on the forehead and nose means combination.'],
            ['question' => 'Are home remedies safe for the face?', 'answer' => 'Some are gentle, but lemon juice, toothpaste and baking soda can irritate or burn the skin. Patch test anything new on a small area first.'],
            ['question' => 'How often should I oil my hair?', 'answer' => 'Once or twice a week before washing suits most people. Use less, or skip it, if your scalp is oily or has dandruff.'],
            ['question' => 'When should I see a dermatologist?', 'answer' => 'See a qualified dermatologist for acne that leaves marks, rashes, sudden hair fall, nail changes or any problem that does not improve with simple care.'],
        ])
        @push('schema')
            <script type="application/ld+json">@json(\App\Support\JsonLd::faq($beautyFaqs))</script>
        @endpush
        <section class="beauty-faqs" aria-labelledby="beauty-faqs">
            <div class="sh"><h2 id="beauty-faqs">Frequently Asked Questions</h2></div>
            @foreach ($beautyFaqs as $faq)
                <details @if ($loop->first) open @endif><summary>{{ $faq['question'] }}</summary><p>{{ $faq['answer'] }}</p></details>
            @endforeach
        </section>
    @endunless
@endsection
