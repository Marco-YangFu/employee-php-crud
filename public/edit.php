<?php
require __DIR__ . '/../src/bootstrap.php';
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    exit('invalid id');
}

// 更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf($_POST['csrf_token'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $dept = trim($_POST['dept'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $err = [];
    if ($name === '' || mb_strlen($name) > 50) {
        $err[] = '名前は1～50文字で入力してください。';
    }
    if ($dept !== '' && mb_strlen($dept) > 50) {
        $err[] = '部署は50文字以内で入力してください。';
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<h1 class="display-6 ms-3">編集ページ (ID: <?= e((string)$id)?>)</h1>

<form method="post" class="mb-3 ms-3" style="max-width: 480px;">
    <div class="mb-3 ">
        <label class="form-label">
        名前:
        </label>
        <input name="name" value="<?= e($user['name']) ?>" class="form-control" required>        
    </div>
    <div class="mb-3">
        <label class="form-label">
        部署: 
        </label>
        <input name="dept" value="<?= e($user['dept']) ?>" class="form-control">

    </div>
    <div class="mb-3">
        <label class="form-label">
        Email:  
        </label>
        <input name="email" type="email" value="<?= e($user['email']) ?>" class="form-control" required>
    </div>
    <?= csrf_field() ?>
    <button type="submit" class="btn btn-primary">更新</button>
     
</form>



