@if ($posts->isEmpty())
    <p class="empty">There are no blog posts yet.</p>
@else
    <div class="posts">
        @foreach ($posts as $post)
            @include('partials.blog-card')
        @endforeach
    </div>
    {{ $posts->links() }}
@endif
