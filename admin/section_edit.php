<?php
require __DIR__ . '/includes/layout_top.php';
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$pageId = (int) ($_GET['page_id'] ?? 0);
$section = $id ? Sections::find($id) : null;
if ($section) { $pageId = (int) $section['page_id']; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? 'save_section';

    if ($action === 'save_item') {
        Sections::saveItem([
            'section_id' => post_int('section_id'),
            'title' => post_string('item_title'),
            'text' => post_string('item_text'),
            'image_url' => post_string('image_url'),
            'link_url' => post_string('link_url'),
            'sort_order' => post_int('item_sort_order'),
            'is_visible' => post_bool('item_is_visible'),
        ], post_int('item_id') ?: null);
        header('Location: /admin/section_edit.php?id=' . post_int('section_id') . '&page_id=' . $pageId);
        exit;
    }

    if ($action === 'delete_item') {
        Sections::deleteItem(post_int('item_id'));
        header('Location: /admin/section_edit.php?id=' . post_int('section_id') . '&page_id=' . $pageId);
        exit;
    }

    $savedId = Sections::save([
        'page_id' => post_int('page_id'),
        'type' => post_string('type'),
        'title' => post_string('title'),
        'subtitle' => post_string('subtitle'),
        'sort_order' => post_int('sort_order'),
        'is_visible' => post_bool('is_visible'),
    ], $id);

    header('Location: /admin/section_edit.php?id=' . $savedId . '&page_id=' . $pageId);
    exit;
}

$section = $id ? Sections::find($id) : null;
$items = $section['items'] ?? [];
?>
<div class="card"><h2><?= $id ? 'Editar' : 'Nova' ?> seção</h2>
<form method="post"><?= csrf_field() ?>
<input type="hidden" name="page_id" value="<?= $pageId ?>">
<input name="type" value="<?= e($section['type'] ?? '') ?>" placeholder="type (hero,features,faq...)" required>
<input name="title" value="<?= e($section['title'] ?? '') ?>" placeholder="title">
<textarea name="subtitle" placeholder="subtitle"><?= e($section['subtitle'] ?? '') ?></textarea>
<input type="number" name="sort_order" value="<?= (int)($section['sort_order'] ?? 0) ?>" placeholder="sort_order">
<label><input type="checkbox" name="is_visible" <?= !isset($section['is_visible']) || (int)$section['is_visible'] === 1 ? 'checked' : '' ?>> Visível</label>
<button class="btn" type="submit">Salvar seção</button>
</form></div>

<?php if ($id): ?>
<div class="card"><h3>Items da seção</h3>
<form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="save_item"><input type="hidden" name="section_id" value="<?= $id ?>">
<input name="item_title" placeholder="Título item" required>
<textarea name="item_text" placeholder="Texto"></textarea>
<input name="image_url" placeholder="image_url (/uploads/arquivo.jpg)">
<input name="link_url" placeholder="link_url">
<input type="number" name="item_sort_order" value="0">
<label><input type="checkbox" name="item_is_visible" checked> Visível</label>
<button class="btn" type="submit">Adicionar item</button></form>
<table><tr><th>Ordem</th><th>Título</th><th>Texto</th><th></th></tr>
<?php foreach ($items as $item): ?>
<tr><td><?= (int)$item['sort_order'] ?></td><td><?= e($item['title']) ?></td><td><?= e($item['text']) ?></td><td><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="delete_item"><input type="hidden" name="section_id" value="<?= $id ?>"><input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>"><button class="btn alt" type="submit">Excluir</button></form></td></tr>
<?php endforeach; ?></table></div>
<?php endif; ?>

<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
