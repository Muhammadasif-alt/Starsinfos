@php
    $seoTitle = isset($title) ? $title.' | '.config('app.name') : config('app.name');
    $seoDescription = $description ?? $tagline;
    $seoUrl = $canonical ?? url()->current();
@endphp
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="google-site-verification" content="eIL-g-SgY4AemxAGCF7W3RtcbBXSgjGMRpVFyDrYNTg">
<meta name="msvalidate.01" content="C2A03253D85A4A5495DC8DC8113C1585">
<link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
<link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">
<link rel="canonical" href="{{ $seoUrl }}">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:type" content="{{ $type ?? 'website' }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoUrl }}">
<meta name="twitter:card" content="{{ isset($image) ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
@isset($image)
    <meta property="og:image" content="{{ $image }}">
    <meta name="twitter:image" content="{{ $image }}">
@endisset
