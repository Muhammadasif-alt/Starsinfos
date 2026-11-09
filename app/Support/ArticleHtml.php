<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Tidies article HTML written in the admin editor so every post gets the same SEO basics,
 * whoever wrote it:
 *  - images the editor dropped inside headings are moved to just after the heading,
 *  - H1s become H2s (the page title is the only H1),
 *  - images get alt text from their caption, or the article title,
 *  - a "Frequently Asked Questions" section typed into the body becomes structured FAQs.
 */
class ArticleHtml
{
    private const FAQ_HEADING = '#<h2\b[^>]*>(?:(?!</h2>).)*?(?:Frequently\s+Asked\s+Questions|\bFAQs?\b)(?:(?!</h2>).)*?</h2>#is';

    public static function normalize(?string $html, string $title = ''): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        $html = self::liftFiguresOutOfHeadings($html);
        $html = preg_replace('#<(/?)h1\b#i', '<$1h2', $html);

        return self::addAltText($html, $title);
    }

    /**
     * Pulls a typed FAQ section (an H2 followed by H3 questions ending in "?") out of the body.
     *
     * @return array{0: string, 1: list<array{question: string, answer: string}>}
     */
    public static function extractFaqs(string $html): array
    {
        if (! preg_match(self::FAQ_HEADING, $html, $heading, PREG_OFFSET_CAPTURE)) {
            return [$html, []];
        }

        $start = $heading[0][1];
        $afterHeading = $start + strlen($heading[0][0]);
        $nextH2 = stripos($html, '<h2', $afterHeading);
        $section = substr($html, $afterHeading, ($nextH2 === false ? strlen($html) : $nextH2) - $afterHeading);

        preg_match_all('#<h3\b[^>]*>(.*?)</h3>(.*?)(?=<h3\b|$)#is', $section, $items, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        $faqs = [];
        $end = 0;
        foreach ($items as $item) {
            $question = preg_replace('/^\s*(?:Q\s*\d*\s*[:.)-]|\d+\s*[.):-])\s*/i', '', self::text($item[1][0]));
            $answer = self::text($item[2][0]);

            if (! str_ends_with($question, '?') || $answer === '') {
                break;
            }

            $faqs[] = ['question' => $question, 'answer' => $answer];
            $end = $item[0][1] + strlen($item[0][0]);
        }

        if (count($faqs) < 2) {
            return [$html, []];
        }

        // Anything after the last question (e.g. a closing paragraph under a non-question H3) stays.
        $rest = substr($section, $end);

        return [substr($html, 0, $start).$rest.substr($html, $afterHeading + strlen($section)), $faqs];
    }

    /** One or two sentences for cards and search results, cut at a word boundary. */
    public static function summary(?string $html, int $limit = 200): string
    {
        preg_match_all('#<p\b[^>]*>(.*?)</p>#is', (string) $html, $paragraphs);
        $first = collect($paragraphs[1])->map(fn (string $p) => self::text($p))->first(fn (string $text) => mb_strlen($text) >= 40)
            ?? self::text((string) $html);

        return Str::limit($first, $limit, '…', preserveWords: true);
    }

    private static function liftFiguresOutOfHeadings(string $html): string
    {
        return preg_replace_callback('#<(h[1-6])\b([^>]*)>(.*?)</\1>#is', function (array $m) {
            if (! preg_match_all('#<figure\b.*?</figure>#is', $m[3], $figures)) {
                return $m[0];
            }

            $text = trim(preg_replace('#<figure\b.*?</figure>#is', '', $m[3]));
            $heading = self::text($text) === '' ? '' : "<{$m[1]}{$m[2]}>{$text}</{$m[1]}>";

            return $heading.implode('', $figures[0]);
        }, $html);
    }

    private static function addAltText(string $html, string $title): string
    {
        $html = preg_replace_callback('#<figure\b.*?</figure>#is', function (array $m) {
            $caption = preg_match('#<figcaption\b[^>]*>(.*?)</figcaption>#is', $m[0], $c) ? self::text($c[1]) : '';

            return $caption === '' ? $m[0] : self::fillAlt($m[0], $caption);
        }, $html);

        return $title === '' ? $html : self::fillAlt($html, $title);
    }

    private static function fillAlt(string $html, string $alt): string
    {
        return preg_replace_callback('#<img\b(?![^>]*\balt=")([^>]*?)(/?)>#i', fn (array $m) => '<img alt="'.e($alt).'"'.$m[1].$m[2].'>', $html);
    }

    private static function text(string $html): string
    {
        return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    }
}
