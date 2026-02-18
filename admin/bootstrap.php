<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/helpers/esc.php';
require_once __DIR__ . '/../app/helpers/upload.php';
require_once __DIR__ . '/../app/models/Settings.php';
require_once __DIR__ . '/../app/models/HomeSections.php';
require_once __DIR__ . '/../app/models/HomeItems.php';

ensure_session_started();

function admin_top(string $title): void
{
    echo '<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' . e($title) . '</title><link rel="stylesheet" href="/admin/assets/admin.css"></head><body><div class="admin-wrap">';
    if (current_user()) {
        echo '<nav><a href="/admin/index.php">Dashboard</a><a href="/admin/settings.php">Settings</a><a href="/admin/home.php">Home</a><a href="/admin/items.php">Itens</a><a href="/admin/media.php">Mídia</a><a href="/admin/logout.php">Sair</a></nav>';
    }
}

function admin_bottom(): void
{
    echo '</div><script src="/admin/assets/admin.js"></script></body></html>';
}
