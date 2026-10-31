@if ($posts->isEmpty())
    <p class="empty">There are no blog posts yet.</p>
@else
    <div class="bl">
        @foreach ($posts as $post)
            @include('partials.blog-row')
        @endforeach
    </div>
    {{ $posts->links() }}
@endif
