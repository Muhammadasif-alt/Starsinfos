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
        $this->assertSame(4, Category::whereNull('parent_id')->count());
        $this->assertSame(24, Post::count());
        $this->assertSame(4, BlogPost::count());

        Category::whereNull('parent_id')->get()->each(
            fn (Category $root) => $this->assertSame(6, Post::whereIn('category_id', $root->familyIds())->count(), $root->slug),
        );
    }

    public function test_homepage_renders_all_sections_in_order(): void
    {
        $this->get('/')->assertOk()->assertSeeInOrder([
            'class="hero"', 'class="stats"', 'Find Jobs by Category', 'Latest Jobs', 'Browse Categories', 'Disclaimer', 'Trending Now',
            'How to Apply', 'Latest Posts', 'Admissions Calendar',
            'Career Advice', 'Frequently Asked Questions', '<footer>',
        ], false)->assertSee('FAQPage', false);
    }

    public function test_every_public_url_resolves(): void
    {
        $urls = collect(['/search?q=bisp', '/search', '/blog', '/sitemap.xml', '/robots.txt'])
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
        $this->get('/bisp-8171')->assertOk()->assertDontSee('scheme-cats', false);
        $this->get('/education')->assertOk()->assertSee('edu-updates', false)->assertSee('Admissions Calendar');
        $this->get('/education/entry-tests')->assertOk()->assertSee('edu-updates', false)->assertDontSee('edu-cards', false);
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

    public function test_bisp_is_hidden_from_the_header_menu_but_stays_live(): void
    {
        $response = $this->get('/')->assertOk();

        $this->assertStringNotContainsString('BISP 8171', Str::betweenFirst($response->getContent(), '<nav id="m"', '</nav>'));
        $this->assertStringContainsString('>Career Advice<', Str::betweenFirst($response->getContent(), '<nav id="m"', '</nav>'));
        $response->assertSee('BISP 8171 Quick Tools');

        $this->get('/bisp-8171')->assertOk();
    }

    public function test_disclaimer_only_shows_on_bisp_and_schemes(): void
    {
        $this->get('/bisp-8171')->assertSee('<b>Disclaimer:</b>', false);
        $this->get('/government-schemes/solar-schemes')->assertSee('<b>Disclaimer:</b>', false);
        $this->get('/jobs')->assertDontSee('<b>Disclaimer:</b>', false);
    }

    public function test_drafts_and_posts_under_the_wrong_category_are_hidden(): void
    {
        $post = Post::with('category.parent')->first();
        $this->get('/bisp-8171/'.$post->slug)->assertNotFound();

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

        foreach (['', '/categories', '/posts', '/blog-posts', '/pages', '/admissions', '/faqs', '/subscribers', '/contact-messages', '/users', '/settings'] as $path) {
            $this->get('/admin'.$path)->assertOk();
        }
    }
}
