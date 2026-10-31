<footer><div class="wrap">
    <div class="cols">
        <div><h5>{{ config('app.name') }}</h5>{{ $tagline }}</div>
        <div>
            <h5>Categories</h5>
            @foreach ($footerCategories as $category)
                <a href="{{ $category->url() }}">{{ $category->name }}</a>
            @endforeach
            <a href="{{ route('blog.index') }}">Blog</a>
        </div>
        <div>
            <h5>Pages</h5>
            @foreach ($footerPages as $page)
                <a href="{{ $page->url() }}">{{ $page->title }}</a>
            @endforeach
        </div>
    </div>
    <div class="cp">© {{ date('Y') }} {{ config('app.name') }}.</div>
</div></footer>
