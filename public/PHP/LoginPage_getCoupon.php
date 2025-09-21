<?php  /* LoginPage_getCoupon.php 抓取可以使用的優惠券-會員中心 */

include 'conn.php';

session_start();
if (!isset($_SESSION['member']) || !isset($_SESSION['member']['id'])) {
echo json_encode(['success' => false, 'message' => '尚未登入'],JSON_UNESCAPED_UNICODE); exit;
}



$sql = "SELECT COUPON_ID, COUPON_TITLE,DISCOUNT,END_AT,STATUS FROM COUPON WHERE STATUS= '上架' AND END_AT > NOW()" ;

$pstmt = $pdo->prepare($sql);
$pstmt->execute();
$couponData = $pstmt->fetchAll(PDO::FETCH_ASSOC);

$renamedCoupons = array_map(function($coupon) {
    return [
        'id' => $coupon['COUPON_ID'],
        'title' => $coupon['COUPON_TITLE'],
        'discount' => $coupon['DISCOUNT'],
        'expiryDate' => $coupon['END_AT'],
        'status' => $coupon['STATUS'],
        'usageRule' => '單筆消費滿額立減'
    ];
}, $couponData);

//  檢查是否有找到資料
if ($couponData) {
    // 成功找到資料，回傳結果
    // 為了配合前端邏輯，將單筆資料包裝成陣列
    echo json_encode([
        'success' => true, 
        'message' => '成功取得優惠券資料',
        'coupons' => $renamedCoupons    // 包成陣列格式，讓前端可以用 [0] 取得
    ], JSON_UNESCAPED_UNICODE);
} else {
    // 沒找到資料
    echo json_encode([
        'success' => false, 
        'message' => '找不到優惠券資料',
    ], JSON_UNESCAPED_UNICODE);
}
?>
