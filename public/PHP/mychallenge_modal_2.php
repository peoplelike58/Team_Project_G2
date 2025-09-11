<?php

    // 若前端在 http://localhost:5173
    header('Access-Control-Allow-Origin: http://localhost:5173'); // ⚠️ 不能用 *
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

    header('Content-Type: application/json; charset=utf-8');

    // 導入資料庫連線的資料檔
    include 'conn.php'; 

    //---------------------------------------------------


    // 取得山峰名稱
    $mountain_name = $_GET['name'];
    
    if (empty($mountain_name)) {
        echo json_encode(['error' => '請提供山峰名稱']);
        exit();
    }

    //建立SQL語法
    $sql = "SELECT MOUNTAIN_ID, MOUNTAIN_NAME 
            FROM MOUNTAIN 
            WHERE MOUNTAIN_NAME 
            LIKE ?";
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