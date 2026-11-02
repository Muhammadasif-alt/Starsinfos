<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
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
    public function nested(Request $request, string $category, string $slug): View
    {
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

    /** Highlight cards on the Education & Admissions page: subcategory slug => [title, text, call to action, background image]. */
    private const EDUCATION_HIGHLIGHTS = [
        'bs-admissions' => ['Admissions 2026', 'Apply for BS and other degree programs.', 'View Details', 'edu-admissions.jpg'],
        'scholarships' => ['Scholarships', 'Find national and international scholarships.', 'View Opportunities', 'edu-scholarships.jpg'],
        'entry-tests' => ['Entry Tests', 'Prepare for tests such as NTS, ECAT and MDCAT.', 'Start Preparation', 'edu-entry-tests.jpg'],
        'results-merit-lists' => ['Results & Merit Lists', 'Check results and merit lists online.', 'Check Now', 'edu-results.jpg'],
    ];

    private function education(Category $category, Category $root): View
    {
        $subcategories = $root->children()->with('parent')->get();

        $highlights = collect(self::EDUCATION_HIGHLIGHTS)
            ->map(fn (array $copy, string $slug) => ['category' => $subcategories->firstWhere('slug', $slug)] + array_combine(['title', 'text', 'cta', 'image'], $copy))
            ->filter(fn (array $highlight) => $highlight['category'])
            ->values();

        return view('posts.education', [
            'category' => $category,
            'root' => $root,
            'subcategories' => $subcategories,
            'highlights' => $highlights,
            'posts' => Post::with('category.parent')->latestPublished()->whereIn('category_id', $category->familyIds())->paginate(8),
            'admissions' => Admission::whereDate('last_date', '>=', today())->orderBy('sort_order')->get(),
            'scholarships' => $subcategories->firstWhere('slug', 'scholarships'),
        ]);
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

        if ($root->slug === 'education') {
            return $this->education($category, $root);
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
