<?php

include __DIR__ . '/conn.php';
header("Access-Control-Allow-Methods: POST, OPTIONS"); // 允許的請求方法
header("Access-Control-Allow-Headers: Content-Type"); // 允許的自訂標頭


//後台傳來JSON 轉成php的物件或陣列
$delmessage = json_decode(file_get_contents("php://input"), true);

//找到id 並轉成數值，如果沒有id就給0
$id = isset($delmessage['id']) ? intval($delmessage['id']) : 0;


if($id<=0){
    echo json_encode(['success' => false, 'message'=> '刪除失敗']);
    exit;
}
$sql= 'DELETE FROM `MESSAGE` WHERE MESSAGE_ID = :id';
$pstmt = $pdo->prepare($sql);
$pstmt->bindValue(":id", $id);
$pstmt->execute();

if($pstmt->rowCount() > 0){
    echo json_encode(['success' => true, 'message'=> '刪除成功']);
}else{
    echo json_encode(['success' => false, 'message'=> '刪除失敗']);
}