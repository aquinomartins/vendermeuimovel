<?php

declare(strict_types=1);

function handle_upload(string $fieldName): ?string
{
    if (empty($_FILES[$fieldName]['name'])) {
        return null;
    }

    $file = $_FILES[$fieldName];
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Falha no upload.');
    }

    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];
    $mime = mime_content_type($file['tmp_name']);
    if (!in_array($mime, $allowed, true)) {
        throw new RuntimeException('Tipo de arquivo não permitido.');
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('media_', true) . '.' . strtolower($ext);
    $targetDir = dirname(__DIR__, 2) . '/public/uploads';

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }

    $target = $targetDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('Não foi possível mover o arquivo.');
    }

    return '/uploads/' . $filename;
}
