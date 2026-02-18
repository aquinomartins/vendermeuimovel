<?php require __DIR__ . '/includes/layout_top.php'; ?>
<div class="card">
  <h2>Dashboard</h2>
  <p>Bem-vindo, <?= e(current_user()['name'] ?? 'Admin') ?>.</p>
  <ul>
    <li>Total de páginas: <?= count(Pages::all()) ?></li>
    <li>Total de settings: <?= count(Settings::all()) ?></li>
    <li>Total de leads: <?= count(Leads::all()) ?></li>
  </ul>
</div>
<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
