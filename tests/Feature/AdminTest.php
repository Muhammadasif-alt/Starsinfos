<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSettings;
use App\Filament\Resources\PostResource\Pages\CreatePost;
use App\Models\Category;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    private function postData(array $overrides = []): array
    {
        return $overrides + [
            'title' => 'Admin Created Post',
            'slug' => 'admin-created-post',
            'body' => '<p>Body</p>',
            'status' => 'published',
            'published_at' => now()->subMinute()->toDateTimeString(),
            'category_id' => Category::where('slug', 'bank-jobs')->value('id'),
            'author_id' => auth()->id(),
        ];
    }

    public function test_admin_can_create_a_post_that_goes_live(): void
    {
        Livewire::test(CreatePost::class)
            ->fillForm($this->postData())
            ->call('create')
            ->assertHasNoFormErrors();

        $this->get('/')->assertSee('Admin Created Post');
        $this->get(Post::where('slug', 'admin-created-post')->first()->url())->assertOk();
    }

    public function test_section_pages_load_and_only_list_their_own_posts(): void
    {
        foreach (['', '/jobs', '/jobs/create', '/government-schemes', '/government-schemes/create', '/education-posts', '/education-posts/create', '/bisp', '/bisp/create', '/blog-posts/create'] as $path) {
            $this->get('/admin'.$path)->assertOk();
        }

        $this->get('/admin/jobs')->assertSee('Bank Jobs')->assertDontSee('Housing Schemes Application Process');
    }

    public function test_admin_can_add_a_job_from_the_jobs_section(): void
    {
        Livewire::test(\App\Filament\Resources\JobResource\Pages\CreateJob::class)
            ->fillForm($this->postData(['title' => 'Clerk Jobs Quetta', 'slug' => 'clerk-jobs-quetta', 'department' => 'Test Board', 'location' => 'Quetta', 'job_type' => 'Contract']))
            ->call('create')
            ->assertHasNoFormErrors();

        $this->get('/jobs?location=Quetta')->assertSee('Clerk Jobs Quetta');
    }

    public function test_post_slug_cannot_shadow_a_subcategory(): void
    {
        Livewire::test(CreatePost::class)
            ->fillForm($this->postData(['title' => 'Bank Jobs', 'slug' => 'bank-jobs']))
            ->call('create')
            ->assertHasFormErrors(['slug']);
    }

    public function test_settings_save_and_reach_the_homepage(): void
    {
        $this->get('/')->assertOk();

        Livewire::test(ManageSettings::class)
            ->fillForm(['hero_title' => 'Updated hero title'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Updated hero title', Setting::get('hero_title'));
        $this->get('/')->assertSee('Updated hero title');
    }
}
