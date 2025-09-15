<?php

include __DIR__ . '/conn.php';
header("Access-Control-Allow-Methods: POST, OPTIONS");// 允許的請求方法
header("Access-Control-Allow-Headers: Content-Type");// 允許的自訂標頭


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

//後台傳來JSON 轉成php的物件或陣列
$addnews = json_decode(file_get_contents("php://input"), true);

if (empty($addnews)) {
    echo json_encode(['success' => false, 'message' => '沒有接收到資料']);
    exit;
}


$sql= "INSERT INTO NEWS (TYPE, TITLE, STATUS, UPLOAD_AT, UPDATED_AT)
        VALUES(:type, :title, :status, NOW(), NOW())";
    
$pstmt = $pdo->prepare($sql);
$pstmt->bindValue(":type", $addnews['TYPE']);
$pstmt->bindValue(":title", $addnews['TITLE']);
$pstmt->bindValue(":status", $addnews['STATUS']);
$ok = $pstmt->execute();

if ($ok && $pstmt->rowCount() > 0) {
    echo json_encode(['success' => true, 'message' => '新增成功']);
} else {
    echo json_encode(['success' => false, 'message' => '新增失敗']);
}
