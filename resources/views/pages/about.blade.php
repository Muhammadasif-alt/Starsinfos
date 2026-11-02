@php($stats = \App\Models\Setting::get('stats', []))

@if ($stats)
    <div class="stats about-stats-bar"><div class="stats__bar">
        @foreach ($stats as $stat)
            <div class="stat">
                <span class="stat__icon"><x-site-icon :name="['briefcase', 'book', 'bell', 'check'][$loop->index] ?? 'check'" :size="30" /></span>
                <span><b>{{ $stat['value'] }}</b><span>{{ $stat['label'] }}</span></span>
            </div>
        @endforeach
    </div></div>
@endif

<section class="about-intro" aria-labelledby="who-we-are">
    <div>
        <span class="about-kicker">Who We Are</span>
        <h2 id="who-we-are">Helping Pakistan Find the Right Information, Faster</h2>
        <div class="prose">{!! $page->safe_body !!}</div>
        <div class="about-intro__actions">
            <a class="button" href="{{ url('jobs') }}">Browse Latest Jobs →</a>
            <a class="button button--ghost" href="{{ url('contact-us') }}">Contact Us</a>
        </div>
    </div>
    <figure class="about-intro__media">
        <img src="{{ asset('images/about-team.jpg') }}" alt="Two young professionals reviewing information on a laptop" loading="lazy">
        <figcaption><x-site-icon name="check" :size="20" /> Every guide links to its official source</figcaption>
    </figure>
</section>

<section class="about-mission" aria-label="Mission and vision">
    <article>
        <span class="job-card__icon"><x-site-icon name="award" /></span>
        <h2>Our Mission</h2>
        <p>To give every job seeker, student and family in Pakistan clear, practical information about jobs, government schemes and admissions, in simple language and with a link to the official source.</p>
    </article>
    <article>
        <span class="job-card__icon"><x-site-icon name="globe" /></span>
        <h2>Our Vision</h2>
        <p>A Pakistan where no one misses an opportunity because the information was hard to find, hard to understand or hidden behind an agent's fee.</p>
    </article>
</section>

<section aria-labelledby="how-we-work">
    <div class="sh"><h2 id="how-we-work">How We Work</h2><a href="{{ url('editorial-policy') }}">Read our editorial policy →</a></div>
    <div class="steps about-steps">
        @foreach ([
            ['Find the Announcement', 'We start from the official advertisement, portal or notification, not from social media posts.'],
            ['Check the Details', 'We confirm dates, eligibility and links on the official source before we publish.'],
            ['Explain It Simply', 'We write each guide in plain language, with the steps and documents you need.'],
            ['Keep It Updated', 'We update guides when rules change and label older information as Outdated.'],
        ] as [$title, $text])
            <div class="step"><i>{{ $loop->iteration }}</i><h4>{{ $title }}</h4><p>{{ $text }}</p></div>
        @endforeach
    </div>
</section>

<section aria-labelledby="what-we-cover">
    <div class="sh"><h2 id="what-we-cover">What We Cover</h2></div>
    <div class="grid about-cover">
        @foreach ($categories as $category)
            <a class="cat" style="--c:{{ $category->color }}" href="{{ $category->url() }}">
                <div class="ic"><x-site-icon :name="$category->icon" :size="26" /></div>
                <h3>{{ $category->name }}</h3>
                <p>{{ $category->description }}</p>
            </a>
        @endforeach
        <a class="cat" href="{{ route('blog.index') }}">
            <div class="ic"><x-site-icon name="users" :size="26" /></div>
            <h3>Career Advice</h3>
            <p>CVs, interviews, entry tests and working abroad</p>
        </a>
    </div>
</section>

<section aria-labelledby="our-promise">
    <div class="sh"><h2 id="our-promise">Our Promise to Readers</h2></div>
    <div class="about-values">
        @foreach ([
            ['banknote', 'Always Free', 'Reading our guides is free, and we never charge a fee to apply for any job or scheme.'],
            ['landmark', 'Official Sources', 'Every job and scheme guide links to the official website so you can confirm the details.'],
            ['book', 'Simple Language', 'We explain eligibility, documents and steps clearly, without jargon.'],
            ['shield', 'Your Privacy', 'We only keep the details you choose to share with us, and we never sell them.'],
        ] as [$icon, $title, $text])
            <div>
                <span class="job-card__icon"><x-site-icon :name="$icon" /></span>
                <h3>{{ $title }}</h3>
                <p>{{ $text }}</p>
            </div>
        @endforeach
    </div>
    @include('partials.disclaimer')
</section>

<div class="jobs-cta about-cta">
    <span class="jobs-cta__icon"><x-site-icon name="handshake" :size="44" /></span>
    <div>
        <h2>Found a Mistake or Have a Question?</h2>
        <p>Write to us at <a href="mailto:{{ config('mail.admin_address') }}">{{ config('mail.admin_address') }}</a>, or share your knowledge with our readers.</p>
    </div>
    <div class="cta-band__actions">
        <a class="button button--gold" href="{{ url('contact-us') }}">Contact Us →</a>
        <a class="button button--ghost" href="{{ url('write-for-us') }}">Write for Us</a>
    </div>
</div>
