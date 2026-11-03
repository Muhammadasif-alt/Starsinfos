@php($email = config('mail.admin_address'))
@php($contactFaqs = [
    ['question' => 'How quickly will you reply?', 'answer' => 'We usually reply within 24 to 48 hours on working days. Messages about wrong or outdated information are checked first.'],
    ['question' => 'Can you apply for a job or scheme on my behalf?', 'answer' => 'No. We only share information. You must apply yourself on the official website or portal linked on each listing.'],
    ['question' => 'I found wrong or outdated information. What should I do?', 'answer' => 'Send us the page link and the correct detail, ideally with the official source. We check it and update the page.'],
    ['question' => 'Is Stars Infos linked to the government?', 'answer' => 'No. Stars Infos is an independent information website and not an official government portal. Always confirm details on the official website.'],
    ['question' => 'Can I advertise or partner with Stars Infos?', 'answer' => 'Yes. Email us with your company name, what you would like to promote and your budget, and we will get back to you.'],
])

@php($contactSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'ContactPage',
    'name' => $page->title,
    'url' => $page->url(),
    'mainEntity' => [
        '@type' => 'Organization',
        'name' => config('app.name'),
        'url' => url('/'),
        'email' => $email,
        'contactPoint' => [['@type' => 'ContactPoint', 'contactType' => 'customer support', 'email' => $email, 'areaServed' => 'PK', 'availableLanguage' => ['English', 'Urdu']]],
    ],
])

@push('schema')
    <script type="application/ld+json">@json($contactSchema)</script>
    <script type="application/ld+json">@json(\App\Support\JsonLd::faq($contactFaqs))</script>
@endpush

<section class="contact-cards" aria-label="Ways to reach us">
    <a class="contact-card" href="mailto:{{ $email }}">
        <span class="contact-card__icon"><x-site-icon name="mail" :size="26" /></span>
        <b>Email Us</b>
        <small>{{ $email }}</small>
    </a>
    <div class="contact-card">
        <span class="contact-card__icon"><x-site-icon name="clock" :size="26" /></span>
        <b>Response Time</b>
        <small>Within 24–48 hours on working days</small>
    </div>
    <a class="contact-card" href="{{ url('editorial-policy') }}">
        <span class="contact-card__icon"><x-site-icon name="pen" :size="26" /></span>
        <b>Report a Correction</b>
        <small>See how we check our information</small>
    </a>
</section>

<div class="contact-grid">
    <form class="form contact-form" action="{{ route('contact') }}" method="post">
        @csrf
        <h2>Send Us a Message</h2>
        <p class="contact-form__lead">Fill in the form and our team will reply by email.</p>

        @if (session('contact_status'))
            <p class="ok" role="status">{{ session('contact_status') }}</p>
        @endif

        <div class="contact-form__row">
            <label>Name
                <input name="name" value="{{ old('name') }}" maxlength="100" autocomplete="name" placeholder="Your full name" required>
                @error('name')<span class="err">{{ $message }}</span>@enderror
            </label>
            <label>Email
                <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" placeholder="you@example.com" required>
                @error('email')<span class="err">{{ $message }}</span>@enderror
            </label>
        </div>
        <label>Subject
            <input name="subject" value="{{ old('subject') }}" maxlength="150" placeholder="What is your message about?" required>
            @error('subject')<span class="err">{{ $message }}</span>@enderror
        </label>
        <label>Message
            <textarea name="message" rows="7" maxlength="5000" placeholder="Write your message here" required>{{ old('message') }}</textarea>
            @error('message')<span class="err">{{ $message }}</span>@enderror
        </label>
        <button type="submit">Send Message</button>
    </form>

    <aside class="contact-aside">
        <div class="prose">{!! $page->safe_body !!}</div>
        <h3>We can help with</h3>
        <ul class="contact-aside__list">
            <li><x-site-icon name="check" :size="20" />Questions about a job, scheme or article</li>
            <li><x-site-icon name="check" :size="20" />Wrong, missing or outdated information</li>
            <li><x-site-icon name="check" :size="20" />Article ideas and partnership requests</li>
        </ul>
        <div class="contact-aside__note">
            <x-site-icon name="shield" :size="22" />
            <p><b>Stay safe:</b> we never ask for money, CNIC copies or passwords. Apply only on official websites.</p>
        </div>
    </aside>
</div>

<section class="contact-faqs" aria-labelledby="contact-faqs">
    <div class="sh"><h2 id="contact-faqs">Frequently Asked Questions</h2></div>
    @foreach ($contactFaqs as $faq)
        <details @if ($loop->first) open @endif><summary>{{ $faq['question'] }}</summary><p>{{ $faq['answer'] }}</p></details>
    @endforeach
</section>
