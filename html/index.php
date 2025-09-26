<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'db';
$db   = 'testdb';
$user = 'root';
$pass = 'rootpass';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // 追加処理
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add' && !empty($_POST['name'])) {
        $stmt = $pdo->prepare("INSERT INTO users (name) VALUES (:name)");
        $stmt->execute(['name' => $_POST['name']]);
        $message = "ユーザー「" . htmlspecialchars($_POST['name']) . "」を追加しました！";
    }

    // 編集処理
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit' && !empty($_POST['name'])) {
        $stmt = $pdo->prepare("UPDATE users SET name=:name WHERE id=:id");
        $stmt->execute(['name' => $_POST['name'], 'id' => $_POST['id']]);
        $message = "ユーザーID「" . $_POST['id'] . "」を更新しました！";
    }

    // 削除処理
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id=:id");
        $stmt->execute(['id' => $_POST['id']]);
        $message = "ユーザーID「" . $_POST['id'] . "」を削除しました！";
    }

    // 全データ取得
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll();

} catch (PDOException $e) {
    die("DB接続エラー: " . $e->getMessage());
}
?>

// ここからhtml！
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ユーザー管理</title>
    <style>
        form.inline { display: inline; }
    </style>
</head>

<body>
    <h1>ユーザー管理</h1>

    <?php if (!empty($message)): ?>
        <p style="color:green;"><?= $message ?></p>
    <?php endif; ?>

    <!-- 追加フォーム -->
    <h2>ユーザー追加</h2>
    <form method="post" action="">
        <input type="hidden" name="action" value="add">
        名前: <input type="text" name="name" required>
        <button type="submit">追加</button>
    </form>

    <!-- ユーザー一覧 -->
    <h2>ユーザー一覧</h2>
    <?php if (!empty($users)): ?>
        <ul>
        <?php foreach ($users as $user): ?>
            <li>
                ID: <?= htmlspecialchars($user['id']) ?> /
                名前: <?= htmlspecialchars($user['name']) ?>

                <!-- 編集フォーム -->
                <form method="post" action="" class="inline">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    <button type="submit">更新</button>
                </form>

                <!-- 削除フォーム -->
                <form method="post" action="" class="inline" onsubmit="return confirm('本当に削除しますか？');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                    <button type="submit">削除</button>
                </form>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>ユーザーは登録されていません</p>
    <?php endif; ?>
</body>
</html>
