<?php   /* 加入購物車php */

include 'conn.php';

try {
    $sql = "SELECT 
                p.PRODUCT_ID,
                p.PRODUCT_NAME,
                p.PRICE,
                p.IMAGE,
                p.GENDER,
                p.DESCRIPTION,
                p.PRODUCT_TYPE,
                p.PRODUCT_STATUS,
                GROUP_CONCAT(DISTINCT pc.COLOR SEPARATOR ',') as COLOR,
                GROUP_CONCAT(DISTINCT ps.SIZE SEPARATOR ',') as SIZE
            FROM PRODUCT p
            LEFT JOIN PRODUCT_COLOR pc ON p.PRODUCT_ID = pc.PRODUCT_ID
            LEFT JOIN PRODUCT_SIZE ps ON p.PRODUCT_ID = ps.PRODUCT_ID
            GROUP BY p.PRODUCT_ID, p.PRODUCT_NAME, p.PRICE, p.IMAGE, p.GENDER, p.DESCRIPTION, p.PRODUCT_TYPE, p.PRODUCT_STATUS
            ORDER BY p.PRODUCT_ID";

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

