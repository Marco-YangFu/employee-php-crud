<?php
require __DIR__ . '/../src/bootstrap.php';
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { exit('invalid id'); }

$stmt = $pdo->prepare('SELECT name, email FROM users WHERE id = ?');
$stmt->execute([$id]);
$target = $stmt->fetch();
if (!$target) {
    exit('User not found');
}

// 確認画面
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf($_POST['csrf_token'] ?? '');
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
    }
    redirect('/'); // 削除してもキャンセルしても一覧に戻す
}

?>
<!DOCTYPE html>
<meta charset="UTF-8">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<div class="container py-4" style="max-width: 620px;">
    <h1 class="display-6">削除確認（ID: <?= e((string)$id) ?>）</h1>

    <div class="alert alert-warning">
        <div><p class="fs-5">このユーザーを本当に削除しますか？ この操作は取り消せません。</p></div>
        <div class="mt-2">
            <strong>名前：</strong><?= e($target['name']) ?>
            <strong>Email：</strong><?= e($target['email']) ?>
        </div>
    </div>

    <form method="post" class="d-flex gap-2">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-danger" name="confirm" value="yes">削除する</button>
        <button type="submit" class="btn btn-secondary" name="confirm" value="no">キャンセル</button>
    </form>
</div>
