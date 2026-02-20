<?php

declare(strict_types=1);

session_start();

require __DIR__ . '/content_manager.php';

const ADMIN_USERNAME = 'admin';
const ADMIN_PASSWORD = '123456';

$isAuthenticated = ($_SESSION['admin_authenticated'] ?? false) === true;
$message = null;
$isLoginError = false;

if (($_POST['action'] ?? '') === 'logout') {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    header('Location: admin.php');
    exit;
}

if (!$isAuthenticated && ($_POST['action'] ?? '') === 'login') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        session_regenerate_id(true);
        $_SESSION['admin_authenticated'] = true;
        header('Location: admin.php');
        exit;
    }

    $message = 'Usuário ou senha inválidos.';
    $isLoginError = true;
}

$isAuthenticated = ($_SESSION['admin_authenticated'] ?? false) === true;

if (!$isAuthenticated) {
    ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 20px; }
        .container { max-width: 420px; margin: 60px auto; background: #fff; border-radius: 8px; padding: 24px; box-shadow: 0 3px 12px rgba(0,0,0,.08); }
        h1 { margin-top: 0; margin-bottom: 8px; }
        .hint { color: #4b5563; margin-bottom: 20px; }
        .message { padding: 10px 12px; border-radius: 6px; margin-bottom: 16px; }
        .message.error { background: #fee2e2; color: #991b1b; }
        .field { margin-bottom: 16px; }
        label { display:block; font-weight: bold; margin-bottom: 6px; }
        input { width: 100%; border:1px solid #d1d5db; border-radius: 6px; padding: 10px; box-sizing: border-box; }
        button { width:100%; background:#2563eb; color:#fff; border:0; padding:12px 20px; border-radius: 6px; cursor: pointer; }
        button:hover { background:#1d4ed8; }
        .credentials { margin-top: 14px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
<div class="container">
    <h1>Acesso Admin</h1>
    <p class="hint">Entre com usuário e senha para acessar o painel.</p>

    <?php if ($message !== null): ?>
        <div class="message <?= $isLoginError ? 'error' : '' ?>"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="action" value="login">
        <div class="field">
            <label for="username">Usuário</label>
            <input id="username" type="text" name="username" required>
        </div>
        <div class="field">
            <label for="password">Senha</label>
            <input id="password" type="password" name="password" required>
        </div>
        <button type="submit">Entrar</button>
    </form>

    <p class="credentials">Credenciais padrão: <strong>admin</strong> / <strong>123456</strong></p>
</div>
</body>
</html>
<?php
    exit;
}

$template = file_get_contents(getTemplatePath());
if ($template === false) {
    http_response_code(500);
    echo 'Não foi possível carregar index.html.';
    exit;
}

$defaults = extractEditableContent($template);
$stored = loadStoredContent();
$current = buildEffectiveContent($defaults, $stored);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated = [];
    foreach ($defaults as $key => $meta) {
        if (($meta['type'] ?? 'text') === 'image') {
            $uploadedPath = handleImageUpload($_FILES['upload_' . $key] ?? [], $key);
            if ($uploadedPath !== null) {
                $updated[$key] = $uploadedPath;
                continue;
            }

            $value = $_POST[$key] ?? ($current[$key] ?? '');
            $updated[$key] = trim((string) $value);
            continue;
        }

        if (($meta['type'] ?? 'text') === 'link') {
            $hrefValue = $_POST[$key . '__href'] ?? ($current[$key . '__href'] ?? '');
            $textValue = $_POST[$key . '__text'] ?? ($current[$key . '__text'] ?? '');
            $updated[$key . '__href'] = trim((string) $hrefValue);
            $updated[$key . '__text'] = trim((string) $textValue);
            continue;
        }

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
        input[type="text"], input[type="file"] { width: 100%; border:1px solid #d1d5db; border-radius: 6px; padding: 10px; box-sizing: border-box; margin-top: 6px; margin-bottom: 8px; }
        button { background:#2563eb; color:#fff; border:0; padding:12px 20px; border-radius: 6px; cursor: pointer; }
        button:hover { background:#1d4ed8; }
        .actions { display:flex; gap: 10px; align-items:center; margin-top: 20px; }
        a { color:#2563eb; text-decoration:none; }
        .logout { background: #ef4444; }
        .logout:hover { background: #dc2626; }
    </style>
</head>
<body>
<div class="container">
    <h1>Painel Admin</h1>
    <p class="hint">Edite os conteúdos das tags de texto, links (<code>&lt;a href&gt;</code> e texto interno) e atualize os caminhos <code>src</code> das imagens da página inicial. Você também pode enviar um novo arquivo para cada <code>&lt;img&gt;</code>.</p>

    <?php if ($message !== null): ?>
        <div class="message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <?php foreach ($defaults as $key => $meta): ?>
            <div class="field">
                <label for="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>">
                    <?= strtoupper(htmlspecialchars($meta['tag'], ENT_QUOTES, 'UTF-8')) ?> #<?= (int) $meta['order'] ?>
                </label>

                <?php if (($meta['type'] ?? 'text') === 'image'): ?>
                    <small>Original src: <?= htmlspecialchars($meta['src'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                    <input id="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" type="text" name="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars($current[$key] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <small>Enviar nova imagem (sobrescreve o src acima):</small>
                    <input type="file" name="<?= htmlspecialchars('upload_' . $key, ENT_QUOTES, 'UTF-8') ?>" accept="image/*">
                <?php elseif (($meta['type'] ?? 'text') === 'link'): ?>
                    <small>Original href: <?= htmlspecialchars($meta['href'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                    <input id="<?= htmlspecialchars($key . '__href', ENT_QUOTES, 'UTF-8') ?>" type="text" name="<?= htmlspecialchars($key . '__href', ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars($current[$key . '__href'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <small>Texto original: <?= htmlspecialchars($meta['text'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                    <textarea id="<?= htmlspecialchars($key . '__text', ENT_QUOTES, 'UTF-8') ?>" name="<?= htmlspecialchars($key . '__text', ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($current[$key . '__text'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                <?php else: ?>
                    <small>Original: <?= htmlspecialchars($meta['text'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                    <textarea id="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" name="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($current[$key] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <div class="actions">
            <button type="submit">Salvar alterações</button>
            <a href="index.php" target="_blank" rel="noopener noreferrer">Abrir página inicial</a>
            <button class="logout" type="submit" name="action" value="logout" formnovalidate>Sair</button>
        </div>
    </form>
</div>
</body>
</html>
