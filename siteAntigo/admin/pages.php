<?php
require __DIR__ . '/includes/layout_top.php';
$pages = Pages::all();
?>
<div class="card"><h2>Páginas</h2><a class="btn" href="/admin/page_edit.php">Nova página</a></div>
<table><tr><th>ID</th><th>Slug</th><th>Título</th><th>Publicado</th><th></th></tr>
<?php foreach ($pages as $page): ?>
<tr><td><?= (int)$page['id'] ?></td><td><?= e($page['slug']) ?></td><td><?= e($page['title']) ?></td><td><?= (int)$page['is_published'] ? 'Sim' : 'Não' ?></td><td><a class="btn alt" href="/admin/page_edit.php?id=<?= (int)$page['id'] ?>">Editar</a> <a class="btn" href="/admin/sections.php?page_id=<?= (int)$page['id'] ?>">Seções</a></td></tr>
<?php endforeach; ?>
</table>
<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
