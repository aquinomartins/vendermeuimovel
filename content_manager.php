<?php

declare(strict_types=1);

const EDITABLE_TEXT_TAGS = ['p', 'h1', 'h2', 'h3', 'h4', 'button', 'footer'];
const EDITABLE_IMAGE_TAG = 'img';
const EDITABLE_LIST_ITEM_QUERY = "//li[contains(concat(' ', normalize-space(@class), ' '), ' editable-feature-item ')]";
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
    $dom->loadHTML(
        '<?xml encoding="UTF-8">' . normalizeToUtf8($html),
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $query = '//' . implode('|//', EDITABLE_TEXT_TAGS) . '|//' . EDITABLE_IMAGE_TAG . '|' . EDITABLE_LIST_ITEM_QUERY;
    $nodes = $xpath->query($query);

    $result = [];
    $counters = array_fill_keys(array_merge(EDITABLE_TEXT_TAGS, [EDITABLE_IMAGE_TAG, 'li']), 0);

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

    $filtered = array_filter($decoded, static fn ($value) => is_string($value));

    return array_map(
        static fn (string $value): string => normalizeToUtf8($value),
        $filtered
    );
}

/**
 * @param array<string, string> $content
 */
function saveStoredContent(array $content): bool
{
    $content = array_map(
        static fn (string $value): string => normalizeToUtf8($value),
        $content
    );

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

        $effective[$key] = normalizeStoredValue($stored[$key] ?? null, $fallback);
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
    $dom->loadHTML(
        '<?xml encoding="UTF-8">' . normalizeToUtf8($html),
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $query = '//' . implode('|//', EDITABLE_TEXT_TAGS) . '|//' . EDITABLE_IMAGE_TAG . '|' . EDITABLE_LIST_ITEM_QUERY;
    $nodes = $xpath->query($query);

    $counters = array_fill_keys(array_merge(EDITABLE_TEXT_TAGS, [EDITABLE_IMAGE_TAG, 'li']), 0);

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

            $node->appendChild($dom->createTextNode(normalizeToUtf8($content[$key])));
        }
    }

    foreach ($dom->childNodes as $childNode) {
        if (
            $childNode->nodeType === XML_PI_NODE
            && $childNode->nodeName === 'xml'
        ) {
            $dom->removeChild($childNode);
            break;
        }
    }

    return normalizeToUtf8($dom->saveHTML() ?: $html);
}

function normalizeToUtf8(string $value): string
{
    if ($value === '') {
        return '';
    }

    if (function_exists('mb_detect_encoding') && function_exists('mb_convert_encoding')) {
        $encoding = mb_detect_encoding($value, ['UTF-8', 'Windows-1252', 'ISO-8859-1'], true);
        if ($encoding === false) {
            return mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
        }

        return mb_convert_encoding($value, 'UTF-8', $encoding);
    }

    if (preg_match('//u', $value) === 1) {
        return $value;
    }

    if (function_exists('iconv')) {
        $converted = iconv('Windows-1252', 'UTF-8//IGNORE', $value);
        if (is_string($converted)) {
            return $converted;
        }
    }

    return utf8_encode($value);
}

function normalizeStoredValue(?string $storedValue, string $fallback): string
{
    if ($storedValue === null || $storedValue === '') {
        return $fallback;
    }

    $normalized = normalizeToUtf8($storedValue);
    $fallbackNormalized = normalizeToUtf8($fallback);

    // Alguns ambientes persistem "�" já convertido para a sequência "ï¿½".
    if (str_contains($normalized, 'ï¿½')) {
        $normalized = str_replace('ï¿½', '�', $normalized);
    }

    // Quando já existe caractere de substituição, a informação original se perdeu.
    // Nestes casos usamos o valor padrão do template para restaurar os acentos.
    if (str_contains($normalized, '�') && !str_contains($fallbackNormalized, '�')) {
        return $fallbackNormalized;
    }

    // Corrige padrão clássico de mojibake (ex.: "imÃ³vel").
    if (preg_match('/Ã.|Â./u', $normalized) === 1) {
        $repaired = normalizeToUtf8(utf8_decode($normalized));
        if ($repaired !== '' && preg_match('/Ã.|Â./u', $repaired) !== 1) {
            $normalized = $repaired;
        }
    }

    return $normalized;
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
