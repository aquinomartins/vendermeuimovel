<?php
require __DIR__ . '/includes/layout_top.php';
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$page = $id ? Pages::find($id) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $payload = [
        'slug' => post_string('slug'),
        'title' => post_string('title'),
        'meta_title' => post_string('meta_title'),
        'meta_description' => post_string('meta_description'),
        'is_published' => post_bool('is_published'),
    ];
    $savedId = Pages::save($payload, $id);
    header('Location: /admin/sections.php?page_id=' . $savedId);
    exit;
}
?>
<div class="card"><h2><?= $id ? 'Editar' : 'Nova' ?> página</h2>
<form method="post"><?= csrf_field() ?>
<input name="slug" value="<?= e($page['slug'] ?? '') ?>" placeholder="slug" required>
<input name="title" value="<?= e($page['title'] ?? '') ?>" placeholder="title" required>
<input name="meta_title" value="<?= e($page['meta_title'] ?? '') ?>" placeholder="meta_title">
<textarea name="meta_description" placeholder="meta_description"><?= e($page['meta_description'] ?? '') ?></textarea>
<label><input type="checkbox" name="is_published" <?= !empty($page['is_published']) ? 'checked' : '' ?>> Publicada</label>
<button class="btn" type="submit">Salvar</button>
</form></div>
<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
