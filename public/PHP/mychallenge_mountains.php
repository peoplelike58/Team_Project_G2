<?php
    session_start();

    // 導入資料庫連線的資料檔
    include 'conn.php'; 

    //---------------------------------------------------
    if (!isset($_SESSION["memberID"])) {
        $_SESSION["memberID"] = 1;  // 測試用的固定用戶ID
    }

    $MEMBER_ID = $_SESSION["memberID"];

    // // 1. 取得所有山峰的基本資料
    $sql = "SELECT MOUNTAIN_ID, MOUNTAIN_NAME, type, LATITUDE, LONGITUDE
            FROM MOUNTAIN
            WHERE type IN ('大百岳', '小百岳')
            ORDER BY MOUNTAIN_NAME";
        
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $allMountains = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    // 2. 取得該用戶已攀登的山峰
    $climbedSql = "SELECT DISTINCT M.MOUNTAIN_ID, M.MOUNTAIN_NAME 
                   FROM MOUNTAIN M 
                   JOIN FOOT F ON M.MOUNTAIN_ID = F.MOUNTAIN_ID 
                   WHERE F.MEMBER_ID = ?";
    
    $climbedStmt = $pdo->prepare($climbedSql);
    $climbedStmt->execute([$MEMBER_ID]);
    $climbed = $climbedStmt->fetchAll(PDO::FETCH_COLUMN);

    // 3. 組合結果
    $result = [
        'mountains' => $allMountains,
        'climbed' => $climbed
    ];
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE);

?>