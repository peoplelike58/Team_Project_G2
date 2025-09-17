<?php   /* 移除收藏 php */

include 'conn.php';

$data=json_decode(file_get_contents("php://input"), true);//接收前端來的東西，做json檔的解碼，file_get_contents("php://input") → 抓到整個 JSON 字串，json_decode(..., true) → 把 JSON 轉成 關聯陣列 (associative array)


session_start();

if (!isset($_SESSION['member']) || !isset($_SESSION['member']['id'])) {
  echo json_encode(['success' => false, 'message' => '尚未登入']); exit;
}

$memberId = $_SESSION['member']['id']; // 從 Session 拿會員ID

//檢查前端是否有傳送要刪除的購物車 ID
if (!isset($data['cartId']) || empty($data['cartId'])) {
    echo json_encode([
        'success' => false, 
        'message' => '缺少購物車項目 ID'
    ],JSON_UNESCAPED_UNICODE);
    exit;
}

//取得要刪除的購物車項目 ID
$cartId = $data['cartId'];

//建立SQL
$sql = "DELETE FROM CART WHERE CART_ID = ? AND MEMBER_ID = ?";

  $pstmt = $pdo->prepare($sql);        // prepare() → 預先編譯 SQL 語句，提高安全性和效能
  $result = $pstmt->execute([$cartId,$memberId]);  // execute([參數陣列]) → 執行預備語句，自動替換佔位符

//檢查是否有刪除到資料
$affectedRows = $pstmt->rowCount();     // rowCount() → 取得受影響的資料筆數

if($affectedRows > 0){
    echo json_encode([
            'success' => true,
            'message' => '商品已成功從購物車移除',
            'affectedRows' => $affectedRows    ],
            JSON_UNESCAPED_UNICODE);
}else{
        echo json_encode([
        'success' => false,
        'message' => '找不到該購物車項目或您沒有權限刪除']
        ,JSON_UNESCAPED_UNICODE);
}


?>