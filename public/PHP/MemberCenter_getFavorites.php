<?php   /* 獲取收藏資料 php */

include 'conn.php';

try {
    $sql = "insert into CART (MEMBER_ID,PRODUCT_ID,SIZE,COLOR,QUANTITY)
values (:memberId ,:productID ,:size ,:color,:quantity)
ON DUPLICATE KEY UPDATE 
  QUANTITY = :quantity";

    $pstmt = $pdo->prepare($sql);
    $pstmt->execute();
    $products = $pstmt->fetchAll(PDO::FETCH_ASSOC);

    // 處理資料格式，將顏色和尺寸轉為陣列
    $formattedProducts = array_map(function($product) {
        return [
            'id' => $product['PRODUCT_ID'],
            'name' => $product['PRODUCT_NAME'],
            'price' => $product['PRICE'],
            'image' => $product['IMAGE'],
            'gender' => $product['GENDER'],
            'description' => $product['DESCRIPTION'],
            'category' => $product['PRODUCT_TYPE'], // 對應前端的 category
            'status' => $product['PRODUCT_STATUS'],
            'color' => $product['COLOR'] ? explode(',', $product['COLOR']) : [],
            'size' => $product['SIZE'] ? explode(',', $product['SIZE']) : []
        ];
    }, $products);

    $respBody = [
        'success' => true,
        'products' => $formattedProducts,
        'total' => count($formattedProducts)
    ];
} catch (Exception $e) {
    $respBody = [
        'success' => false,
        'message' => '資料查詢失敗: ' . $e->getMessage(),
        'products' => []
    ];
}

echo json_encode($respBody, JSON_UNESCAPED_UNICODE);

?>
