<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $term = trim((string) $request->query('q'));

        $posts = Post::with('category.parent')->latestPublished()
            ->when($term !== '', fn ($query) => $query->search($term))
            ->paginate(12, ['*'], 'posts_page')
            ->withQueryString();

        $blogPosts = BlogPost::with('author', 'category')->latestPublished()
            ->when($term !== '', fn ($query) => $query->search($term))
            ->paginate(6, ['*'], 'blog_page')
            ->withQueryString();

        return view('search', compact('term', 'posts', 'blogPosts'));
    }
}
