<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use Illuminate\View\View;

class ContentController extends Controller
{
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

        $posts = Post::with('category.parent')->latestPublished()
            ->whereIn('category_id', $category->familyIds())
            ->paginate(12);

        return view('posts.archive', [
            'category' => $category,
            'root' => $root,
            'subcategories' => $root->children()->with('parent')->get(),
            'posts' => $posts,
        ]);
    }
}
