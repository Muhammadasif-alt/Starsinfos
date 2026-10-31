<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect([['loc' => route('home'), 'lastmod' => null], ['loc' => route('blog.index'), 'lastmod' => null]])
            ->concat(Category::with('parent')->get()->map(fn ($item) => $this->entry($item)))
            ->concat(Page::published()->get()->map(fn ($item) => $this->entry($item)))
            ->concat(Post::with('category.parent')->published()->get()->map(fn ($item) => $this->entry($item)))
            ->concat(BlogPost::published()->get()->map(fn ($item) => $this->entry($item)));

        return response()->view('seo.sitemap', compact('urls'))->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $lines = ['User-agent: *', 'Disallow: /admin', 'Allow: /', '', 'Sitemap: '.route('sitemap')];

        return response(implode("\n", $lines)."\n")->header('Content-Type', 'text/plain');
    }

    private function entry(Category|Page|Post|BlogPost $item): array
    {
        return ['loc' => $item->url(), 'lastmod' => $item->updated_at?->toAtomString()];
    }
}
