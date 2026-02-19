<?php
/** @var array $settings */
/** @var array $page */
require_once dirname(__DIR__, 2) . '/helpers/sanitize.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= e($page['meta_description'] ?: 'Landing page') ?>">
  <title><?= e($page['meta_title'] ?: $page['title'] ?: 'Vender Meu Imóvel') ?></title>
  <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
<header class="site-header" id="topo">
  <div class="container header-content">
    <a href="#topo" class="logo"><?= e($settings['site_name'] ?? 'Aurora Imóveis') ?></a>
    <nav class="desktop-nav">
      <a href="#sobre">Sobre</a>
      <a href="#prontos">Prontos</a>
      <a href="#lancamentos">Lançamentos</a>
      <a href="#contato">Contato</a>
    </nav>
    <a class="btn btn-whatsapp" href="<?= e($settings['whatsapp'] ?? 'https://wa.me/5511999999999') ?>" target="_blank" rel="noopener noreferrer">Falar no WhatsApp</a>
  </div>
</header>
<main id="conteudo-principal">
