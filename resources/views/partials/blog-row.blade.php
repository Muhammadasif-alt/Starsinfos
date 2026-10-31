<a class="bpost" style="--c:{{ $post->category?->color ?? '#0B6B4F' }}" href="{{ $post->url() }}">
    <div class="bimg">
        @if ($post->image_url)
            <img src="{{ $post->image_url }}" alt="{{ $post->title }}" loading="lazy">
        @endif
    </div>
    <div>
        @if ($post->category)
            <span class="tag">{{ $post->category->name }}</span>
        @endif
        <h4>{{ $post->title }}</h4>
        <div class="meta">
            @include('partials.avatar', ['user' => $post->author])
            <span>{{ $post->author->name }}</span>
            <span>· {{ $post->published_at->format('j M Y') }}</span>
            <span>· {{ $post->read_time }} min read</span>
        </div>
    </div>
</a>
