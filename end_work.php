<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jugyoin_id = (int)$_POST['jugyoin_id'];
    $end_work = date('Y-m-d H:i:s');
    
    try {
        $sql = "SELECT id FROM kiroku 
                WHERE jugyoin_id = :jugyoin_id AND end_work IS NULL 
                ORDER BY start_work DESC LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':jugyoin_id' => $jugyoin_id]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($record) {
            $update_sql = "UPDATE kiroku SET end_work = :end_work WHERE id = :id";
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->execute([':end_work' => $end_work, ':id' => $record['id']]);
            $message = "退勤時刻を記録しました";
        } else {
            $message = "未出勤の記録が見つかりません";
        }
        
        echo "<script>alert('$message'); window.location.href='index.php';</script>";
    } catch (PDOException $e) {
        echo "エラーが発生しました: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>退勤記録システム</title>
    <style>
        body {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(33, 150, 243, 0.2);
            border-left: 6px solid #2196F3;
        }
        h1 {
            color: #1565C0;
            text-align: center;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #1976D2;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
        }
        input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #90CAF9;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        input[type="number"]:focus {
            border-color: #2196F3;
            outline: none;
        }
        .btn {
            background: linear-gradient(to right, #2196F3, #1565C0);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(33, 150, 243, 0.4);
        }
        .nav-links {
            text-align: center;
            margin-top: 30px;
        }
        .nav-links a {
            color: #2196F3;
            text-decoration: none;
            margin: 0 15px;
            padding: 8px 16px;
            border-radius: 6px;
            transition: background 0.3s;
        }
        .nav-links a:hover {
            background: #E3F2FD;
        }
        .current-time {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .info-box {
            background: #E3F2FD;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #2196F3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>退勤記録</h1>
        <div class="subtitle">勤務終了時刻を記録します</div>
        
        <div class="info-box">
            <strong>注意:</strong> 最後に出勤した記録に退勤時刻を記録します。
            未出勤の場合は記録されません。
        </div>
        
        <div class="current-time" id="currentTime">
            現在時刻: <span id="timeDisplay"></span>
        </div>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="jugyoin_id">従業員ID</label>
                <input type="number" id="jugyoin_id" name="jugyoin_id" 
                       min="1" max="9999" required 
                       placeholder="例: 1001">
            </div>
            
            <button type="submit" class="btn">
                <span style="font-size: 18px;">🚪</span> 退勤を記録
            </button>
        </form>
        
        <div class="nav-links">
            <a href="index.php">記録一覧</a>
            <a href="start_work.php">出勤記録</a>
        </div>
    </div>

    <script>
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleString('ja-JP', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('timeDisplay').textContent = timeString;
        }
        
        updateTime();
        setInterval(updateTime, 1000);
    </script>
</body>
</html>