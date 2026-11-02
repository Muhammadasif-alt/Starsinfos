<div class="page-layout">
    <aside class="page-nav" aria-label="Site information">
        <h2>Site Information</h2>
        @foreach ($pages as $item)
            <a href="{{ $item->url() }}" @class(['active' => $item->is($page)]) @if ($item->is($page)) aria-current="page" @endif>{{ $item->title }}</a>
        @endforeach
    </aside>
    <article class="page-card prose">{!! $page->safe_body !!}</article>
</div>
