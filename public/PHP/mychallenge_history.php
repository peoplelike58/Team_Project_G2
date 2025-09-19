<?php
    // 導入資料庫連線的資料檔
    include 'conn.php'; 
    
    session_start();

    //---------------------------------------------------
    // 檢查是否已登入
    if (!isset($_SESSION['member']['id']) || empty($_SESSION['member']['id'])) {
        $response = [
            'success' => true,
            // 'session_data' => $_SESSION,
            'data' => []
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $MEMBER_ID = $_SESSION['member']['id'];

    //建立SQL語法
    $sql = "SELECT F.ID as id,
                   F.CONTENT as content,
                   M.MOUNTAIN_NAME as name,
                   F.UPLOAD_AT as date,
                   F.HEIGHT as height,
                   F.DISTANCE as kilo,
                   F.DURATION as time,
                   F.IS_CLIMBED as isClimbed
            FROM FOOT AS F
            JOIN MOUNTAIN AS M
            ON F.MOUNTAIN_ID = M.MOUNTAIN_ID
            WHERE F.MEMBER_ID = ?
            ORDER BY F.UPLOAD_AT DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$MEMBER_ID]);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = [
        'success' => true,
        'isLoggedIn' => true,
        'data' => $rows,
    ];

    echo json_encode( $response, JSON_UNESCAPED_UNICODE);

?>