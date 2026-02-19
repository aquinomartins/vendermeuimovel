<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/models/HomeItems.php';

header('Content-Type: application/json; charset=utf-8');

$groups = ['typeChips', 'metrics', 'readyCards', 'launchCards', 'regionChips', 'testimonials', 'pinsList'];
$response = [];

foreach ($groups as $group) {
    $items = array_values(array_filter(HomeItems::all($group), static fn(array $item): bool => (int) $item['is_visible'] === 1));
    $response[$group] = array_map(static function (array $item): array {
        return [
            'id' => (int) $item['id'],
            'title' => $item['title'],
            'text' => $item['text'],
            'image_url' => $item['image_url'],
            'link_url' => $item['link_url'],
            'badge' => $item['badge'],
            'price' => $item['price'],
            'sort_order' => (int) $item['sort_order'],
        ];
    }, $items);
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
