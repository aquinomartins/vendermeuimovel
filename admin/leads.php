<?php
require __DIR__ . '/includes/layout_top.php';
$rows = Leads::all();

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=leads.csv');
    $out = fopen('php://output', 'wb');
    fputcsv($out, ['id', 'name', 'email', 'phone', 'message', 'source', 'created_at']);
    foreach ($rows as $row) {
        fputcsv($out, $row);
    }
    fclose($out);
    exit;
}
?>
<div class="card"><h2>Leads</h2><a class="btn" href="/admin/leads.php?export=csv">Exportar CSV</a></div>
<table><tr><th>ID</th><th>Nome</th><th>Email</th><th>Telefone</th><th>Mensagem</th><th>Origem</th><th>Data</th></tr>
<?php foreach ($rows as $row): ?>
<tr><td><?= (int)$row['id'] ?></td><td><?= e($row['name']) ?></td><td><?= e($row['email']) ?></td><td><?= e($row['phone']) ?></td><td><?= e($row['message']) ?></td><td><?= e($row['source']) ?></td><td><?= e($row['created_at']) ?></td></tr>
<?php endforeach; ?>
</table>
<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
