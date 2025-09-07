<?php
require __DIR__ . '/../src/bootstrap.php';

$err = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $dept = trim($_POST['dept'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name === '' || mb_strlen($name) > 50) {
        $err[] = '名前は1～50文字で入力してください。';
    }    
    if ($email === '' || mb_strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err[] = 'メールアドレスの形式が正しくありません。';
    }

    if (!$err) {
        try {
            $stmt = $pdo->prepare('INSERT INTO users (name, dept, email) VALUES (?, ?, ?)');
            $stmt->execute([$name, $dept, $email]);
            redirect('/'); // 成功したらリダイレクト
            // echo 'INSERT OK';
            // exit;
        } catch (Throwable $e) {
            $err[] = '登録できませんでした。: ' . e($e->getMessage());
        }
    }

    
}

$stmt = $pdo->query('SELECT id, name, dept, email, created_at FROM users ORDER BY id DESC LIMIT 10');
$rows = $stmt->fetchAll();
?>

<!DOCTYPE html>
<meta charset="UTF-8">
<title>社員一覧</title>
<h1>社員一覧</h1>    
<!-- 追加フォーム -->
 <form method="post" style="margin: 12px 0; display:flex; gap:8px; align-items:flex-end; flex-wrap:wrap">
    <label>名前<br><input name="name" value="<?= e($_POST['name'] ?? '') ?>" required></label>
    <label>部署<br><input name="dept" value="<?= e($_POST['dept'] ?? '') ?>" required></label>
    <label>Email<br><input name="email" type="email" value="<?= e($_POST['email'] ?? '') ?>" required></label>
    <button type="submit">追加</button>
 </form>
<?php if(!empty($err)): ?>
    <div style="color: #b00; margin:6px 0">
        <?php foreach ($err as $m): ?>
            <div>・<?= e($m) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- 一覧テーブル -->
<table border="1" cellpadding="6">
<tr><th>ID</th><th>名前</th><th>部署</th><th>Email</th><th>作成日</th></tr>
<?php foreach ($rows as $r): ?>
    <tr>
        <td><?= htmlspecialchars($r['id']) ?></td>
        <td><?= htmlspecialchars($r['name']) ?></td>
        <td><?= htmlspecialchars($r['dept']) ?></td>
        <td><?= htmlspecialchars($r['email']) ?></td>
        <td><?= htmlspecialchars($r['created_at']) ?></td>
        <td>
            <a href="/edit.php?id=<?= e($r['id']) ?>">編集</a>
            <a href="/delete.php?id=<?= e($r['id'])?>" style="color:red">削除</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
