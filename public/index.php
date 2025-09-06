<?php
require __DIR__ . '/../src/bootstrap.php';

$stmt = $pdo->query("SELECT id, name, dept, email, created_at FROM users ORDER BY id DESC LIMIT 10");
$rows = $stmt->fetchAll();
?>

<!DOCTYPE html>
<meta charset="UTF-8">
<h1>社員一覧</h1>    
<table border="1" callpadding="6">
<tr><th>ID</th><th>名前</th><th>部署</th><th>Email</th><th>作成日</th></tr>
<?php foreach ($rows as $r): ?>
    <tr>
        <td><?= htmlspecialchars($r['id']) ?></td>
        <td><?= htmlspecialchars($r['name']) ?></td>
        <td><?= htmlspecialchars($r['dept']) ?></td>
        <td><?= htmlspecialchars($r['email']) ?></td>
        <td><?= htmlspecialchars($r['created_at']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
