<?php
$dsn = 'mysql:host=localhost;dbname=kintaidb;charset=utf8';
$user = 'kintaiuser';
$pass = 'kintaipass123';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $stmt = $pdo->query("SELECT * FROM kiroku ORDER BY id DESC");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('接続エラー: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>全記録一覧</title>
    <style>
        /* 担当2：ここをオレンジ色っぽくデザインしてください */
        body { background-color: #fffaf0; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>全記録一覧</h1>
    <nav>
        <a href="index.php">出勤入力</a> | <a href="out.php">退勤入力</a>
    </nav>
    <br>
    <table>
        <thead>
            <tr>
                <th>従業員ID</th>
                <th>出勤時刻</th>
                <th>退勤時刻</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($records as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['jugyoin_id']); ?></td>
                <td><?php echo htmlspecialchars($row['start_work']); ?></td>
                <td><?php echo htmlspecialchars($row['end_work']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>