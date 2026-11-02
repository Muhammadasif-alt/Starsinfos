<article class="page-card prose page-intro">{!! $page->safe_body !!}</article>

@if ($stats = \App\Models\Setting::get('stats'))
    <div class="about-stats">
        @foreach ($stats as $stat)
            <div class="stat"><b>{{ $stat['value'] }}</b><span>{{ $stat['label'] }}</span></div>
        @endforeach
    </div>
@endif

<div class="sh"><h2>What We Cover</h2></div>
<div class="grid">
    @foreach ($categories as $category)
        <a class="cat" style="--c:{{ $category->color }}" href="{{ $category->url() }}">
            <div class="ic">{{ $category->icon }}</div>
            <h3>{{ $category->name }}</h3>
            <p>{{ $category->description }}</p>
        </a>
    @endforeach
</div>

<div class="cta-band">
    <div>
        <h2>Want to get in touch?</h2>
        <p>Send us a question or correction, or write for our readers.</p>
    </div>
    <div class="cta-band__actions">
        <a class="button" href="{{ url('contact-us') }}">Contact Us</a>
        <a class="button button--ghost" href="{{ url('write-for-us') }}">Write for Us</a>
    </div>
</div>
