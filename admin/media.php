<?php
require __DIR__ . '/includes/layout_top.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        $path = handle_upload('media');
        $msg = $path ? 'Upload feito: ' . $path : 'Nenhum arquivo enviado.';
    } catch (Throwable $e) {
        $msg = $e->getMessage();
    }
}

$uploadDir = dirname(__DIR__) . '/public/uploads';
$files = is_dir($uploadDir) ? array_values(array_filter(scandir($uploadDir), static fn(string $file): bool => !in_array($file, ['.', '..'], true))) : [];
?>
<div class="card"><h2>Mídia</h2><?php if ($msg): ?><p><?= e($msg) ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="file" name="media" required><button class="btn" type="submit">Enviar</button></form>
</div>
<table><tr><th>Arquivo</th><th>URL</th></tr>
<?php foreach ($files as $file): ?><tr><td><?= e($file) ?></td><td><a href="/uploads/<?= e($file) ?>" target="_blank">/uploads/<?= e($file) ?></a></td></tr><?php endforeach; ?>
</table>
<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
