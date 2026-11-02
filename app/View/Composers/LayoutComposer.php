<?php

namespace App\View\Composers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Setting;
use App\Support\SiteCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class LayoutComposer
{
    /** Position of the Blog link in the header menu, after Government Schemes. */
    private const BLOG_MENU_POSITION = 2;

    private const MENU_ORDER = ['jobs', 'government-schemes', 'education'];

    /** Categories that stay live but are left out of the header menu. */
    private const MENU_HIDDEN = ['bisp-8171'];

    /** Pages linked from the copyright line instead of a footer column. */
    private const LEGAL_PAGES = ['contact-us', 'privacy-policy', 'terms-and-conditions'];

    private const FOOTER_JOB_CATEGORIES = 6;

    public function compose(View $view): void
    {
        $data = Cache::remember(SiteCache::LAYOUT, SiteCache::TTL, fn () => [
            'footerCategories' => Category::roots()->get(['id', 'name', 'slug']),
            'footerJobCategories' => Category::where('parent_id', Category::where('slug', 'jobs')->value('id'))
                ->with('parent')
                ->orderBy('sort_order')
                ->take(self::FOOTER_JOB_CATEGORIES)
                ->get(),
            'footerPages' => Page::published()->whereNotIn('slug', self::LEGAL_PAGES)->orderBy('id')->get(['id', 'title', 'slug']),
            'legalPages' => Page::published()->whereIn('slug', self::LEGAL_PAGES)->orderBy('id')->get(['id', 'title', 'slug']),
        ]);

        $menu = $data['footerCategories']
            ->reject(fn (Category $category) => in_array($category->slug, self::MENU_HIDDEN, true))
            ->sortBy(fn (Category $category) => array_search($category->slug, self::MENU_ORDER, true))
            ->map(fn (Category $category) => ['label' => $category->name, 'url' => $category->url()])
            ->values();

        $menu->splice(self::BLOG_MENU_POSITION, 0, [['label' => 'Career Advice', 'url' => route('blog.index')]]);

        $view->with($data + [
            'menu' => $menu->prepend(['label' => 'Home', 'url' => route('home')]),
            'tagline' => Setting::get('tagline'),
            'footerText' => Setting::get('footer_text', Setting::get('tagline')),
        ]);
    }
}
