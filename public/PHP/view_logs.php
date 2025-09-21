<?php
$password = "debug123"; // 改成你自己的密碼
$debugFile = __DIR__ . '/email_debug.log';

// 檢查密碼
if (!isset($_GET['pass']) || $_GET['pass'] !== $password) {
    die('需要密碼才能查看 Log');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Email 除錯 Log</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: monospace; margin: 20px; background: #1e1e1e; color: #fff; }
        .warning { 
            background: #ff4444; 
            color: white; 
            padding: 10px; 
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .log-content { 
            background: #2d2d2d; 
            padding: 20px; 
            border: 1px solid #555;
            white-space: pre-wrap;
            max-height: 70vh;
            overflow-y: auto;
            border-radius: 5px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            margin: 5px;
            cursor: pointer;
            border-radius: 3px;
        }
        .clear-btn { background: #dc3545; color: white; }
        .refresh-btn { background: #007bff; color: white; }
    </style>
</head>
<body>
    <div class="warning">
        ⚠️ 警告：這是除錯頁面，測試完請立即刪除！
    </div>
    
    <h1>Email 除錯 Log</h1>
    
    <button class="btn refresh-btn" onclick="window.location.reload()">重新整理</button>
    
    <?php if (isset($_POST['clear'])): ?>
        <?php file_put_contents($debugFile, ''); ?>
        <p style="color: #4CAF50;">Log 已清空</p>
    <?php endif; ?>
    
    <form method="post" style="display: inline;">
        <button type="submit" name="clear" class="btn clear-btn" 
                onclick="return confirm('確定要清空 log 嗎？')">清空 Log</button>
    </form>
    
    <div class="log-content">
        <?php 
        if (file_exists($debugFile)) {
            $content = file_get_contents($debugFile);
            if (empty($content)) {
                echo "Log 檔案是空的";
            } else {
                echo htmlspecialchars($content);
            }
        } else {
            echo "Log 檔案不存在或尚未產生";
        }
        ?>
    </div>
    
    <p style="color: #888; font-size: 12px;">
        訪問方式：view_logs.php?pass=<?php echo $password; ?>
    </p>
</body>
</html>