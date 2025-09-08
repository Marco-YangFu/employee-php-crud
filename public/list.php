<?php
require __DIR__ . '/../src/bootstrap.php';

$err = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf($_POST['csrf_token'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $dept = trim($_POST['dept'] ?? '');
    $email = trim($_POST['email'] ?? '');

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

// ---- Sort params
$allowedsCol = ['id','name', 'dept', 'email', 'created_at']; // permission columns
$sort = $_GET['sort'] ?? 'id';
$dir = $_GET['dir'] ?? 'desc';

if (!in_array($sort, $allowedsCol, true)) $sort = 'id';
$dir = strtolower($dir) === 'asc'? 'asc' : 'desc'; // asc 以外は desc

$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    // 部分一致（前後に%）
    $stmt = $pdo->prepare(
        "SELECT id, name, dept, email, created_at
        FROM users
        WHERE name LIKE ?
        ORDER BY $sort $dir
        LIMIT 50"
    );
    $stmt->execute(['%' . $q . '%']);
} else {
    $stmt = $pdo->query(
        "SELECT id, name, dept, email, created_at
        FROM users
        ORDER BY $sort $dir
        LIMIT 50"
    );
}

$rows = $stmt->fetchAll();
?>

<!DOCTYPE html>
<meta charset="UTF-8">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<title>社員一覧</title>
<div class="container py-4">
    <h1>社員一覧</h1>    
    <!-- 追加フォーム -->
    <form method="post" class="row g-2 mb-3">
        <div class="col-auto">
            <input name="name" class="form-control" placeholder="名前" value="<?= e($_POST['name'] ?? '')?>" required>
        </div>
        <div class="col-auto">
            <input name="dept" class="form-control" placeholder="部署" value="<?= e($_POST['dept'] ?? '') ?>">
        </div>
        <div class="col-auto">
            <input name="email" class="form-control" type="email" placeholder="Email" value="<?= e($_POST['email'] ?? '') ?>" required>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">追加</button>
        </div>
        <?= csrf_field() ?>
    </form>
    <?php if(!empty($err)): ?>
        <div style="color: #b00; margin:6px 0">
            <?php foreach ($err as $m): ?>
                <div>・<?= e($m) ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!--検索フォーム -->
            <form method="get" class="row g-2 mb-3">
                <div class="col-auto">
                    <input name="q" placeholder="名前で検索" value="<?= e($_GET['q'] ?? '') ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-secondary">検索</button>
                </div>                    
            </form>
            
            <?php

function nextDir(string $col, string $sort, string $dir): string {
    return ($sort === $col && strtolower($dir) === 'asc') ? 'desc' : 'asc';
}
function sortArrow(string $col, string $sort, string $dir): string {
    if($sort !== $col) return '';
    
    return strtolower($dir) === 'asc' ? '👆' : '👇';
}

?>
<!-- 一覧テーブル -->
<table class="table table-striped table-bordered">
    <tr>
        <th><a href="/?q=<?= $q ?>&sort=id&dir=<?= nextDir('id', $sort, $dir)?>">ID</a></th>
        <th><a href="/?q=<?= $q ?>&sort=name&dir=<?= nextDir('name', $sort, $dir)?>">名前<?= sortArrow('name', $sort, $dir) ?></a></th>
        <th><a href="/?q=<?= $q ?>&sort=dept&dir=<?= nextDir('dept', $sort, $dir)?>">部署 <?= sortArrow('dept', $sort, $dir) ?></a></th>
        <th><a href="/?q=<?= $q ?>&sort=email&dir=<?= nextDir('email', $sort, $dir)?>">Email <?= sortArrow('email', $sort, $dir) ?></a></th>
        <th><a href="/?q=<?= $q ?>&sort=created_at&dir=<?= nextDir('created_at', $sort, $dir)?>">作成日 <?= sortArrow('created_at', $sort, $dir) ?></a></th>
        <th>操作</th>
    </tr>
    <?php foreach ($rows as $r): ?>
        <tr>
            <td><?= e($r['id']) ?></td>
            <td><?= e($r['name']) ?></td>
            <td><?= e($r['dept']) ?></td>
            <td><?= e($r['email']) ?></td>
            <td><?= e($r['created_at']) ?></td>
            <td>
                <a href="/edit.php?id=<?= e($r['id']) ?>">編集</a>
                <a href="/delete.php?id=<?= e($r['id'])?>" style="color:red">削除</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
    