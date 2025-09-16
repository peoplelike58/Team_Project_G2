<?php

include __DIR__ . '/conn.php';
header("Access-Control-Allow-Methods: POST, OPTIONS");// 允許的請求方法
header("Access-Control-Allow-Headers: Content-Type");// 允許的自訂標頭


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

//後台傳來JSON 轉成php的物件或陣列
$updatcoupon = json_decode(file_get_contents("php://input"), true);

//COALESCE() Function (回傳第一個非空值的值，如果是NULL，就用原本的值)
$sql= "UPDATE COUPON 
        SET STATUS=COALESCE(:status, STATUS),
            COUPON_TITLE=COALESCE(:ctitle, COUPON_TITLE),
            DISCOUNT=COALESCE(:dis, DISCOUNT),
            END_AT=COALESCE(:exp, END_AT)
        WHERE COUPON_ID = :id";

    
$pstmt = $pdo->prepare($sql);
$pstmt->bindValue(":status", $updatcoupon["STATUS"] !=='' ? $updatcoupon['STATUS'] : null);
$pstmt->bindValue(":ctitle", $updatcoupon['COUPON_TITLE'] !=='' ? $updatcoupon['COUPON_TITLE'] : null);
$pstmt->bindValue(":dis", $updatcoupon['DISCOUNT'] !=='' ? $updatcoupon['DISCOUNT'] : null);
$pstmt->bindValue(":exp", $updatcoupon['END_AT']!=='' ? $updatcoupon['END_AT'] : null);
$pstmt->bindValue(":id", $updatcoupon["COUPON_ID"]);
$ok = $pstmt->execute();



if ($ok) {
    echo json_encode(['success' => true, 'message' => '修改成功']);
} else {
    echo json_encode(['success' => false, 'message' => '修改失敗']);
}
