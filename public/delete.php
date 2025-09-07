<?php
require __DIR__ . '/../src/bootstrap.php';
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { exit('invalid id'); }

// 確認画面
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
    }
    redirect('/'); // 削除してもキャンセルしても一覧に戻す
}

?>
<!DOCTYPE html>
<meta charset="UTF-8">
<h1>削除確認（ID: <?= e((string)$id) ?>）</h1>
<p>このユーザーを本当に削除しますか？</p>
<form method="post" style="margin-top: 12px;">
    <button type="submit" name="confirm" value="yes" style="color: red;">削除する</button>
    <button type="submit" name="confirm" value="no">キャンセル</button>
</form>
