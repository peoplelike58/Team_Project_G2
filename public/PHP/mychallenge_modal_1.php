<?php
    session_start();

    // 導入資料庫連線的資料檔
    include 'conn.php'; 

    //---------------------------------------------------

    $_SESSION["memberID"] = "1";


    $mountain_id = $_POST['mountain_id'];
    $height = $_POST['height'];
    $distance = $_POST['distance'];
    $duration = $_POST['duration'];
    $content = $_POST['content'] ?? '';  // 可為空字串

    //建立SQL語法
    $sql = "INSERT INTO FOOT(MEMBER_ID, MOUNTAIN_ID, HEIGHT, DISTANCE, DURATION, CONTENT, UPLOAD_AT) 
            VALUES ( ?, ?, ?, ?, ?, ?, NOW())";

    $statement = $pdo->prepare($sql);

    $result = $statement->execute([$member_id, $mountain_id, $height, $distance, $duration, $content]);

    if ($result) {
            echo json_encode(['success' => true, 'message' => '紀錄儲存成功']);
    } else {
            echo json_encode(['error' => '儲存失敗']);
    }

?>