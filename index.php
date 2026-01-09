<?php
$dsn = 'mysql:host=localhost;dbname=kintaidb;charset=utf8';
$user = 'kintaiuser';
$pass = 'kintaipass123';

try {
    $pdo = new PDO($dsn, $user, $pass);
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['jugyoin_id'])) {
        $stmt = $pdo->prepare("INSERT INTO kiroku (jugyoin_id, start_work) VALUES (?, NOW())");
        $stmt->execute([$_POST['jugyoin_id']]);
        $msg = "出勤を記録しました。";
    }
} catch (PDOException $e) {
    die('接続エラー: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>出勤入力</title>
    <style>
        /* 担当2：ここを緑色っぽくデザインしてください */
        body { background-color: #f0fff0; }
    </style>
</head>
<body>
    <h1>出勤入力画面</h1>
    <?php if(isset($msg)) echo "<p>$msg</p>"; ?>
    <form method="POST">
        <label>従業員ID: <input type="number" name="jugyoin_id" required></label>
        <button type="submit">出勤</button>
    </form>
    <nav>
        <a href="out.php">退勤入力へ</a> | <a href="list.php">全記録一覧へ</a>
    </nav>
</body>
</html>