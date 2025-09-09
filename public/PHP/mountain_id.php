<?php

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


    // 取得山峰名稱
    $mountain_name = $_GET['name'];
    
    if (empty($mountain_name)) {
        echo json_encode(['error' => '請提供山峰名稱']);
        exit();
    }

    //建立SQL語法
    $sql = "SELECT MOUNTAIN_ID, MOUNTAIN_NAME FROM MOUNTAIN WHERE MOUNTAIN_NAME LIKE ?";
    $statement = $pdo->prepare($sql);
    $statement->execute(['%' . $mountain_name . '%']);
    
    $result = $statement->fetch();
    
    if ($result) {
            echo json_encode([
                'success' => true,
                'mountain_id' => $result['MOUNTAIN_ID'],
                'mountain_name' => $result['MOUNTAIN_NAME']
            ]);
        }

?>