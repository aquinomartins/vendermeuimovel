<?php
require __DIR__ . '/includes/layout_top.php';
$pageId = (int) ($_GET['page_id'] ?? 0);
if (!$pageId) { echo '<p>Informe page_id.</p>'; require __DIR__ . '/includes/layout_bottom.php'; exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (($_POST['action'] ?? '') === 'delete') {
        Sections::delete(post_int('id'));
    }
    header('Location: /admin/sections.php?page_id=' . $pageId);
    exit;
}

$sections = Sections::byPage($pageId);
?>
<div class="card"><h2>Seções da página #<?= $pageId ?></h2><a class="btn" href="/admin/section_edit.php?page_id=<?= $pageId ?>">Nova seção</a></div>
<table><tr><th>Ordem</th><th>Tipo</th><th>Título</th><th>Visível</th><th>Ações</th></tr>
<?php foreach ($sections as $section): ?>
<tr><td><?= (int)$section['sort_order'] ?></td><td><?= e($section['type']) ?></td><td><?= e($section['title']) ?></td><td><?= (int)$section['is_visible'] ? 'Sim' : 'Não' ?></td><td><a class="btn alt" href="/admin/section_edit.php?id=<?= (int)$section['id'] ?>&page_id=<?= $pageId ?>">Editar</a>
<form method="post" style="display:inline"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$section['id'] ?>"><button class="btn alt" type="submit">Excluir</button></form>
</td></tr>
<?php endforeach; ?>
</table>
<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
