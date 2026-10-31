@extends('layouts.app', ['seo' => ['title' => $page->title, 'canonical' => $page->url()]])

@section('content')
    @include('partials.breadcrumbs', ['crumbs' => ['Home' => route('home'), $page->title => null]])

    <article class="article">
        <h1 class="page-title">{{ $page->title }}</h1>
        <div class="prose">{!! $page->safe_body !!}</div>

        @if ($page->slug === 'contact-us')
            @if (session('contact_status'))
                <p class="ok" role="status">{{ session('contact_status') }}</p>
            @endif

            <form class="form" action="{{ route('contact') }}" method="post">
                @csrf
                <label>Name
                    <input name="name" value="{{ old('name') }}" maxlength="100" required>
                    @error('name')<span class="err">{{ $message }}</span>@enderror
                </label>
                <label>Email
                    <input type="email" name="email" value="{{ old('email') }}" required>
                    @error('email')<span class="err">{{ $message }}</span>@enderror
                </label>
                <label>Subject
                    <input name="subject" value="{{ old('subject') }}" maxlength="150" required>
                    @error('subject')<span class="err">{{ $message }}</span>@enderror
                </label>
                <label>Message
                    <textarea name="message" rows="6" maxlength="5000" required>{{ old('message') }}</textarea>
                    @error('message')<span class="err">{{ $message }}</span>@enderror
                </label>
                <button type="submit">Send Message</button>
            </form>
        @endif
    </article>
@endsection
