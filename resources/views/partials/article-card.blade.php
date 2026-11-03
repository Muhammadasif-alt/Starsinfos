{{-- Article card used on the Beauty & Style page and the homepage. Expects $post. --}}
<a class="beauty-card" href="{{ $post->url() }}">
    <span class="beauty-card__img">
        @if ($post->image_url)
            <img src="{{ $post->image_url }}" alt="{{ $post->title }}" loading="lazy">
        @endif
        <span class="beauty-card__tag">{{ $post->category->name }}</span>
    </span>
    <span class="beauty-card__body">
        <b>{{ $post->title }}</b>
        <span class="beauty-card__excerpt">{{ $post->excerpt }}</span>
        <span class="beauty-card__meta">
            <small>{{ $post->published_at->format('j M Y') }}</small>
            <span class="beauty-card__more">Read more →</span>
        </span>
    </span>
</a>
