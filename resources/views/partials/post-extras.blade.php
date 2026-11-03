{{-- FAQs and tags shared by posts and blog posts. Expects $post. --}}
@if ($post->faqs)
    @push('schema')
        <script type="application/ld+json">@json(\App\Support\JsonLd::faq($post->faqs))</script>
    @endpush
    <section class="detail-faqs" aria-labelledby="faq-title">
        <h2 id="faq-title">Frequently Asked Questions</h2>
        @foreach ($post->faqs as $faq)
            <details @if ($loop->first) open @endif><summary>{{ $faq['question'] }}</summary><p>{{ $faq['answer'] }}</p></details>
        @endforeach
    </section>
@endif

@php($tags = is_string($post->tags) ? array_filter(array_map('trim', explode(',', $post->tags))) : (array) $post->tags)
@if ($tags)
    <div class="detail-tags">
        <strong>Tags:</strong>
        @foreach ($tags as $tag)
            <a href="{{ route('search', ['q' => $tag]) }}" rel="tag">{{ $tag }}</a>
        @endforeach
    </div>
@endif
