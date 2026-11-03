<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Subscriber;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_seed_matches_the_brief(): void
    {
        $this->assertSame(['jobs', 'government-schemes', 'beauty-style'], Category::roots()->pluck('slug')->all());
        $this->assertSame(20, Post::count());
        $this->assertSame(4, BlogPost::count());
        $this->assertSame(8, Category::where('slug', 'beauty-style')->first()->children()->count());

        foreach (['jobs', 'government-schemes'] as $slug) {
            $this->assertSame(6, Post::whereIn('category_id', Category::where('slug', $slug)->first()->familyIds())->count(), $slug);
        }
    }

    public function test_homepage_renders_all_sections_in_order(): void
    {
        $this->get('/')->assertOk()->assertSeeInOrder([
            'class="hero"', 'class="stats"', 'Find Jobs by Category', 'Latest Jobs', 'Disclaimer', 'Trending Now',
            'Latest Articles', 'Career Advice', 'Frequently Asked Questions', 'How often is the information updated?', '<footer',
        ], false)->assertSee('FAQPage', false)->assertDontSee('Browse Categories')->assertDontSee('How to Apply (Simple Steps)');
    }

    public function test_contact_page_has_the_form_faqs_and_schema(): void
    {
        $this->get('/contact-us')->assertOk()->assertSee('Send Us a Message')->assertSee('Response Time')
            ->assertSee('"@type":"ContactPage"', false)->assertSee('"@type":"FAQPage"', false)
            ->assertSee('Can you apply for a job or scheme on my behalf?');
    }

    public function test_every_public_url_resolves(): void
    {
        $urls = collect(['/search?q=jobs', '/search', '/blog', '/sitemap.xml', '/robots.txt'])
            ->concat(Category::with('parent')->get()->map->url())
            ->concat(Page::all()->map->url())
            ->concat(Post::with('category.parent')->get()->map->url())
            ->concat(BlogPost::all()->map->url())
            ->push(route('blog.author', BlogPost::first()->author));

        $urls->each(fn (string $url) => $this->get($url)->assertOk());
    }

    public function test_jobs_and_schemes_use_the_listing_layout(): void
    {
        $this->get('/jobs')->assertOk()->assertSee('jobs-filter', false);
        $this->get('/government-schemes/solar-schemes')->assertOk()->assertSee('scheme-cats', false);
        $this->get('/beauty-style')->assertOk()->assertSee('Explore Topics')->assertSee('Nail Care')
            ->assertSee('Featured Articles')->assertSee('beauty-split', false)->assertSee('"@type":"FAQPage"', false);
        $this->get('/beauty-style/makeup')->assertOk()->assertSee('Everyday Makeup Look for Beginners')->assertDontSee('Explore Topics');
        $this->get('/beauty-style')->assertSee('View All Articles')->assertSee('Body Care Routine for Soft, Healthy Skin All Year');
        $this->get('/beauty-style?all=1')->assertOk()->assertSee('All Beauty &amp; Style Articles', false)->assertSee('8 articles')->assertDontSee('Explore Topics');
        $this->get('/beauty-style/everyday-makeup-look-for-beginners')->assertOk()->assertSee('Frequently Asked Questions');
    }

    public function test_jobs_can_be_filtered(): void
    {
        $category = Category::where('slug', 'bank-jobs')->first();
        $author = User::first();
        $make = fn (string $title, string $location, string $type) => Post::factory()->create([
            'category_id' => $category->id, 'author_id' => $author->id, 'title' => $title,
            'department' => 'Test Department', 'location' => $location, 'job_type' => $type,
        ]);
        $make('Quetta Clerk Opening', 'Balochistan', 'Permanent');
        $make('Lahore Contract Opening', 'Lahore', 'Contract');

        $this->get('/jobs?location=Balochistan')->assertSee('Quetta Clerk Opening')->assertDontSee('Lahore Contract Opening');
        $this->get('/jobs?type[]=Contract')->assertSee('Lahore Contract Opening')->assertDontSee('Quetta Clerk Opening');
        $this->get('/jobs/bank-jobs?q=Quetta')->assertSee('Quetta Clerk Opening')->assertDontSee('Lahore Contract Opening');
    }

    public function test_career_advice_filters_by_topic_and_search(): void
    {
        $author = User::first();
        BlogPost::factory()->create(['author_id' => $author->id, 'title' => 'Writing a Strong CV', 'topic' => 'cv-resume']);
        BlogPost::factory()->create(['author_id' => $author->id, 'title' => 'Facing a Panel Interview', 'topic' => 'interview']);

        $this->get('/blog')->assertOk()->assertSee('Career Advice &amp; Guidance', false)->assertSee('Featured Career Guides');
        $this->get('/blog?topic=cv-resume')->assertSee('Writing a Strong CV')->assertDontSee('Facing a Panel Interview');
        $this->get('/blog?q=Panel')->assertSee('Facing a Panel Interview')->assertDontSee('Writing a Strong CV');
    }

    public function test_menu_lists_only_the_kept_sections(): void
    {
        $menu = Str::betweenFirst($this->get('/')->assertOk()->getContent(), '<nav id="m"', '</nav>');

        foreach (['>Jobs<', '>Government Schemes<', '>Beauty &amp; Style<', '>Career Advice<'] as $item) {
            $this->assertStringContainsString($item, $menu);
        }
        foreach (['BISP', 'Education'] as $item) {
            $this->assertStringNotContainsString($item, $menu);
        }

        $this->get('/bisp-8171')->assertNotFound();
        $this->get('/education')->assertNotFound();
        $this->get('/write-for-us')->assertNotFound();
    }

    public function test_disclaimer_only_shows_on_schemes(): void
    {
        $this->get('/government-schemes')->assertSee('<b>Disclaimer:</b>', false);
        $this->get('/government-schemes/solar-schemes')->assertSee('<b>Disclaimer:</b>', false);
        $this->get('/jobs')->assertDontSee('<b>Disclaimer:</b>', false);
    }

    public function test_drafts_and_posts_under_the_wrong_category_are_hidden(): void
    {
        $post = Post::with('category.parent')->first();
        $this->get('/beauty-style/'.$post->slug)->assertNotFound();

        $post->update(['status' => 'draft']);
        $this->get($post->url())->assertNotFound();
    }

    public function test_post_body_is_sanitized_and_outdated_badge_shows(): void
    {
        $post = Post::with('category.parent')->first();
        $post->update(['body' => '<p>safe</p><script>alert(1)</script>', 'is_outdated' => true]);

        $this->get($post->url())
            ->assertSee('<p>safe</p>', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('Outdated');
    }

    public function test_subscribe_validates_and_rejects_duplicates(): void
    {
        $this->postJson('/subscribe', ['email' => 'nope'])->assertUnprocessable();
        $this->postJson('/subscribe', ['email' => 'a@example.com'])->assertOk();
        $this->postJson('/subscribe', ['email' => 'a@example.com'])
            ->assertUnprocessable()
            ->assertJsonPath('errors.email.0', 'This email is already subscribed.');

        $this->assertSame(1, Subscriber::count());
    }

    public function test_subscribe_is_rate_limited(): void
    {
        foreach (range(1, 5) as $attempt) {
            $this->postJson('/subscribe', ['email' => 'nope']);
        }

        $this->postJson('/subscribe', ['email' => 'nope'])->assertTooManyRequests();
    }

    public function test_contact_form_saves_and_notifies(): void
    {
        Mail::fake();

        $this->post('/contact', ['name' => 'A', 'email' => 'a@example.com', 'subject' => 'Hi', 'message' => 'Hello'])
            ->assertRedirect()
            ->assertSessionHas('contact_status');

        $this->assertDatabaseHas('contact_messages', ['subject' => 'Hi']);
        Mail::assertSent(\App\Mail\ContactMessageReceived::class);
    }

    public function test_admin_panel_is_protected(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');

        $this->actingAs(User::where('role', 'author')->first())->get('/admin')->assertForbidden();

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        foreach (['', '/categories', '/posts', '/blog-posts', '/pages', '/faqs', '/subscribers', '/contact-messages', '/users', '/settings'] as $path) {
            $this->get('/admin'.$path)->assertOk();
        }
    }
}
