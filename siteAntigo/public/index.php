<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/helpers/esc.php';
require_once __DIR__ . '/../app/models/Settings.php';
require_once __DIR__ . '/../app/models/HomeSections.php';

$settings = Settings::map();
$sections = HomeSections::mapByKey();

$title = $settings['site_title'] ?? 'Aurora Imóveis | Seu próximo endereço começa aqui';
$description = $settings['meta_description'] ?? 'Aurora Imóveis conecta você ao imóvel ideal para morar, investir ou vender com segurança.';
$brand = $settings['brand_name'] ?? 'Aurora Imóveis';
$whatsapp = $settings['whatsapp_url'] ?? 'https://wa.me/5511999999999';
$hero = $sections['hero'] ?? ['title' => '', 'subtitle' => '', 'body' => ''];
$finance = $sections['finance'] ?? ['title' => '', 'body' => '', 'button_label' => 'Saiba mais'];
$sell = $sections['sell_cta'] ?? ['title' => '', 'body' => '', 'button_label' => 'Quero anunciar', 'button_url' => $whatsapp];
$work = $sections['work_cta'] ?? ['title' => '', 'body' => '', 'button_label' => 'Quero me candidatar', 'button_url' => '#contato'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= e($description) ?>">
  <title><?= e($title) ?></title>
  <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<a class="skip-link" href="#conteudo-principal">Pular para conteúdo principal</a>
<header class="site-header" id="topo"><div class="container header-content"><a href="#topo" class="logo"><?= e($brand) ?></a><button class="menu-toggle" id="menuToggle" aria-expanded="false" aria-controls="menuDrawer"><span></span><span></span><span></span></button><nav class="desktop-nav" aria-label="Menu principal"><a href="#prontos">Prontos</a><a href="#lancamentos">Lançamentos</a><a href="#venda">Anunciar imóvel</a><a href="#trabalhe">Trabalhe conosco</a><a href="#sobre">Sobre</a><a href="#contato">Contato</a><a href="#consorcio">Consórcio</a></nav><a class="btn btn-whatsapp" href="<?= e($whatsapp) ?>" target="_blank" rel="noopener noreferrer">Falar no WhatsApp</a></div><div class="menu-overlay" id="menuOverlay" hidden></div><aside class="menu-drawer" id="menuDrawer" aria-hidden="true"><div class="drawer-header"><strong>Menu</strong><button class="close-drawer" id="closeDrawer">×</button></div><nav class="drawer-nav"><a href="#prontos">Prontos</a><a href="#lancamentos">Lançamentos</a><a href="#venda">Anunciar imóvel</a><a href="#trabalhe">Trabalhe conosco</a><a href="#sobre">Sobre</a><a href="#contato">Contato</a><a href="#consorcio">Consórcio</a><a class="btn btn-whatsapp" href="<?= e($whatsapp) ?>" target="_blank" rel="noopener noreferrer">Falar no WhatsApp</a></nav></aside></header>
<main id="conteudo-principal">
<section class="hero" id="sobre"><div class="container hero-grid"><div><p class="eyebrow"><?= e($hero['title']) ?></p><h1><?= e($hero['subtitle']) ?></h1><p class="hero-subtitle"><?= e($hero['body']) ?></p></div><div class="search-box" aria-label="Busca de imóveis"><div class="tabs" role="tablist"><button class="tab is-active" role="tab" aria-selected="true" aria-controls="panel-prontos" id="tab-prontos">Prontos</button><button class="tab" role="tab" aria-selected="false" aria-controls="panel-lancamentos" id="tab-lancamentos">Lançamentos</button><button class="tab" role="tab" aria-selected="false" aria-controls="panel-mapa" id="tab-mapa">Busca no mapa</button></div><div class="tab-panels"><section id="panel-prontos" class="tab-panel is-active" role="tabpanel"></section><section id="panel-lancamentos" class="tab-panel" role="tabpanel" hidden></section><section id="panel-mapa" class="tab-panel" role="tabpanel" hidden><p class="map-tab-copy">Explore bairros, compare preços por região e visualize oportunidades em tempo real no nosso mapa interativo.</p><button class="btn btn-secondary" id="openMapFromTab" type="button">Ir para o mapa</button></section></div><button class="btn" type="button" id="searchBtn">Buscar</button><p id="searchStatus" aria-live="polite"></p></div></div></section>
<section class="type-filter" aria-label="Filtrar por tipo"><div class="container"><div class="chips-row" id="typeChips"></div><p id="selectedTypeText"></p></div></section>
<section class="metrics" aria-label="Números da Aurora"><div class="container metrics-grid" id="metricsGrid"></div></section>
<section class="listing" id="prontos"><div class="container"><div class="section-header"><h2 class="section-title">Imóveis prontos para morar</h2></div><div class="cards-grid" id="readyGrid"></div></div></section>
<section class="listing" id="lancamentos"><div class="container"><div class="section-header"><h2 class="section-title">Lançamentos selecionados</h2><button class="btn btn-secondary" id="launchCta" type="button">Receber novidades</button></div><div class="cards-grid" id="launchGrid"></div></div></section>
<section class="map-area" id="mapa"><div class="container"><div class="section-header"><h2 class="section-title">Busca no mapa</h2><button class="btn btn-secondary" id="mapSearchBtn" type="button">Atualizar pinos</button></div><div class="map-placeholder" role="img"><span>🗺️ Prévia do mapa interativo</span></div></div><div class="container"><ul class="pins-list" id="pinsList" aria-live="polite"></ul></div></section>
<section class="regions"><div class="container"><h2 class="section-title">Regiões em destaque</h2><div class="chips-row" id="regionChips"></div></div></section>
<section class="finance" id="consorcio"><div class="container finance-card"><div><h2 class="section-title"><?= e($finance['title']) ?></h2><p><?= e($finance['body']) ?></p></div><button class="btn" id="openFinanceModal" type="button"><?= e($finance['button_label']) ?></button></div></section>
<section class="dual-cta" id="venda"><div class="container dual-grid"><article class="cta-card"><img src="<?= e($sell['image_url'] ?: '/uploads/placeholders.svg') ?>" alt="Ilustração de venda de imóvel"><div><h2 class="section-title"><?= e($sell['title']) ?></h2><p><?= e($sell['body']) ?></p><a href="<?= e($sell['button_url']) ?>" class="btn"><?= e($sell['button_label']) ?></a></div></article><article class="cta-card" id="trabalhe"><img src="<?= e($work['image_url'] ?: '/uploads/placeholders.svg') ?>" alt="Ilustração de recrutamento imobiliário"><div><h2 class="section-title"><?= e($work['title']) ?></h2><p><?= e($work['body']) ?></p><a href="<?= e($work['button_url']) ?>" class="btn btn-secondary"><?= e($work['button_label']) ?></a></div></article></div></section>
<section class="testimonials"><div class="container"><h2 class="section-title">Depoimentos de clientes</h2><div class="testimonial-grid" id="testimonialGrid"></div></div></section>
<section class="features" aria-label="Diferenciais">
  <div class="container">
    <div class="col-md-4 col-sm-4">
      <ul class="features-list" id="featuresList">
        <li>Living inside a nature</li>
        <li>Underground parking</li>
        <li>Easy access for all</li>
        <li>Non-stop security</li>
        <li>Spacious Apartments</li>
      </ul><!-- /.features-list -->
    </div>
  </div>
</section>

</main>
<footer class="site-footer" id="contato"><div class="container footer-bottom"><p>© <span id="currentYear"></span> <?= e($brand) ?>. Todos os direitos reservados.</p></div></footer>
<script src="/js/main.js"></script>
</body>
</html>
