<?php

include __DIR__ . '/conn.php';
header("Access-Control-Allow-Methods: POST, OPTIONS");// 允許的請求方法
header("Access-Control-Allow-Headers: Content-Type");// 允許的自訂標頭


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

//後台傳來JSON 轉成php的物件或陣列
$updatenews = json_decode(file_get_contents("php://input"), true);

//COALESCE() Function (回傳第一個非空值的值，如果是NULL，就用原本的值)
$sql= "UPDATE NEWS 
        SET TYPE=COALESCE(:type , TYPE),
            TITLE=COALESCE(:title, TITLE),
            STATUS=COALESCE(:status, STATUS),
            UPDATED_AT = NOW()
        WHERE NEWS_ID = :id";

    
$pstmt = $pdo->prepare($sql);
//如果不是空字串，就傳值，如果空字串，就轉成null
$pstmt->bindValue(":type", $updatenews['TYPE'] !=='' ? $updatenews['TYPE'] : null);
$pstmt->bindValue(":title", $updatenews['TITLE']!=='' ? $updatenews['TITLE'] : null);
$pstmt->bindValue(":status", $updatenews['STATUS']!=='' ? $updatenews['STATUS'] : null);
$pstmt->bindValue(":id", $updatenews['NEWS_ID']);
$ok = $pstmt->execute();



if ($ok && $pstmt->rowCount() > 0) {
    echo json_encode(['success' => true, 'message' => '修改成功']);
} else {
    echo json_encode(['success' => false, 'message' => '修改失敗']);
}
