<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (current_user()) {
    header('Location: /admin/index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (attempt_login(post_string('email'), post_string('password'))) {
        header('Location: /admin/index.php');
        exit;
    }
    $error = 'Credenciais inválidas.';
}
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><title>Login Admin</title><style>body{font-family:Arial;background:#f6f7fb}.box{max-width:380px;margin:60px auto;background:#fff;padding:20px}input{width:100%;padding:8px;margin:6px 0}.btn{padding:10px 12px;background:#0b5fff;border:0;color:#fff;width:100%}</style></head><body>
<div class="box"><h1>Login</h1><?php if ($error): ?><p><?= e($error) ?></p><?php endif; ?>
<form method="post"><?= csrf_field() ?><input type="email" name="email" placeholder="E-mail" required><input type="password" name="password" placeholder="Senha" required><button class="btn" type="submit">Entrar</button></form>
</div></body></html>
