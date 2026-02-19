<?php
require_once __DIR__ . '/bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    HomeSections::upsert([
        'section_key' => post_string('section_key'),
        'title' => post_string('title'),
        'subtitle' => post_string('subtitle'),
        'body' => post_string('body'),
        'image_url' => post_string('image_url'),
        'button_label' => post_string('button_label'),
        'button_url' => post_string('button_url'),
        'is_visible' => isset($_POST['is_visible']) ? 1 : 0,
        'sort_order' => post_int('sort_order', 0),
    ]);
    header('Location: /admin/home.php');
    exit;
}

$sections = HomeSections::all();
admin_top('Editor Home');
?>
<div class="card"><h1>Home por seções</h1>
<form method="post"><?= csrf_field() ?>
<label>section_key<input name="section_key" placeholder="hero" required></label>
<label>Título<input name="title"></label><label>Subtítulo<input name="subtitle"></label>
<label>Body<textarea name="body" rows="4"></textarea></label>
<label>Imagem URL<input name="image_url"></label>
<label>Botão Label<input name="button_label"></label><label>Botão URL<input name="button_url"></label>
<label>Ordem<input type="number" name="sort_order" value="0"></label>
<label><input type="checkbox" name="is_visible" checked> Visível</label><br>
<button type="submit">Salvar seção</button></form></div>
<div class="card"><table><thead><tr><th>section_key</th><th>title</th><th>visible</th></tr></thead><tbody><?php foreach($sections as $s): ?><tr><td><?= e($s['section_key']) ?></td><td><?= e($s['title']) ?></td><td><?= (int)$s['is_visible'] ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php admin_bottom(); ?>
