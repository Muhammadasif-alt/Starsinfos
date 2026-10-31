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
    /** Position of the Blog link in the header menu, after News. */
    private const BLOG_MENU_POSITION = 4;

    private const MENU_ORDER = ['jobs', 'government-schemes', 'bisp-8171', 'news', 'education', 'home-decor'];

    public function compose(View $view): void
    {
        $data = Cache::remember(SiteCache::LAYOUT, SiteCache::TTL, fn () => [
            'footerCategories' => Category::roots()->get(['id', 'name', 'slug']),
            'footerPages' => Page::published()->orderBy('id')->get(['id', 'title', 'slug']),
        ]);

        $menu = $data['footerCategories']
            ->sortBy(fn (Category $category) => array_search($category->slug, self::MENU_ORDER, true))
            ->map(fn (Category $category) => ['label' => $category->name, 'url' => $category->url()])
            ->values();

        $menu->splice(self::BLOG_MENU_POSITION, 0, [['label' => 'Blog', 'url' => route('blog.index')]]);

        $view->with($data + [
            'menu' => $menu->prepend(['label' => 'Home', 'url' => route('home')]),
            'tagline' => Setting::get('tagline'),
        ]);
    }
}
