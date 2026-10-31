{{-- $crumbs: label => url (null for the current page) --}}
@push('schema')
    <script type="application/ld+json">@json(\App\Support\JsonLd::breadcrumbs($crumbs))</script>
@endpush
<ol class="crumbs" aria-label="Breadcrumb">
    @foreach ($crumbs as $label => $url)
        <li>@if ($url)<a href="{{ $url }}">{{ $label }}</a>@else{{ $label }}@endif</li>
    @endforeach
</ol>
