<?php

declare(strict_types=1);

require __DIR__ . '/content_manager.php';

$template = file_get_contents(getTemplatePath());
if ($template === false) {
    http_response_code(500);
    echo 'Não foi possível carregar index.html.';
    exit;
}

$defaults = extractEditableContent($template);
$stored = loadStoredContent();
$current = buildEffectiveContent($defaults, $stored);
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated = [];
    foreach ($defaults as $key => $_meta) {
        $value = $_POST[$key] ?? ($current[$key] ?? '');
        $updated[$key] = trim((string) $value);
    }

    if (saveStoredContent($updated)) {
        $current = $updated;
        $message = 'Conteúdo salvo com sucesso.';
    } else {
        $message = 'Erro ao salvar conteúdo.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Edição de Conteúdo</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: #fff; border-radius: 8px; padding: 24px; box-shadow: 0 3px 12px rgba(0,0,0,.08); }
        h1 { margin-top: 0; }
        .hint { color: #4b5563; margin-bottom: 20px; }
        .message { padding: 10px 12px; border-radius: 6px; background: #dcfce7; color: #166534; margin-bottom: 16px; }
        .field { margin-bottom: 16px; }
        label { display:block; font-weight: bold; margin-bottom: 6px; }
        small { color: #6b7280; }
        textarea { width: 100%; min-height: 68px; border:1px solid #d1d5db; border-radius: 6px; padding: 10px; resize: vertical; box-sizing: border-box; }
        button { background:#2563eb; color:#fff; border:0; padding:12px 20px; border-radius: 6px; cursor: pointer; }
        button:hover { background:#1d4ed8; }
        .actions { display:flex; gap: 10px; align-items:center; margin-top: 20px; }
        a { color:#2563eb; text-decoration:none; }
    </style>
</head>
<body>
<div class="container">
    <h1>Painel Admin</h1>
    <p class="hint">Edite os conteúdos das tags <code>&lt;p&gt;</code>, <code>&lt;h1&gt;</code>, <code>&lt;h2&gt;</code>, <code>&lt;h3&gt;</code>, <code>&lt;h4&gt;</code> da página inicial.</p>

    <?php if ($message !== null): ?>
        <div class="message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="post">
        <?php foreach ($defaults as $key => $meta): ?>
            <div class="field">
                <label for="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>">
                    <?= strtoupper(htmlspecialchars($meta['tag'], ENT_QUOTES, 'UTF-8')) ?> #<?= (int) $meta['order'] ?>
                </label>
                <small>Original: <?= htmlspecialchars($meta['text'], ENT_QUOTES, 'UTF-8') ?></small>
                <textarea id="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" name="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($current[$key] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
        <?php endforeach; ?>

        <div class="actions">
            <button type="submit">Salvar alterações</button>
            <a href="index.php" target="_blank" rel="noopener noreferrer">Abrir página inicial</a>
        </div>
    </form>
</div>
</body>
</html>
