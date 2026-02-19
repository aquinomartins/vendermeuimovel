<?php
/** @var array $page */
/** @var array $sections */
/** @var array $settings */
/** @var string $leadMessage */

require __DIR__ . '/partials/header.php';

$visibleSections = array_filter($sections, static fn(array $s): bool => (int) $s['is_visible'] === 1);

foreach ($visibleSections as $section):
    $type = $section['type'];
    $items = array_filter($section['items'], static fn(array $i): bool => (int) $i['is_visible'] === 1);
?>
<section class="<?= e($type === 'hero' ? 'hero' : 'property-section') ?>" id="<?= e($type) ?>">
  <div class="container">
    <h2><?= e($section['title'] ?: ucfirst($type)) ?></h2>
    <?php if (!empty($section['subtitle'])): ?><p><?= e($section['subtitle']) ?></p><?php endif; ?>

    <?php if ($type === 'hero' && !empty($items)): $hero = $items[array_key_first($items)]; ?>
      <h1><?= e($hero['title'] ?: 'Descubra seu novo lar') ?></h1>
      <p><?= e($hero['text'] ?: 'Conteúdo padrão do hero.') ?></p>
      <?php if (!empty($hero['link_url'])): ?><a class="btn" href="<?= e($hero['link_url']) ?>">Saiba mais</a><?php endif; ?>
    <?php else: ?>
      <div class="cards-grid">
        <?php foreach ($items as $item): ?>
          <article class="card">
            <div class="card-content">
              <?php if (!empty($item['image_url'])): ?><img src="<?= e($item['image_url']) ?>" alt="<?= e($item['title']) ?>" style="width:100%;max-height:200px;object-fit:cover"><?php endif; ?>
              <h3><?= e($item['title']) ?></h3>
              <p><?= e($item['text']) ?></p>
              <?php if (!empty($item['link_url'])): ?><a class="btn btn-secondary" href="<?= e($item['link_url']) ?>">Ver mais</a><?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php endforeach; ?>

<section class="property-section" id="lead-form">
  <div class="container">
    <h2>Fale com a gente</h2>
    <?php if ($leadMessage): ?><p><?= e($leadMessage) ?></p><?php endif; ?>
    <form method="post" action="/index.php#lead-form">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="lead_create">
      <input type="text" name="name" placeholder="Nome" required>
      <input type="email" name="email" placeholder="E-mail" required>
      <input type="text" name="phone" placeholder="Telefone">
      <textarea name="message" placeholder="Mensagem"></textarea>
      <button class="btn" type="submit">Enviar</button>
    </form>
  </div>
</section>

<?php require __DIR__ . '/partials/footer.php';
