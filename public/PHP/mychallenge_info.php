<?php
    // 導入資料庫連線的資料檔
    include 'conn.php'; 

    session_start();

    //---------------------------------------------------

    // 檢查是否已登入
    if (!isset($_SESSION['member']['id']) || empty($_SESSION['member']['id'])) {
        $response = [
            'success' => true,
            'heightTotal' => '0.00',
            'kiloTotal' => '0.00',
            'timeTotal' => '0.00'
        ];

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 已登入，取得使用者數據
    $MEMBER_ID = $_SESSION['member']['id'];
    

    $sql = "SELECT COALESCE(SUM(HEIGHT), 0) as heightTotal,
                   COALESCE(SUM(DISTANCE), 0) as kiloTotal,
                   COALESCE(SUM(DURATION), 0) as timeTotal
            FROM FOOT
            WHERE MEMBER_ID = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$MEMBER_ID]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // 格式化數值 (保留2位小數)
    $response = [
        'success' => true,
        // 'isLoggedIn' => true,
        // 'member_id' => $MEMBER_ID,
        'heightTotal' => number_format($result['heightTotal'], 2),
        'kiloTotal' => number_format($result['kiloTotal'], 2),
        'timeTotal' => number_format($result['timeTotal'], 2)
    ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE);



?>