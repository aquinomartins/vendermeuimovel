<?php

declare(strict_types=1);

const EDITABLE_TAGS = ['p', 'h1', 'h2', 'h3', 'h4'];

function getTemplatePath(): string
{
    return __DIR__ . '/index.html';
}

function getContentStorePath(): string
{
    return __DIR__ . '/content_store.json';
}

/**
 * @return array<string, array{tag:string,order:int,text:string}>
 */
function extractEditableContent(string $html): array
{
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $nodes = $xpath->query('//' . implode('|//', EDITABLE_TAGS));

    $result = [];
    $counters = array_fill_keys(EDITABLE_TAGS, 0);

    if ($nodes !== false) {
        foreach ($nodes as $node) {
            $tag = strtolower($node->nodeName);
            $counters[$tag]++;
            $key = sprintf('%s_%d', $tag, $counters[$tag]);
            $result[$key] = [
                'tag' => $tag,
                'order' => $counters[$tag],
                'text' => trim($node->textContent ?? ''),
            ];
        }
    }

    return $result;
}

/**
 * @return array<string, string>
 */
function loadStoredContent(): array
{
    $path = getContentStorePath();
    if (!file_exists($path)) {
        return [];
    }

    $raw = file_get_contents($path);
    if ($raw === false || trim($raw) === '') {
        return [];
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return [];
    }

    return array_filter($decoded, static fn ($value) => is_string($value));
}

/**
 * @param array<string, string> $content
 */
function saveStoredContent(array $content): bool
{
    $json = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }

    return file_put_contents(getContentStorePath(), $json . PHP_EOL, LOCK_EX) !== false;
}

/**
 * @param array<string, array{tag:string,order:int,text:string}> $defaults
 * @param array<string, string> $stored
 * @return array<string, string>
 */
function buildEffectiveContent(array $defaults, array $stored): array
{
    $effective = [];
    foreach ($defaults as $key => $meta) {
        $effective[$key] = $stored[$key] ?? $meta['text'];
    }

    return $effective;
}

/**
 * @param array<string, string> $content
 */
function renderTemplateWithContent(string $html, array $content): string
{
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $nodes = $xpath->query('//' . implode('|//', EDITABLE_TAGS));

    $counters = array_fill_keys(EDITABLE_TAGS, 0);

    if ($nodes !== false) {
        foreach ($nodes as $node) {
            $tag = strtolower($node->nodeName);
            $counters[$tag]++;
            $key = sprintf('%s_%d', $tag, $counters[$tag]);
            if (!array_key_exists($key, $content)) {
                continue;
            }

            while ($node->firstChild !== null) {
                $node->removeChild($node->firstChild);
            }

            $node->appendChild($dom->createTextNode($content[$key]));
        }
    }

    return $dom->saveHTML() ?: $html;
}
