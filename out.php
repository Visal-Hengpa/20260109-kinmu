<?php
$dsn = 'mysql:host=localhost;dbname=kintaidb;charset=utf8';
$user = 'kintaiuser';
$pass = 'kintaipass123';

try {
    $pdo = new PDO($dsn, $user, $pass);
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['jugyoin_id'])) {
        // 当該IDの未退勤レコードの最新1件を更新
        $stmt = $pdo->prepare("UPDATE kiroku SET end_work = NOW() WHERE jugyoin_id = ? AND end_work IS NULL ORDER BY id DESC LIMIT 1");
        $stmt->execute([$_POST['jugyoin_id']]);
        $msg = "退勤を記録しました。";
    }
} catch (PDOException $e) {
    die('接続エラー: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>退勤入力</title>
    <style>
        /* 担当2：ここを青色っぽくデザインしてください */
        body { background-color: #f0f8ff; }
    </style>
</head>
<body>
    <h1>退勤入力画面</h1>
    <?php if(isset($msg)) echo "<p>$msg</p>"; ?>
    <form method="POST">
        <label>従業員ID: <input type="number" name="jugyoin_id" required></label>
        <button type="submit">退勤</button>
    </form>
    <nav>
        <a href="index.php">出勤入力へ</a> | <a href="list.php">全記録一覧へ</a>
    </nav>
</body>
</html>