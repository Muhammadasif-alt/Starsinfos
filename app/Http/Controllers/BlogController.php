<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $topics = BlogPost::TOPICS;
        $activeTopic = array_key_exists((string) $request->query('topic'), $topics) ? $request->query('topic') : null;
        $term = trim((string) $request->query('q'));

        $posts = BlogPost::with('author', 'category')->latestPublished()
            ->when($activeTopic, fn ($query, $topic) => $query->where('topic', $topic))
            ->when($term !== '', fn ($query) => $query->search($term))
            ->paginate(9)
            ->withQueryString();

        $filtered = $activeTopic || $term !== '' || $posts->currentPage() > 1;

        return view('blog.index', [
            'posts' => $posts,
            'topics' => $topics,
            'activeTopic' => $activeTopic,
            'term' => $term,
            'topicCounts' => BlogPost::published()->whereNotNull('topic')->selectRaw('topic, count(*) as total')->groupBy('topic')->pluck('total', 'topic'),
            'featured' => $filtered ? collect() : BlogPost::with('category')->published()->orderByDesc('is_featured')->orderByDesc('published_at')->take(3)->get(),
            'resources' => $this->resources(),
        ]);
    }

    /** Resource cards pointing at real guides: slug => [title, text, icon]. Missing guides are skipped. */
    private const RESOURCES = [
        'how-to-write-a-cv-tips-for-jobs-in-pakistan' => ['CV Writing Guide', 'Create a professional CV', 'file'],
        'common-job-interview-questions-and-how-to-answer-them' => ['Interview Checklist', 'Be fully prepared for interviews', 'users'],
        'how-to-fill-a-government-job-application-form-correctly' => ['Job Application Guide', 'Apply for jobs the right way', 'clipboard'],
        'in-demand-skills-for-jobs-in-pakistan' => ['Career Planning Guide', 'Set goals and grow your career', 'chart'],
    ];

    private function resources()
    {
        $posts = BlogPost::published()->whereIn('slug', array_keys(self::RESOURCES))->get()->keyBy('slug');

        return collect(self::RESOURCES)
            ->filter(fn (array $copy, string $slug) => $posts->has($slug))
            ->map(fn (array $copy, string $slug) => ['post' => $posts[$slug]] + array_combine(['title', 'text', 'icon'], $copy))
            ->values();
    }

    public function show(string $slug): View
    {
        $post = BlogPost::with('author', 'category')->published()->where('slug', $slug)->firstOrFail();

        $related = BlogPost::with('author', 'category')->latestPublished()
            ->whereKeyNot($post->id)
            ->when($post->category_id, fn ($query, $id) => $query->orderByRaw('category_id = ? desc', [$id]))
            ->take(4)
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
