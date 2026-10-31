<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $activeCategory = $request->query('category');

        $posts = BlogPost::with('author', 'category')->latestPublished()
            ->when($activeCategory, fn ($query, $slug) => $query->whereHas('category', fn ($q) => $q->where('slug', $slug)))
            ->paginate(9)
            ->withQueryString();

        $filters = Category::whereHas('blogPosts', fn ($query) => $query->published())->orderBy('sort_order')->get();

        return view('blog.index', compact('posts', 'filters', 'activeCategory'));
    }

    public function show(string $slug): View
    {
        $post = BlogPost::with('author', 'category')->published()->where('slug', $slug)->firstOrFail();

        $related = BlogPost::with('author', 'category')->latestPublished()
            ->whereKeyNot($post->id)
            ->when($post->category_id, fn ($query, $id) => $query->orderByRaw('category_id = ? desc', [$id]))
            ->take(3)
            ->get();

        return view('blog.show', compact('post', 'related'));
    }

    public function author(User $user): View
    {
        $posts = $user->blogPosts()->with('author', 'category')->latestPublished()->paginate(9);

        abort_if($posts->isEmpty() && $posts->currentPage() === 1, 404);

        return view('blog.author', compact('user', 'posts'));
    }
}
