<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\Post;
use Illuminate\Support\Collection;

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

    public static function faq(Collection $faqs): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faqs->map(fn ($faq) => [
                '@type' => 'Question',
                'name' => $faq->question,
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq->answer],
            ])->all(),
        ];
    }
}
