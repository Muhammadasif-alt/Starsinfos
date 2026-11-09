<?php

namespace App\Models\Concerns;

use App\Support\ArticleHtml;

/** Applies the article SEO clean-up from ArticleHtml every time a post is saved. */
trait NormalizesArticle
{
    protected static function bootNormalizesArticle(): void
    {
        static::saving(function (self $post) {
            foreach (['title', 'meta_title', 'meta_description', 'excerpt'] as $field) {
                if (is_string($post->{$field})) {
                    $post->{$field} = trim(preg_replace('/\s+/u', ' ', $post->{$field}));
                }
            }

            $body = ArticleHtml::normalize($post->body, (string) $post->title);

            if (empty($post->faqs) && $body) {
                [$body, $faqs] = ArticleHtml::extractFaqs($body);
                if ($faqs) {
                    $post->faqs = $faqs;
                }
            }

            $post->body = $body;

            if (blank($post->excerpt)) {
                $post->excerpt = ArticleHtml::summary($post->body) ?: null;
            }
        });
    }
}
