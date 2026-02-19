<?php
require_once __DIR__ . '/bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = post_string('action');
    if ($action === 'save') {
        Settings::upsert(post_string('key'), post_string('value'));
    }
    if ($action === 'delete') {
        Settings::delete(post_int('id'));
    }
    header('Location: /admin/settings.php');
    exit;
}

admin_top('Settings');
$rows = Settings::all();
?>
<div class="card"><h1>Settings (chave/valor)</h1>
<form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="save"><label>Chave<input name="key" required></label><label>Valor<textarea name="value" rows="3" required></textarea></label><button type="submit">Salvar</button></form>
</div>
<div class="card"><table><thead><tr><th>ID</th><th>Chave</th><th>Valor</th><th>Ação</th></tr></thead><tbody>
<?php foreach ($rows as $row): ?>
<tr><td><?= (int) $row['id'] ?></td><td><?= e($row['key']) ?></td><td><?= e($row['value']) ?></td><td><form method="post" style="display:inline"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$row['id'] ?>"><button data-confirm="Excluir setting?" type="submit">Excluir</button></form></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php admin_bottom(); ?>
