<?php

declare(strict_types=1);

function handle_upload(string $field): ?string
{
    if (empty($_FILES[$field]['name'])) {
        return null;
    }

    $file = $_FILES[$field];
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Erro no upload.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']) ?: '';
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        'image/svg+xml' => 'svg',
    ];

    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Formato de imagem não permitido.');
    }

    $hash = hash_file('sha256', $file['tmp_name']);
    $filename = $hash . '.' . $allowed[$mime];
    $targetDir = dirname(__DIR__, 2) . '/public/uploads';

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }

    $target = $targetDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('Falha ao salvar arquivo.');
    }

    return '/uploads/' . $filename;
}
