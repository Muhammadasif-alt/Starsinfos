<footer style="--img:url('{{ asset('images/footer-skyline.jpg') }}')"><div class="wrap">
    <div class="cols">
        <div class="footer-about">
            <h5>{{ config('app.name') }}</h5>
            <p>{{ $footerText }}</p>
        </div>
        <div>
            <h5>Categories</h5>
            @foreach ($footerCategories as $category)
                <a href="{{ $category->url() }}">{{ $category->name }}</a>
            @endforeach
            <a href="{{ route('blog.index') }}">Career Advice</a>
        </div>
        <div>
            <h5>Popular Jobs</h5>
            @foreach ($footerJobCategories as $category)
                <a href="{{ $category->url() }}">{{ $category->name }}</a>
            @endforeach
        </div>
        <div>
            <h5>Company</h5>
            @foreach ($footerPages as $page)
                <a href="{{ $page->url() }}">{{ $page->title }}</a>
            @endforeach
        </div>
    </div>
    <div class="cp">
        <span>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</span>
        <nav class="legal-links" aria-label="Legal">
            @foreach ($legalPages as $page)
                <a href="{{ $page->url() }}">{{ $page->title }}</a>
            @endforeach
        </nav>
    </div>
</div></footer>
