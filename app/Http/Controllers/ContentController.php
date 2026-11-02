<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use Illuminate\View\View;

class ContentController extends Controller
{
    /** Root categories shown as a searchable list instead of a card grid. */
    private const LISTING_ROOTS = ['jobs', 'government-schemes'];

    /** /{slug}: a root category, otherwise a static page. */
    public function show(string $slug): View
    {
        $category = Category::roots()->where('slug', $slug)->first();

        if ($category) {
            return $this->archive($category);
        }

        return view('page', [
            'page' => Page::published()->where('slug', $slug)->firstOrFail(),
            'pages' => Page::published()->orderBy('id')->get(['id', 'title', 'slug']),
            'categories' => Category::roots()->get(),
        ]);
    }

    /** /{category}/{slug}: a subcategory archive, otherwise a post in that category. */
    public function nested(string $category, string $slug): View
    {
        $root = Category::roots()->where('slug', $category)->firstOrFail();

        $subcategory = $root->children()->with('parent')->where('slug', $slug)->first();

        if ($subcategory) {
            return $this->archive($subcategory);
        }

        $post = Post::with('category.parent', 'author')
            ->published()
            ->where('slug', $slug)
            ->whereIn('category_id', $root->familyIds())
            ->firstOrFail();

        $related = Post::with('category.parent')->latestPublished()
            ->whereKeyNot($post->id)
            ->whereIn('category_id', $root->familyIds())
            ->take(3)
            ->get();

        return view('posts.show', compact('post', 'root', 'related'));
    }

    private function archive(Category $category): View
    {
        $root = $category->root();

        $usesListing = in_array($root->slug, self::LISTING_ROOTS, true);

        $posts = Post::with('category.parent')->published()
            ->whereIn('category_id', $category->familyIds())
            ->when($usesListing, fn ($query) => $query->orderByRaw('case when closing_date < ? then 1 else 0 end', [today()->toDateString()]))
            ->orderByDesc('published_at')
            ->paginate($usesListing ? 15 : 12);

        return view($usesListing ? 'posts.listing' : 'posts.archive', [
            'category' => $category,
            'root' => $root,
            'subcategories' => $root->children()->with('parent')->get(),
            'posts' => $posts,
        ]);
    }
}
