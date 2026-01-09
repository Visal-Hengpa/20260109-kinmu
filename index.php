<?php
require_once 'config.php';

$search = $_GET['search'] ?? '';
$where = '';
$params = [];

if ($search) {
    $where = "WHERE jugyoin_id = :jugyoin_id";
    $params[':jugyoin_id'] = (int)$search;
}

$sql = "SELECT * FROM kiroku $where ORDER BY start_work DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stats_sql = "SELECT 
    COUNT(*) as total_records,
    COUNT(DISTINCT jugyoin_id) as unique_employees,
    SUM(CASE WHEN end_work IS NULL THEN 1 ELSE 0 END) as active_workers
    FROM kiroku";
$stats_stmt = $pdo->query($stats_sql);
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>出退勤記録一覧</title>
    <style>
        body {
            background: linear-gradient(135deg, #fff3e0, #ffcc80);
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(255, 152, 0, 0.2);
            border-left: 6px solid #FF9800;
        }
        h1 {
            color: #E65100;
            text-align: center;
            margin-bottom: 20px;
        }
        .stats-container {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }
        .stat-box {
            background: #FFF3E0;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            flex: 1;
            min-width: 200px;
            border-top: 4px solid #FF9800;
        }
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #EF6C00;
            margin: 10px 0;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        .search-box {
            margin-bottom: 30px;
            text-align: center;
        }
        .search-box input {
            padding: 12px 20px;
            width: 300px;
            border: 2px solid #FFB74D;
            border-radius: 8px;
            font-size: 16px;
            margin-right: 10px;
        }
        .search-box button {
            background: #FF9800;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }
        .search-box button:hover {
            background: #F57C00;
        }
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #FF9800;
            color: white;
            padding: 15px;
            text-align: left;
        }
        td {
            padding: 15px;
            border-bottom: 1px solid #FFE0B2;
        }
        tr:hover {
            background: #FFF3E0;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-working {
            background: #FFEB3B;
            color: #F57C00;
        }
        .status-finished {
            background: #C8E6C9;
            color: #2E7D32;
        }
        .action-buttons {
            text-align: center;
            margin: 30px 0;
        }
        .action-buttons a {
            display: inline-block;
            padding: 15px 30px;
            margin: 0 10px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: transform 0.2s;
        }
        .action-buttons a:hover {
            transform: translateY(-3px);
        }
        .btn-start {
            background: #4CAF50;
            color: white;
        }
        .btn-end {
            background: #2196F3;
            color: white;
        }
        .nav-links {
            text-align: center;
            margin-top: 20px;
        }
        .nav-links a {
            color: #FF9800;
            text-decoration: none;
            margin: 0 10px;
            padding: 8px 16px;
            border-radius: 6px;
            transition: background 0.3s;
        }
        .nav-links a:hover {
            background: #FFF3E0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>出退勤記録一覧</h1>
        
        <div class="stats-container">
            <div class="stat-box">
                <div class="stat-number"><?= $stats['total_records'] ?></div>
                <div class="stat-label">総記録数</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= $stats['unique_employees'] ?></div>
                <div class="stat-label">登録従業員数</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= $stats['active_workers'] ?></div>
                <div class="stat-label">勤務中</div>
            </div>
        </div>
        
        <div class="search-box">
            <form method="GET" action="">
                <input type="number" name="search" 
                       value="<?= htmlspecialchars($search) ?>" 
                       placeholder="従業員IDで検索">
                <button type="submit">検索</button>
                <?php if ($search): ?>
                    <a href="index.php" style="color: #FF9800; margin-left: 10px;">全件表示</a>
                <?php endif; ?>
            </form>
        </div>
        
        <div class="action-buttons">
            <a href="start_work.php" class="btn-start">出勤記録</a>
            <a href="end_work.php" class="btn-end">退勤記録</a>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>記録ID</th>
                        <th>従業員ID</th>
                        <th>出勤時刻</th>
                        <th>退勤時刻</th>
                        <th>勤務時間</th>
                        <th>ステータス</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?= htmlspecialchars($record['id']) ?></td>
                        <td><?= htmlspecialchars($record['jugyoin_id']) ?></td>
                        <td><?= htmlspecialchars($record['start_work']) ?></td>
                        <td>
                            <?php if ($record['end_work']): ?>
                                <?= htmlspecialchars($record['end_work']) ?>
                            <?php else: ?>
                                <span style="color: #999; font-style: italic;">未退勤</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($record['end_work']): 
                                $start = new DateTime($record['start_work']);
                                $end = new DateTime($record['end_work']);
                                $interval = $start->diff($end);
                                echo $interval->format('%h時間%i分');
                            endif; ?>
                        </td>
                        <td>
                            <?php if ($record['end_work']): ?>
                                <span class="status-badge status-finished">退勤済</span>
                            <?php else: ?>
                                <span class="status-badge status-working">勤務中</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($records)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px;">
                            <div style="color: #999; font-size: 18px;">
                                記録がありません。最初の出勤記録を作成してください。
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="nav-links">
            <?php if (!empty($records)): ?>
                <div style="margin-top: 20px; color: #666; text-align: center;">
                    表示件数: <?= count($records) ?>件
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>