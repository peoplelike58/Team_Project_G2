<?php

    // 若前端在 http://localhost:5173
    header('Access-Control-Allow-Origin: http://localhost:5173'); // ⚠️ 不能用 *
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

    // 導入資料庫連線的資料檔
    include 'conn.php'; 

    //---------------------------------------------------

    //建立SQL語法
    $sql = "SELECT MOUNTAIN_NAME, type, LATITUDE, LONGITUDE
            FROM MOUNTAIN
            WHERE type IN ('大百岳', '小百岳')";

    //執行並查詢，會回傳查詢結果的物件，必須使用fetch、fetchAll...等方式取得資料
    $statement = $pdo->query($sql);

    //抓出全部且依照順序封裝成一個二維陣列
    $data = $statement->fetchAll();

    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    //    print_r($data);

?>