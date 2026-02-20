<?php

declare(strict_types=1);

const EDITABLE_TEXT_TAGS = ['p', 'h1', 'h2', 'h3', 'h4', 'button', 'footer'];
const EDITABLE_IMAGE_TAG = 'img';
const ALLOWED_IMAGE_MIME_TYPES = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp',
    'image/svg+xml' => 'svg',
];

function getTemplatePath(): string
{
    return __DIR__ . '/index.html';
}

function getContentStorePath(): string
{
    return __DIR__ . '/content_store.json';
}

function getUploadsDirPath(): string
{
    return __DIR__ . '/uploads';
}

function getUploadsPublicPath(): string
{
    return 'uploads';
}

function ensureUploadsDirExists(): bool
{
    $path = getUploadsDirPath();
    if (is_dir($path)) {
        return true;
    }

    return mkdir($path, 0775, true);
}

/**
 * @return array<string, array{tag:string,order:int,type:string,text?:string,src?:string}>
 */
function extractEditableContent(string $html): array
{
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $query = '//' . implode('|//', EDITABLE_TEXT_TAGS) . '|//' . EDITABLE_IMAGE_TAG;
    $nodes = $xpath->query($query);

    $result = [];
    $counters = array_fill_keys(array_merge(EDITABLE_TEXT_TAGS, [EDITABLE_IMAGE_TAG]), 0);

    if ($nodes !== false) {
        foreach ($nodes as $node) {
            $tag = strtolower($node->nodeName);
            $counters[$tag]++;
            $key = sprintf('%s_%d', $tag, $counters[$tag]);

            if ($tag === EDITABLE_IMAGE_TAG) {
                $result[$key] = [
                    'tag' => $tag,
                    'order' => $counters[$tag],
                    'type' => 'image',
                    'src' => trim((string) $node->getAttribute('src')),
                ];
                continue;
            }

            $result[$key] = [
                'tag' => $tag,
                'order' => $counters[$tag],
                'type' => 'text',
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
 * @param array<string, array{tag:string,order:int,type:string,text?:string,src?:string}> $defaults
 * @param array<string, string> $stored
 * @return array<string, string>
 */
function buildEffectiveContent(array $defaults, array $stored): array
{
    $effective = [];
    foreach ($defaults as $key => $meta) {
        $fallback = $meta['type'] === 'image'
            ? (string) ($meta['src'] ?? '')
            : (string) ($meta['text'] ?? '');

        $effective[$key] = $stored[$key] ?? $fallback;
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
    $query = '//' . implode('|//', EDITABLE_TEXT_TAGS) . '|//' . EDITABLE_IMAGE_TAG;
    $nodes = $xpath->query($query);

    $counters = array_fill_keys(array_merge(EDITABLE_TEXT_TAGS, [EDITABLE_IMAGE_TAG]), 0);

    if ($nodes !== false) {
        foreach ($nodes as $node) {
            $tag = strtolower($node->nodeName);
            $counters[$tag]++;
            $key = sprintf('%s_%d', $tag, $counters[$tag]);
            if (!array_key_exists($key, $content)) {
                continue;
            }

            if ($tag === EDITABLE_IMAGE_TAG) {
                $node->setAttribute('src', $content[$key]);
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

function handleImageUpload(array $file, string $fieldKey): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return null;
    }

    $tmpName = (string) ($file['tmp_name'] ?? '');
    if ($tmpName === '' || !is_uploaded_file($tmpName)) {
        return null;
    }

    $mimeType = mime_content_type($tmpName) ?: '';
    if (!array_key_exists($mimeType, ALLOWED_IMAGE_MIME_TYPES)) {
        return null;
    }

    if (!ensureUploadsDirExists()) {
        return null;
    }

    $extension = ALLOWED_IMAGE_MIME_TYPES[$mimeType];
    $safeFieldKey = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $fieldKey) ?: 'image';
    $filename = sprintf('%s_%s.%s', $safeFieldKey, bin2hex(random_bytes(6)), $extension);
    $destination = getUploadsDirPath() . '/' . $filename;

    if (!move_uploaded_file($tmpName, $destination)) {
        return null;
    }

    return getUploadsPublicPath() . '/' . $filename;
}
