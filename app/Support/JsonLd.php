<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\Post;

class JsonLd
{
    public static function article(Post|BlogPost $post): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post->title,
            'description' => $post->seo_description,
            'image' => $post->image_url,
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'author' => ['@type' => 'Person', 'name' => $post->author->name],
            'publisher' => ['@type' => 'Organization', 'name' => config('app.name')],
            'mainEntityOfPage' => $post->url(),
        ];
    }

    /** @param array<string, string|null> $crumbs label => url */
    public static function breadcrumbs(array $crumbs): array
    {
        $position = 0;

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($crumbs)->map(fn (?string $url, string $label) => array_filter([
                '@type' => 'ListItem',
                'position' => ++$position,
                'name' => $label,
                'item' => $url,
            ]))->values()->all(),
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
