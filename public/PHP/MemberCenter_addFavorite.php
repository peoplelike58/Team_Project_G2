<?php     /* 加入收藏 php */



include 'conn.php';

try{
  $data=json_decode(file_get_contents("php://input"), true);//接收前端來的東西，做json檔的解碼，file_get_contents("php://input") → 抓到整個 JSON 字串，json_decode(..., true) → 把 JSON 轉成 關聯陣列 (associative array)

  session_start();
  if (!isset($_SESSION['member']) || !isset($_SESSION['member']['id'])) {
    echo json_encode(['success' => false, 'message' => '尚未登入']); exit;
  }

  $memberId = $_SESSION['member']['id']; // 從 Session 拿會員ID

  $sql = "INSERT INTO FAVORITES (id,PRODUCT_ID,CREATED_AT,UPDATED_AI) VALUES (:memberId ,:productID ,NOW() ,NOW())";

  $pstmt = $pdo->prepare($sql);
  $pstmt->bindValue( ":memberId", $memberId);
  $pstmt->bindValue(":productID", $data["productId"]);
  $pstmt->execute();

  if ($stmt->execute()) {
    // 新增成功，取得新增記錄的ID
    $favorite_id = $pdo->lastInsertId();           // 取得剛剛新增記錄的 AUTO_INCREMENT ID
    
    // 回傳成功結果，包含完整商品資訊供前端使用
    echo json_encode([
        'success' => true,
        'message' => '已成功加入收藏',
        'favorite_id' => $favorite_id,
        'product' => $data["productId"]  // 回傳商品資訊給前端，避免前端需要再次查詢
    ],JSON_UNESCAPED_UNICODE);
  };
}catch{
  echo json_encode([
    'success' => false,
    'message' => '資料庫錯誤，請稍後再試'
  ]);
}
?>
