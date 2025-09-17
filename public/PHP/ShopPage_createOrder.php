<?php  /* 結賬新增訂單php */

include 'conn.php';

$input=json_decode(file_get_contents("php://input"), true);



try{
    // // 驗證必要欄位
    // $required_fields = ['MEMBER_ID', 'MEMBER_NAME', 'MEMBER_PHONE', 'MEMBER_ADDRESS', 'PAYMENT', 'DELIVERY', 'ORDER_ID'];
    // foreach ($required_fields as $field) {
    //     if (!isset($input[$field])) {
    //         echo json_encode(['success' => false, 'message' => "缺少必要欄位: $field"],JSON_UNESCAPED_UNICODE);
    //         exit;
    //     }
    // }

    session_start();
    if (!isset($_SESSION['member']) || !isset($_SESSION['member']['id'])) {
    echo json_encode(['success' => false, 'message' => '尚未登入'],JSON_UNESCAPED_UNICODE); exit;
    }

    $memberId = $_SESSION['member']['id']; // 從 Session 拿會員ID


    /* 名稱在執行前要載核對一次 */
    // 開始資料庫交易，全部成功才提交
    $pdo->beginTransaction();

    // 1. 新增主訂單 (ORDER_LIST 表)
    $orderSql = "
        INSERT INTO ORDER_LIST (
            ORDER_CODE, MEMBER_ID, ORDER_AT, PAY_STATUS, DEL_STATUS,
            MEMBER_NAME, MEMBER_PHONE, MEMBER_ADDRESS, PAYMENT, DELIVERY,
            SHIPPINGFEE, SUBTTL, DISCOUNT, TTL_AMT, ORDER_STATUS
        ) VALUES (
            :orderCode, :memberId, NOW(), '未付款', '備貨中',
            :memberName, :memberPhone, :memberAddress, :payment, :delivery,
            :shipfee, :productprice, :discount, :finalprice, '已下單'
        )
    ";

    $orderStmt = $pdo->prepare($orderSql);
    $orderStmt->bindValue(':orderCode', $input['orderCode']);
    $orderStmt->bindValue(':memberId', $memberId);
    $orderStmt->bindValue(':memberName', $input['recipientName']);
    $orderStmt->bindValue(':memberPhone', $input['recipientPhone']);
    $orderStmt->bindValue(':memberAddress', $input['recipientAddress']);
    $orderStmt->bindValue(':shipfee', $input['shippingFee']);
    $orderStmt->bindValue(':productprice', $input['subtotal']);
    $orderStmt->bindValue(':discount', $input['discountAmount']);
    $orderStmt->bindValue(':finalprice', $input['finalTotal']);
    $orderStmt->bindValue(':payment', $input['paymentMethod']);
    $orderStmt->bindValue(':delivery', $input['shippingMethod']);
    $orderStmt->execute();

    /*從資料庫抓取最終訂單編號*/

    //1- 獲取剛插入的訂單ID（自動遞增的主鍵）
    $insertedOrderId = $pdo->lastInsertId();

    // 2-完整的訂單資訊，用SELECT查詢
    $selectSql = "SELECT ORDER_ID, ORDER_CODE FROM ORDER_LIST WHERE ORDER_ID = :orderId";
    $selectStmt = $pdo->prepare($selectSql);
    $selectStmt->bindValue(':orderId', $insertedOrderId);
    $selectStmt->execute();
    $orderData = $selectStmt->fetch(PDO::FETCH_ASSOC);

    // 組合最終訂單編號
    $finalOrderId = $orderData['ORDER_CODE'] . $orderData['ORDER_ID'];


    // 2. 新增訂單商品明細 (ORDER_DETAIL 表) 
    $itemSql = "
        INSERT INTO ORDER_DETAIL (
            ORDER_ID, PRODUCT_ID, QTY, SIZE, COLOR, UNIT_PRICE
        ) VALUES (
            :orderId, :productId, :qty, :size, :color, :unitPrice
        )
    ";

    $itemStmt = $pdo->prepare($itemSql);

    foreach ($input['items'] as $item) {
        $itemStmt->bindValue(':orderId', $insertedOrderId);
        $itemStmt->bindValue(':productId', $item['productId']);
        $itemStmt->bindValue(':qty', $item['quantity']);
        $itemStmt->bindValue(':size', $item['size']);
        $itemStmt->bindValue(':color', $item['color']);
        $itemStmt->bindValue(':unitPrice', $item['price']);
        $itemStmt->execute();
    }

    // 3. 刪除購物車中已結帳的商品
    if (isset($input['cartIds']) && !empty($input['cartIds'])) {
        // 建立 IN 語句的佔位符
        $placeholders = str_repeat('?,', count($input['cartIds']) - 1) . '?';
        $deleteSql = "DELETE FROM CART WHERE CART_ID IN ($placeholders) AND MEMBER_ID = ?";
        $deleteStmt = $pdo->prepare($deleteSql);
        
        // 合併購物車ID和會員ID參數
        $deleteParams = array_merge($input['cartIds'], [$memberId]);
        
        if (!$deleteStmt->execute($deleteParams)) {
            throw new Exception('清理購物車失敗');
        }
        
        $deletedCount = $deleteStmt->rowCount();
        error_log("已刪除 {$deletedCount} 個購物車項目");
    }

    

    // 提交交易， 全部生效
    $pdo->commit();

    // 記錄成功日誌
    error_log("訂單建立成功 - 訂單ID: {$input['orderCode']}, 會員ID: {$memberId}");

    // 回傳成功結果
    echo json_encode([
        'success' => true,
        'message' => '訂單建立成功',
        'orderId' => $insertedOrderId,
        'finalOrderId' => $finalOrderId,
        'orderData' => [
            'orderId' => $orderData['ORDER_CODE'],
            'finalTotal' => $input['finalTotal'],
            'itemCount' => count($input['items']),
            'paymentMethod' => $input['paymentMethod'],
            'shippingMethod' => $input['shippingMethod'],
        ]
    ]);

}catch (Exception $e) {
    // 回滾交易(任何一步失敗就全部回滾)
    if ($pdo->inTransaction()) {
        $pdo->rollBack();              // 全部取消，回到原始狀態
    }
    
    // 記錄錯誤日誌
    error_log("建立訂單失敗: " . $e->getMessage());
    
    // 回傳錯誤結果
    echo json_encode([
        'success' => false,
        'message' => '訂單建立失敗: ' . $e->getMessage()
    ],JSON_UNESCAPED_UNICODE);
} finally {
    // 確保資料庫連線關閉
    $pdo = null;
}


?>