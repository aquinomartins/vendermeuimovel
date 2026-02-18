<?php
require_once __DIR__ . '/bootstrap.php';
require_login();
admin_top('Dashboard');
?>
<div class="card">
  <h1>Dashboard</h1>
  <p>Bem-vindo, <?= e(current_user()['name'] ?? 'admin') ?>.</p>
  <ul>
    <li>Total de settings: <?= count(Settings::all()) ?></li>
    <li>Total de seções: <?= count(HomeSections::all()) ?></li>
    <li>Total de itens: <?= count(HomeItems::all()) ?></li>
  </ul>
</div>
<?php admin_bottom(); ?>
