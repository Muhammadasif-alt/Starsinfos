<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Support\RetiredPosts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    /** /{slug}: a root category, otherwise a static page. */
    public function show(Request $request, string $slug): View
    {
        $category = Category::roots()->where('slug', $slug)->first();

        if ($category) {
            return $this->archive($category, $request);
        }

        return view('page', [
            'page' => Page::published()->where('slug', $slug)->firstOrFail(),
            'pages' => Page::published()->orderBy('id')->get(['id', 'title', 'slug']),
            'categories' => Category::roots()->get(),
        ]);
    }

    /** /{category}/{slug}: a subcategory archive, otherwise a post in that category. */
    public function nested(Request $request, string $category, string $slug): View|RedirectResponse
    {
        if ($target = RetiredPosts::target("$category/$slug")) {
            return redirect($target, 301);
        }

        $root = Category::roots()->where('slug', $category)->firstOrFail();

        $subcategory = $root->children()->with('parent')->where('slug', $slug)->first();

        if ($subcategory) {
            return $this->archive($subcategory, $request);
        }

        $post = Post::with('category.parent', 'author')
            ->published()
            ->where('slug', $slug)
            ->whereIn('category_id', $root->familyIds())
            ->firstOrFail();

        $related = Post::with('category.parent')->latestPublished()
            ->whereKeyNot($post->id)
            ->whereIn('category_id', $root->familyIds())
            ->take(4)
            ->get();

        return view('posts.show', compact('post', 'root', 'related'));
    }

    /** Jobs archive with search and filters on department, location and job type. */
    private function jobs(Category $category, Category $root, Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->query('q')),
            'department' => (string) $request->query('department'),
            'location' => (string) $request->query('location'),
            'type' => array_values(array_intersect((array) $request->query('type', []), Post::JOB_TYPES)),
        ];

        $jobs = fn () => Post::published()->whereIn('category_id', $category->familyIds());

        $posts = $jobs()->with('category.parent')
            ->when($filters['q'] !== '', fn (Builder $query) => $query->search($filters['q']))
            ->when($filters['department'] !== '', fn (Builder $query) => $query->where('department', $filters['department']))
            ->when($filters['location'] !== '', fn (Builder $query) => $query->where('location', $filters['location']))
            ->when($filters['type'], fn (Builder $query, array $types) => $query->whereIn('job_type', $types))
            ->orderByRaw('case when closing_date < ? then 1 else 0 end', [today()->toDateString()])
            ->orderByDesc('published_at')
            ->paginate(15)
            ->withQueryString();

        return view('posts.jobs', [
            'category' => $category,
            'root' => $root,
            'subcategories' => $root->children()->with('parent')->get(),
            'posts' => $posts,
            'filters' => $filters,
            'options' => [
                'departments' => $jobs()->whereNotNull('department')->distinct()->orderBy('department')->pluck('department'),
                'locations' => $jobs()->whereNotNull('location')->distinct()->orderBy('location')->pluck('location'),
            ],
        ]);
    }

    private function archive(Category $category, Request $request): View
    {
        $root = $category->root();

        if ($root->slug === 'jobs') {
            return $this->jobs($category, $root, $request);
        }

        if ($root->slug === 'beauty-style') {
            // The section front page shows a short selection; ?all=1 lists every article.
            $overview = ! $category->parent && ! $request->boolean('all');
            $posts = fn () => Post::with('category.parent')->latestPublished()->whereIn('category_id', $category->familyIds());
            $featured = $overview ? $posts()->take(4)->get() : collect();

            return view('posts.beauty', [
                'category' => $category,
                'root' => $root,
                'overview' => $overview,
                'subcategories' => $root->children()->with('parent')->withCount('publishedPosts')->get(),
                'featured' => $featured,
                'posts' => $posts()->whereNotIn('id', $featured->pluck('id'))->paginate($overview ? 4 : 12)->withQueryString(),
            ]);
        }

        $usesListing = $root->slug === 'government-schemes';

        $posts = Post::with('category.parent')->published()
            ->whereIn('category_id', $category->familyIds())
            ->when($usesListing, fn ($query) => $query->orderByRaw('case when closing_date < ? then 1 else 0 end', [today()->toDateString()]))
            ->orderByDesc('published_at')
            ->paginate($usesListing ? 15 : 12);

        return view($usesListing ? 'posts.schemes' : 'posts.archive', [
            'category' => $category,
            'root' => $root,
            'subcategories' => $root->children()->with('parent')->withCount('publishedPosts')->get(),
            'posts' => $posts,
        ]);
    }
}
