<?php
require_once __DIR__ . '/bootstrap.php';
require_login();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        $path = handle_upload('media_file');
        $message = $path ? 'Upload realizado: ' . $path : 'Nenhum arquivo enviado.';
    } catch (Throwable $e) {
        $message = $e->getMessage();
    }
}

admin_top('Mídia');
?>
<div class="card"><h1>Upload de mídia</h1>
<?php if ($message): ?><p><?= e($message) ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="file" name="media_file" accept="image/*" required><button type="submit">Enviar</button></form>
<p>Arquivos ficam em <code>public/uploads</code>.</p>
</div>
<?php admin_bottom(); ?>
