<?php
    session_start();

    // 導入資料庫連線的資料檔
    include 'conn.php'; 

    //---------------------------------------------------

    if (!isset($_SESSION['member']['id']) || empty($_SESSION['member']['id'])) {
        $response = [
            'success' => true,
        ];

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $MEMBER_ID = $_SESSION['member']['id'];


    $input = json_decode(file_get_contents('php://input'), true);
    $mountain_id = $input['mountain_id'];
    $height = $input['height'];
    $distance = $input['distance'];
    $duration = $input['duration'];
    $content = $input['content'] ?? '';  // 可為空字串

    //建立SQL語法
    $sql = "INSERT INTO FOOT(MEMBER_ID, MOUNTAIN_ID, HEIGHT, DISTANCE, DURATION, CONTENT, UPLOAD_AT) 
            VALUES ( ?, ?, ?, ?, ?, ?, NOW())";

    $statement = $pdo->prepare($sql);

    $result = $statement->execute([$MEMBER_ID, $mountain_id, $height, $distance, $duration, $content]);

    if ($result) {
            echo json_encode(['success' => true, 'message' => '紀錄儲存成功']);
    } else {
            echo json_encode(['error' => '儲存失敗']);
    }

?>