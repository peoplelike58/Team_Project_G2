<?php

include __DIR__ . '/conn.php';
header("Access-Control-Allow-Methods: POST, OPTIONS");// 允許的請求方法
header("Access-Control-Allow-Headers: Content-Type");// 允許的自訂標頭


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

//後台傳來JSON 轉成php的物件或陣列
$updatorder = json_decode(file_get_contents("php://input"), true);

//COALESCE() Function (回傳第一個非空值的值，如果是NULL，就用原本的值)
$sql= "UPDATE ORDER_LIST 
        SET ORDER_STATUS=COALESCE(:status, ORDER_STATUS),
            PAY_STATUS=COALESCE(:pstatus, PAY_STATUS),
            DEL_STATUS=COALESCE(:dstatus, DEL_STATUS),
            SUBTTL=COALESCE(:subttl, SUBTTL),
            DISCOUNT=COALESCE(:discount, DISCOUNT),
            SHIPPINGFEE=COALESCE(:shippingfee, SHIPPINGFEE),
            TTL_AMT=COALESCE(:ttlamt, TTL_AMT)
        WHERE ORDER_ID = :id";

    
$pstmt = $pdo->prepare($sql);
$pstmt->bindValue(":status", $updatorder["ORDER_STATUS"] !=='' ? $updatorder['ORDER_STATUS'] : null);
$pstmt->bindValue(":pstatus", $updatorder['PAY_STATUS'] !=='' ? $updatorder['PAY_STATUS'] : null);
$pstmt->bindValue(":dstatus", $updatorder['DEL_STATUS'] !=='' ? $updatorder['DEL_STATUS'] : null);
$pstmt->bindValue(":subttl", $updatorder['SUBTTL']!=='' ? $updatorder['SUBTTL'] : null);
$pstmt->bindValue(":discount", $updatorder['DISCOUNT']!=='' ? $updatorder['DISCOUNT'] : null);
$pstmt->bindValue(":shippingfee", $updatorder['SHIPPINGFEE']!=='' ? $updatorder['SHIPPINGFEE'] : null);
$pstmt->bindValue(":ttlamt", $updatorder['TTL_AMT']!=='' ? $updatorder['TTL_AMT'] : null);
$pstmt->bindValue(":id", $updatorder["ORDER_ID"]);
$ok = $pstmt->execute();


if ($ok) {
    echo json_encode(['success' => true, 'message' => '修改成功']);
} else {
    echo json_encode(['success' => false, 'message' => '修改失敗']);
}
