<?php   /* 移除收藏 php */

include 'conn.php';

$data=json_decode(file_get_contents("php://input"), true);//接收前端來的東西，做json檔的解碼，file_get_contents("php://input") → 抓到整個 JSON 字串，json_decode(..., true) → 把 JSON 轉成 關聯陣列 (associative array)


session_start();

if (!isset($_SESSION['member']) || !isset($_SESSION['member']['id'])) {
  echo json_encode(['success' => false, 'message' => '尚未登入']); exit;
}

$memberId = $_SESSION['member']['id']; // 從 Session 拿會員ID

// 檢查收藏記錄是否存在
$favoriteCheck = $pdo->prepare("
    SELECT id FROM FAVORITES 
    WHERE MEMBER_ID = :member_id AND PRODUCT_ID = :product_id
");
$favoriteCheck->bindParam(':member_id', $memberId, PDO::PARAM_INT);
$favoriteCheck->bindParam(':product_id', $data['product_id'], PDO::PARAM_INT);
$favoriteCheck->execute();

if ($favoriteCheck->rowCount() === 0) {
    echo json_encode([
        'success' => false,
        'message' => '收藏記錄不存在'
    ]);
    exit();
}

// 刪除收藏記錄
$sql = "DELETE FROM FAVORITES WHERE PRODUCT_ID = :productID AND MEMBER_ID = :memberId";

  $pstmt = $pdo->prepare($sql);        // prepare() → 預先編譯 SQL 語句，提高安全性和效能
  $pstmt->bindValue( ":memberId", $memberId);
  $pstmt->bindValue(":productID", $data["product_id"]);
  $result = $pstmt->execute();  // execute([參數陣列]) → 執行預備語句，自動替換佔位符

//檢查是否有刪除到資料
$affectedRows = $pstmt->rowCount();     // rowCount() → 取得受影響的資料筆數

if($affectedRows > 0){
    echo json_encode([
            'success' => true,
            'message' => '商品已成功從收藏移除',
            'affectedRows' => $affectedRows    ],
            JSON_UNESCAPED_UNICODE);
}else{
        echo json_encode([
        'success' => false,
        'message' => '商品移除失敗或商品不存在']
        ,JSON_UNESCAPED_UNICODE);
}


?>