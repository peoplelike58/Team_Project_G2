<?php

include __DIR__ . '/conn.php';
header("Access-Control-Allow-Methods: POST, OPTIONS");// 允許的請求方法
header("Access-Control-Allow-Headers: Content-Type");// 允許的自訂標頭


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

//後台傳來JSON 轉成php的物件或陣列
$addevent = json_decode(file_get_contents("php://input"), true);

if (empty($addevent)) {
    echo json_encode(['success' => false, 'message' => '沒有接收到資料']);
    exit;
}


$sql= "INSERT INTO EVENT (EVENT_NAME, EVENT_DATE, EVENT_TIME, START_DATE, START_TIME,END_DATETIME,STATUS,CREATED_AT, CONTENT,MEETING_PLACE,DISTANCE,MOUNTAIN_ID)
        VALUES(:ename, :edate, :etime, :sdate, :stime, :edatetime, :status, NOW(), :content, :mplace, :distance, :mountainid)";
    
$pstmt = $pdo->prepare($sql);
$pstmt->bindValue(":ename", $addevent['EVENT_NAME']);
$pstmt->bindValue(":edate", $addevent['EVENT_DATE']);
$pstmt->bindValue(":etime", $addevent['EVENT_TIME']);
$pstmt->bindValue(":sdate", $addevent['START_DATE']);
$pstmt->bindValue(":stime", $addevent['START_TIME']);
$pstmt->bindValue(":edatetime", $addevent['END_DATETIME']);
$pstmt->bindValue(":status", $addevent['STATUS']);
$pstmt->bindValue(":content", $addevent['CONTENT']);
$pstmt->bindValue(":mplace", $addevent['MEETING_PLACE']);
$pstmt->bindValue(":distance", $addevent['DISTANCE']);
$pstmt->bindValue(":mountainid", $addevent['MOUNTAIN_ID']);
$ok = $pstmt->execute();

if ($ok && $pstmt->rowCount() > 0) {
    echo json_encode(['success' => true, 'message' => '新增成功']);
} else {
    echo json_encode(['success' => false, 'message' => '新增失敗']);
}
