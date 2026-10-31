@if ($posts->isEmpty())
    <p class="empty">Abhi koi blog post nahi hai.</p>
@else
    <div class="bl">
        @foreach ($posts as $post)
            @include('partials.blog-row')
        @endforeach
    </div>
    {{ $posts->links() }}
@endif
