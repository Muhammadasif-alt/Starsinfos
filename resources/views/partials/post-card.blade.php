<a class="post" style="--c:{{ $post->category->color }}" href="{{ $post->url() }}">
    <div class="thumb">
        @if ($post->image_url)
            <img src="{{ $post->image_url }}" alt="{{ $post->title }}" loading="lazy">
        @endif
    </div>
    <div class="b">
        <span class="tag">{{ $post->category->root()->name }}</span>
        <h4>{{ $post->title }}</h4>
        <small>{{ $post->published_at->format('j M Y') }}</small>
    </div>
</a>
