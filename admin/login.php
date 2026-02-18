<?php
require_once __DIR__ . '/bootstrap.php';

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

admin_top('Login Admin');
?>
<div class="card">
  <h1>Login</h1>
  <?php if ($error): ?><p><?= e($error) ?></p><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?>
    <label>E-mail <input type="email" name="email" required></label>
    <label>Senha <input type="password" name="password" required></label>
    <button type="submit">Entrar</button>
  </form>
</div>
<?php admin_bottom(); ?>
