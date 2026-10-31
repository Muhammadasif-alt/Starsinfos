<form class="nl" id="newsletter" action="{{ route('subscribe') }}" method="post">
    @csrf
    <h3>{{ \App\Models\Setting::get('newsletter_title') }}</h3>
    <p>{{ \App\Models\Setting::get('newsletter_text') }}</p>
    <div class="row">
        <input type="email" name="email" placeholder="Your email" aria-label="Your email" required>
        <button type="submit">Subscribe</button>
    </div>
    <p class="nl-msg" role="status" aria-live="polite">{{ session('newsletter_status') ?? $errors->first('email') }}</p>
</form>
