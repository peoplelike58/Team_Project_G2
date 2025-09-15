<?php
    session_start();

    // 導入資料庫連線的資料檔
    include 'conn.php'; 

    //---------------------------------------------------
    // 檢查是否已登入
    if (!isset($_SESSION["memberID"]) || empty($_SESSION["memberID"])) {
        $response = [
            'success' => false,
            'isLoggedIn' => false,
            'message' => '請先登入',
            'data' => []
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }


    $MEMBER_ID = $_SESSION["memberID"];

    //建立SQL語法
    $sql = "SELECT M.MOUNTAIN_NAME as name,
                   F.UPLOAD_AT as date,
                   F.HEIGHT as height,
                   F.DISTANCE as kilo,
                   F.DURATION as time
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
        'member_id' => $MEMBER_ID,
        'data' => $rows,
    ];

    echo json_encode($rows, JSON_UNESCAPED_UNICODE);

?>