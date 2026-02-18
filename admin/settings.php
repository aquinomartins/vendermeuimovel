<?php
require __DIR__ . '/includes/layout_top.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (($_POST['action'] ?? '') === 'delete') {
        Settings::delete(post_int('id'));
    } else {
        Settings::upsert(post_string('key'), post_string('value'));
    }
    header('Location: /admin/settings.php');
    exit;
}

$rows = Settings::all();
?>
<div class="card"><h2>Settings (key/value)</h2>
<form method="post"><?= csrf_field() ?><input type="text" name="key" placeholder="key" required><textarea name="value" placeholder="value"></textarea><button class="btn" type="submit">Salvar</button></form>
</div>
<table><tr><th>Key</th><th>Value</th><th>Ações</th></tr>
<?php foreach ($rows as $row): ?>
<tr><td><?= e($row['key']) ?></td><td><?= e($row['value']) ?></td><td><form method="post" style="display:inline"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$row['id'] ?>"><button class="btn alt" type="submit">Excluir</button></form></td></tr>
<?php endforeach; ?>
</table>
<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
