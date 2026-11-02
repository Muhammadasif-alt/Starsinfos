<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Post;
use App\Models\Setting;
use App\Support\SiteCache;
use Illuminate\Database\Eloquent\Builder;
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
        $jobsCategory = $roots->firstWhere('slug', 'jobs');
        $withCategory = ['category.parent'];

        return [
            'categories' => $roots,
            'featured' => Post::with($withCategory)->latestPublished()->where('is_featured', true)->first(),
            'trending' => Post::with($withCategory)->latestPublished()->where('is_trending', true)->take(4)->get(),
            'jobsCategory' => $jobsCategory,
            'jobCategories' => $jobsCategory ? $jobsCategory->children()->with('parent')->get() : collect(),
            'jobs' => $this->openJobs($jobsCategory)->orderByDesc('published_at')->take(6)->get(),
            'newJobs' => $this->openJobs($jobsCategory)->orderByDesc('published_at')->take(3)->get(),
            'bispTools' => Category::with('parent')->whereIn('slug', self::BISP_TOOLS)->orderBy('sort_order')->get(),
            'bispEligibility' => Category::with('parent')->where('slug', '8171-eligibility-guide')->first(),
            'latestPosts' => Post::with($withCategory)->latestPublished()->take(6)->get(),
            'admissions' => Admission::whereDate('last_date', '>=', today())->orderBy('sort_order')->get(),
            'careerPosts' => BlogPost::with('author', 'category')->latestPublished()->orderByDesc('is_featured')->take(3)->get(),
            'faqs' => Faq::where('is_active', true)->orderBy('sort_order')->get(),
        ];
    }

    /** Published jobs that have not passed their closing date. */
    private function openJobs(?Category $jobs): Builder
    {
        return Post::with('category.parent')
            ->published()
            ->whereIn('category_id', $jobs ? $jobs->familyIds() : [])
            ->where(fn ($query) => $query->whereNull('closing_date')->orWhereDate('closing_date', '>=', today()));
    }
}
