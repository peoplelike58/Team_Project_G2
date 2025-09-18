<?php   /* 獲取收藏資料 php */

include 'conn.php';

$data=json_decode(file_get_contents("php://input"), true);//接收前端來的東西，做json檔的解碼，file_get_contents("php://input") → 抓到整個 JSON 字串，json_decode(..., true) → 把 JSON 轉成 關聯陣列 (associative array)

try {
    $sql = "SELECT 
        p.PRODUCT_ID,
        p.PRODUCT_NAME,
        p.PRICE,
        p.IMAGE,
        p.PRODUCT_TYPE,
        p.GENDER,
        f.CREATED_AT
        FROM FAVORITES f
        INNER JOIN PRODUCT p ON f.PRODUCT_ID = p.PRODUCT_ID
        WHERE f.MEMBER_ID = :member_id
        ORDER BY f.CREATED_AT DESC";

    $pstmt = $pdo->prepare($sql);
    $pstmt->bindParam(':member_id', $data['member_id'], PDO::PARAM_INT);
    $pstmt->execute();
    $favorites = $pstmt->fetchAll(PDO::FETCH_ASSOC);


    $respBody = [
        'success' => true,
        'favorites' =>  $favorites,
        'count' => count($favorites)
    ];
} catch (Exception $e) {
    $respBody = [
        'success' => false,
        'message' => '已收藏商品資料查詢失敗: ' . $e->getMessage(),
        'products' => []
    ];
}

echo json_encode($respBody, JSON_UNESCAPED_UNICODE);

?>
