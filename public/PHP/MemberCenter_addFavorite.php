<?php     /* 加入收藏 php */



include 'conn.php';


try{
  $data=json_decode(file_get_contents("php://input"), true);//接收前端來的東西，做json檔的解碼，file_get_contents("php://input") → 抓到整個 JSON 字串，json_decode(..., true) → 把 JSON 轉成 關聯陣列 (associative array)

  session_start();
  if (!isset($_SESSION['member']) || !isset($_SESSION['member']['id'])) {
    echo json_encode(['success' => false, 'message' => '尚未登入']); exit;
  }

  $memberId = $_SESSION['member']['id']; // 從 Session 拿會員ID

     
  // 檢查商品是否存在
  $productCheck = $pdo->prepare("SELECT PRODUCT_ID,PRODUCT_NAME, PRICE, IMAGE FROM PRODUCT WHERE PRODUCT_ID = :product_id");
  $productCheck->bindParam(':product_id', $data['product_id'], PDO::PARAM_INT);
  $productCheck->execute();
  $product = $productCheck->fetch(PDO::FETCH_ASSOC);
  
  if (!$product) {
      echo json_encode([
          'success' => false,
          'message' => '商品不存在'
      ],JSON_UNESCAPED_UNICODE);
      exit();
  }
  
  // 檢查是否已經收藏
  $favoriteCheck = $pdo->prepare("
      SELECT id FROM FAVORITES 
      WHERE MEMBER_ID = :member_id AND PRODUCT_ID = :product_id
  ");
  $favoriteCheck->bindParam(':member_id', $memberId, PDO::PARAM_INT);
  $favoriteCheck->bindParam(':product_id', $data['product_id'], PDO::PARAM_INT);
  $favoriteCheck->execute();
  
  if ($favoriteCheck->rowCount() > 0) {
      echo json_encode([
          'success' => true,
          'message' => '商品已在收藏清單中'
      ],JSON_UNESCAPED_UNICODE);
      exit();
  }

  // 新增收藏記錄
  $sql = "INSERT INTO FAVORITES (MEMBER_ID,PRODUCT_ID,CREATED_AT,UPDATED_AI) VALUES (:memberId ,:productID ,NOW() ,NOW())";

  $pstmt = $pdo->prepare($sql);
  $pstmt->bindValue( ":memberId", $memberId);
  $pstmt->bindValue(":productID", $data["product_id"]);

  if ( $pstmt->execute()) {
    // 新增成功，取得新增記錄的ID
    $favorite_id = $pdo->lastInsertId();           // 取得剛剛新增記錄的 AUTO_INCREMENT ID
    
    // 回傳成功結果，包含完整商品資訊供前端使用
    echo json_encode([
      'success' => true,
      'message' => '已成功加入收藏',
      'favorite_id' => $favorite_id,
      'product' => [
        'id' => $product['PRODUCT_ID'],
        'name' => $product['PRODUCT_NAME'],
        'price' => $product['PRICE'],
        'image' => $product['IMAGE']] // 回傳商品資訊給前端，避免前端需要再次查詢
    ],JSON_UNESCAPED_UNICODE);
  }
}catch(Exception $exception){
  echo json_encode([
    'success' => false,
    'message' => '資料庫錯誤，請稍後再試'. $exception->getMessage()
  ], JSON_UNESCAPED_UNICODE);
}
?>
