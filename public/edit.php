<?php
require __DIR__ . '/../src/bootstrap.php';
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    exit('invalid id');
}

// 更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $dept = trim($_POST['dept'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $err = [];
    if ($name === '' || mb_strlen($name) > 50) {
        $err[] = '名前は1～50文字で入力してください。';
    }
    if ($email === '' || mb_strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err[] = 'メールアドレスの形式が正しくありません。';
    }

    if (!$err) {
        $stmt = $pdo->prepare('UPDATE users SET name = ?, dept = ?, email = ? WHERE id = ?');
        $stmt->execute([$name, $dept, $email, $id]);
        redirect('/'); // 更新後は一覧に戻る
    }
}

// 1件取得
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) {
    exit('User not found');
}

?>
<!DOCTYPE html>
<meta charset="UTF-8">
<h1>編集ページ (ID: <?= e((string)$id)?>)</h1>

<form method="post" style="display: flex;flex-direction:column;gap:8px; max-width:320px">
    <label>名前: <input name="name" value="<?= e($user['name']) ?>" required></label>
    <label>部署: <input name="dept" value="<?= e($user['dept']) ?>"></label>
    <label>Email: <input name="email" type="email" value="<?= e($user['email']) ?>" required></label>
    <button type="submit">更新</button>
</form>



