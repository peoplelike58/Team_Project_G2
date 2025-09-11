<?php
    session_start();
    $MEMBER_ID = $_SESSION["memberID"] = "1";

    // 若前端在 http://localhost:5173
    header('Access-Control-Allow-Origin: http://localhost:5173'); // ⚠️ 不能用 *
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

    header('Content-Type: application/json; charset=utf-8');

    // 導入資料庫連線的資料檔
    include 'conn.php'; 

    //---------------------------------------------------

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

    echo json_encode($rows, JSON_UNESCAPED_UNICODE);

?>