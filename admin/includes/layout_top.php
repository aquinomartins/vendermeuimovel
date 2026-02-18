<?php require_once __DIR__ . '/bootstrap.php'; require_login(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin</title>
  <style>
    body{font-family:Arial,sans-serif;margin:0;background:#f6f7fb} .wrap{max-width:1080px;margin:0 auto;padding:20px}
    nav a{margin-right:12px} table{width:100%;border-collapse:collapse;background:#fff} th,td{border:1px solid #ddd;padding:8px}
    input,textarea,select{width:100%;padding:8px;margin:4px 0 10px} .btn{padding:8px 12px;background:#0b5fff;color:#fff;border:0;cursor:pointer;text-decoration:none;display:inline-block}
    .btn.alt{background:#555} .card{background:#fff;padding:16px;border-radius:6px;margin-bottom:16px}
  </style>
</head>
<body>
<div class="wrap">
  <h1>Admin</h1>
  <nav>
    <a href="/admin/index.php">Dashboard</a>
    <a href="/admin/settings.php">Settings</a>
    <a href="/admin/pages.php">Pages</a>
    <a href="/admin/media.php">Mídia</a>
    <a href="/admin/leads.php">Leads</a>
    <a href="/admin/logout.php">Sair</a>
  </nav>
  <hr>
