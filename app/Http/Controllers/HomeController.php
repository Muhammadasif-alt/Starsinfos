<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Post;
use App\Models\Setting;
use App\Support\SiteCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    private const BISP_TOOLS = ['cnic-status-check-guide', 'payment-updates', 'registration-dynamic-survey', 'official-portal-guides'];

    public function __invoke(): View
    {
        $data = Cache::remember(SiteCache::HOME, SiteCache::TTL, fn () => $this->homeData());

        return view('home', $data + ['settings' => Setting::allValues()]);
    }

    private function homeData(): array
    {
        $roots = Category::roots()->get();
        $withCategory = ['category.parent'];

        $blogPosts = BlogPost::with('author', 'category.parent')->latestPublished()->get();
        $featuredBlog = $blogPosts->firstWhere('is_featured', true) ?? $blogPosts->first();

        return [
            'categories' => $roots,
            'featured' => Post::with($withCategory)->latestPublished()->where('is_featured', true)->first(),
            'trending' => Post::with($withCategory)->latestPublished()->where('is_trending', true)->take(4)->get(),
            'jobs' => $this->latestJobs($roots->firstWhere('slug', 'jobs')),
            'bispTools' => Category::with('parent')->whereIn('slug', self::BISP_TOOLS)->orderBy('sort_order')->get(),
            'bispEligibility' => Category::with('parent')->where('slug', '8171-eligibility-guide')->first(),
            'latestPosts' => Post::with($withCategory)->latestPublished()->take(6)->get(),
            'admissions' => Admission::whereDate('last_date', '>=', today())->orderBy('sort_order')->get(),
            'decor' => $roots->firstWhere('slug', 'home-decor'),
            'decorTiles' => $this->decorTiles($roots->firstWhere('slug', 'home-decor')),
            'featuredBlog' => $featuredBlog,
            'latestBlogs' => $blogPosts->reject(fn (BlogPost $post) => $post->is($featuredBlog))->take(3),
            'faqs' => Faq::where('is_active', true)->orderBy('sort_order')->get(),
        ];
    }

    private function latestJobs(?Category $jobs)
    {
        if (! $jobs) {
            return collect();
        }

        return Post::with('category.parent')
            ->latestPublished()
            ->whereIn('category_id', $jobs->familyIds())
            ->where(fn ($query) => $query->whereNull('closing_date')->orWhereDate('closing_date', '>=', today()))
            ->take(5)
            ->get();
    }

    private function decorTiles(?Category $decor)
    {
        if (! $decor) {
            return collect();
        }

        return $decor->children()->with('parent')->reorder()->orderByRaw('image is null')->orderBy('sort_order')->take(6)->get();
    }
}
