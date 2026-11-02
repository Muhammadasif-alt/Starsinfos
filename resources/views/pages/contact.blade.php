<div class="contact-grid">
    <div class="contact-info">
        <div class="page-card prose">{!! $page->safe_body !!}</div>
        <a class="contact-link" href="mailto:{{ config('mail.admin_address') }}">
            <span class="ic"><x-site-icon name="file" /></span>
            <span><b>Email Us</b><small>{{ config('mail.admin_address') }}</small></span>
        </a>
        <a class="contact-link" href="{{ url('write-for-us') }}">
            <span class="ic"><x-site-icon name="pen" /></span>
            <span><b>Write for Us</b><small>Share an article idea with our team.</small></span>
        </a>
        <a class="contact-link" href="{{ url('editorial-policy') }}">
            <span class="ic"><x-site-icon name="check" /></span>
            <span><b>Editorial Policy</b><small>See how we check our information.</small></span>
        </a>
        @include('partials.disclaimer')
    </div>

    <form class="page-card form" action="{{ route('contact') }}" method="post">
        @csrf
        <h2>Send Us a Message</h2>

        @if (session('contact_status'))
            <p class="ok" role="status">{{ session('contact_status') }}</p>
        @endif

        <label>Name
            <input name="name" value="{{ old('name') }}" maxlength="100" autocomplete="name" required>
            @error('name')<span class="err">{{ $message }}</span>@enderror
        </label>
        <label>Email
            <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
            @error('email')<span class="err">{{ $message }}</span>@enderror
        </label>
        <label>Subject
            <input name="subject" value="{{ old('subject') }}" maxlength="150" required>
            @error('subject')<span class="err">{{ $message }}</span>@enderror
        </label>
        <label>Message
            <textarea name="message" rows="7" maxlength="5000" required>{{ old('message') }}</textarea>
            @error('message')<span class="err">{{ $message }}</span>@enderror
        </label>
        <button type="submit">Send Message</button>
    </form>
</div>
