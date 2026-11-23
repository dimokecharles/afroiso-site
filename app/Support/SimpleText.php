<?php

namespace App\Support;

/**
 * Turns plain text typed in the admin panel into safe HTML.
 *
 * "## " starts a heading and "### " a smaller heading. Lines starting with "- " make a bulleted list and
 * lines starting with "1. ", "2. " and so on make a numbered list. Anything else is a paragraph, and an
 * empty line separates paragraphs. All other characters are shown exactly as typed.
 */
class SimpleText
{
    public static function toHtml(?string $text): string
    {
        $html = '';
        $paragraph = [];
        $items = [];
        $listTag = 'ul';

        $flush = function () use (&$html, &$paragraph, &$items, &$listTag) {
            if ($paragraph) {
                $html .= '<p>' . implode('<br>', $paragraph) . '</p>';
                $paragraph = [];
            }
            if ($items) {
                $html .= '<' . $listTag . '><li>' . implode('</li><li>', $items) . '</li></' . $listTag . '>';
                $items = [];
            }
        };

        foreach (explode("\n", str_replace(["\r\n", "\r"], "\n", trim((string) $text))) as $line) {
            $line = trim($line);

            if ($line === '') {
                $flush();
            } elseif (str_starts_with($line, '### ')) {
                $flush();
                $html .= '<h3>' . e(substr($line, 4)) . '</h3>';
            } elseif (str_starts_with($line, '## ')) {
                $flush();
                $html .= '<h2>' . e(substr($line, 3)) . '</h2>';
            } elseif (str_starts_with($line, '- ') || preg_match('/^\d+\. /', $line)) {
                $tag = str_starts_with($line, '- ') ? 'ul' : 'ol';
                if ($paragraph || ($items && $tag !== $listTag)) {
                    $flush();
                }
                $listTag = $tag;
                $items[] = e(preg_replace('/^(- |\d+\. )/', '', $line));
            } else {
                if ($items) {
                    $flush();
                }
                $paragraph[] = e($line);
            }
        }
        $flush();

        return $html;
    }

    /** The reverse, for loading built-in page text into an editor: headings, lists and paragraphs become plain text. */
    public static function fromHtml(string $html): string
    {
        $html = preg_replace('/\s+/', ' ', $html);
        $html = preg_replace_callback('/<ol[^>]*>(.*?)<\/ol>/i', function ($match) {
            $number = 0;

            return preg_replace_callback('/<li[^>]*>(.*?)<\/li>/i', function ($item) use (&$number) {
                return "\n" . (++$number) . '. ' . trim($item[1]);
            }, $match[1]) . "\n\n";
        }, $html);
        $html = preg_replace('/<li[^>]*>(.*?)<\/li>/i', "\n- $1", $html);
        $html = preg_replace('/<h2[^>]*>(.*?)<\/h2>/i', "\n\n## $1\n\n", $html);
        $html = preg_replace('/<h3[^>]*>(.*?)<\/h3>/i', "\n\n### $1\n\n", $html);
        $html = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $html = preg_replace('/<\/(p|ul)>/i', "\n\n", $html);

        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $lines = array_map('trim', explode("\n", $text));

        return trim(preg_replace("/\n{3,}/", "\n\n", implode("\n", $lines)));
    }
}
