<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/helpers/sanitize.php';
require_once __DIR__ . '/../app/models/Pages.php';
require_once __DIR__ . '/../app/models/Sections.php';
require_once __DIR__ . '/../app/models/Settings.php';
require_once __DIR__ . '/../app/models/Leads.php';

ensure_session_started();

$leadMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'lead_create') {
    verify_csrf();
    Leads::create([
        'name' => post_string('name'),
        'email' => post_string('email'),
        'phone' => post_string('phone'),
        'message' => post_string('message'),
        'source' => 'landing',
    ]);
    $leadMessage = 'Obrigado! Recebemos seu contato.';
}

$page = Pages::bySlug('home') ?? [
    'id' => 0,
    'slug' => 'home',
    'title' => 'Aurora Imóveis',
    'meta_title' => 'Aurora Imóveis | Seu próximo endereço começa aqui',
    'meta_description' => 'Atendimento consultivo para compra e venda de imóveis',
    'is_published' => 1,
];

$sections = !empty($page['id']) ? Sections::byPage((int) $page['id']) : [];
if (!$sections) {
    $sections = [
        ['type' => 'hero', 'title' => 'Bem-vindo', 'subtitle' => 'Seu imóvel ideal', 'is_visible' => 1, 'items' => [['title' => 'Descubra seu novo lar', 'text' => 'Na Aurora Imóveis...', 'link_url' => '#contato', 'image_url' => '', 'is_visible' => 1]]],
    ];
}

$settings = Settings::map();

require __DIR__ . '/../app/views/home.php';
