<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\Post;
use Illuminate\Support\Str;

class JsonLd
{
    public static function article(Post|BlogPost $post): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => $post instanceof BlogPost ? 'BlogPosting' : 'Article',
            'headline' => Str::limit($post->title, 110, ''),
            'description' => $post->seo_description,
            'image' => $post->image_url ? ['@type' => 'ImageObject', 'url' => $post->image_url] : null,
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'inLanguage' => 'en',
            'keywords' => self::keywords($post->tags) ?: null,
            'articleSection' => $post->category?->name,
            'author' => ['@type' => 'Person', 'name' => $post->author->name],
            'publisher' => self::publisher(),
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $post->url()],
        ], fn ($value) => $value !== null);
    }

    public static function publisher(): array
    {
        return [
            '@type' => 'Organization',
            'name' => config('app.name'),
            'url' => url('/'),
            'logo' => ['@type' => 'ImageObject', 'url' => asset('images/logo.png')],
        ];
    }

    private static function keywords(mixed $tags): string
    {
        $tags = is_string($tags) ? explode(',', $tags) : (array) $tags;

        return implode(', ', array_filter(array_map('trim', $tags)));
    }

    /** @param array<string, string|null> $crumbs label => url */
    public static function breadcrumbs(array $crumbs): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($crumbs)->keys()->values()->map(fn (string $label, int $index) => array_filter([
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $label,
                'item' => $crumbs[$label],
            ]))->all(),
        ];
    }

    /** @param iterable<object|array{question: string, answer: string}> $faqs FAQ models or arrays */
    public static function faq(iterable $faqs): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => collect($faqs)->map(fn ($faq) => [
                '@type' => 'Question',
                'name' => data_get($faq, 'question'),
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => data_get($faq, 'answer')],
            ])->values()->all(),
        ];
    }

    private const EMPLOYMENT_TYPES = ['Permanent' => 'FULL_TIME', 'Contract' => 'CONTRACTOR', 'Internship' => 'INTERN'];

    /** Job posting markup for open jobs that name their organization; null otherwise. */
    public static function jobPosting(Post $post): ?array
    {
        if (! $post->department || $post->closing_label === 'Closed') {
            return null;
        }

        $address = array_filter([
            '@type' => 'PostalAddress',
            'addressRegion' => $post->location && $post->location !== 'All Pakistan' ? $post->location : null,
            'addressCountry' => 'PK',
        ]);

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $post->title,
            'description' => $post->safe_body,
            'datePosted' => $post->published_at?->toDateString(),
            'validThrough' => $post->closing_date?->endOfDay()->toIso8601String(),
            'employmentType' => self::EMPLOYMENT_TYPES[$post->job_type] ?? null,
            'hiringOrganization' => ['@type' => 'Organization', 'name' => $post->department, 'sameAs' => $post->source_url],
            'jobLocation' => ['@type' => 'Place', 'address' => $address],
            'directApply' => false,
        ], fn ($value) => $value !== null);
    }
}
