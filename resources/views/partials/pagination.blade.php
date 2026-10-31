@if ($paginator->hasPages())
    <nav aria-label="Pagination" style="display:block;position:static;padding:0;border:0">
        <ul class="pager">
            @if ($paginator->onFirstPage())
                <li class="disabled"><span>← Prev</span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">← Prev</a></li>
            @endif

            @isset($elements)
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li class="disabled"><span>{{ $element }}</span></li>
                    @elseif (is_array($element))
                        @foreach ($element as $page => $url)
                            <li @class(['current' => $page === $paginator->currentPage()])>
                                @if ($page === $paginator->currentPage())<span aria-current="page">{{ $page }}</span>@else<a href="{{ $url }}">{{ $page }}</a>@endif
                            </li>
                        @endforeach
                    @endif
                @endforeach
            @endisset

            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">Next →</a></li>
            @else
                <li class="disabled"><span>Next →</span></li>
            @endif
        </ul>
    </nav>
@endif
