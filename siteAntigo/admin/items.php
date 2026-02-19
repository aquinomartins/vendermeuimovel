<?php
require_once __DIR__ . '/bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $payload = [
        'group_key' => post_string('group_key'),
        'title' => post_string('title'),
        'text' => post_string('text'),
        'image_url' => post_string('image_url'),
        'link_url' => post_string('link_url'),
        'badge' => post_string('badge'),
        'price' => post_string('price'),
        'sort_order' => post_int('sort_order', 0),
        'is_visible' => isset($_POST['is_visible']) ? 1 : 0,
    ];

    $action = post_string('action');
    if ($action === 'create') HomeItems::create($payload);
    if ($action === 'update') HomeItems::update(post_int('id'), $payload);
    if ($action === 'delete') HomeItems::delete(post_int('id'));

    header('Location: /admin/items.php');
    exit;
}

$rows = HomeItems::all();
admin_top('Itens Home');
?>
<div class="card"><h1>CRUD listas da Home</h1>
<form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="create">
<label>Grupo<select name="group_key"><?php foreach (HomeItems::groups() as $group): ?><option value="<?= e($group) ?>"><?= e($group) ?></option><?php endforeach; ?></select></label>
<label>Título<input name="title"></label><label>Texto<textarea name="text" rows="2"></textarea></label>
<label>Imagem URL<input name="image_url"></label><label>Link URL<input name="link_url"></label>
<label>Badge<input name="badge"></label><label>Preço/Sufixo<input name="price"></label>
<label>Ordem<input type="number" name="sort_order" value="0"></label>
<label><input type="checkbox" name="is_visible" checked> Visível</label><br><button type="submit">Criar item</button>
</form></div>
<div class="card"><table><thead><tr><th>ID</th><th>Grupo</th><th>Título</th><th>Texto</th><th>Ações</th></tr></thead><tbody>
<?php foreach ($rows as $row): ?>
<tr><td><?= (int)$row['id'] ?></td><td><?= e($row['group_key']) ?></td><td><?= e($row['title']) ?></td><td><?= e($row['text']) ?></td><td>
<form method="post" style="display:inline"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$row['id'] ?>"><button data-confirm="Excluir item?" type="submit">Excluir</button></form>
</td></tr>
<?php endforeach; ?></tbody></table></div>
<?php admin_bottom(); ?>
