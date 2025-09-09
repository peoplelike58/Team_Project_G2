<?php
    session_start();

    // 若前端在 http://localhost:5173
    header('Access-Control-Allow-Origin: http://localhost:5173'); // ⚠️ 不能用 *
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

    header('Content-Type: application/json; charset=utf-8');

    //MySQL相關資訊
    $db_host = "127.0.0.1";
    $db_user = "root";
    $db_pass = "password";
    $db_select = "hou_Shan";

    //建立資料庫連線物件
    $dsn = "mysql:host=".$db_host.";dbname=".$db_select.";charset=utf8";
    $pdo = new PDO($dsn, $db_user, $db_pass);

    //---------------------------------------------------


    // 取得會員ID
    $MEMBER_ID = $_SESSION["memberID"] = "1";

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

    $rows = $stmt->fetchAll();

    echo json_encode($rows, JSON_UNESCAPED_UNICODE);

?>