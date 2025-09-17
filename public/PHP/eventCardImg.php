<?php

include 'conn.php';

try {
    header('Content-Type: application/json; charset=utf-8');
    
    $sql = "SELECT IMAGE,IMAGE_TYPE,MOUNTAIN_ID
            FROM MOUNTAIN_IMAGE WHERE IMAGE_TYPE = 'main'
            AND MOUNTAIN_ID IS NOT NULL
            ORDER BY MOUNTAIN_ID ASC";

    $statement = $pdo->query($sql);
    $images = $statement->fetchAll(PDO::FETCH_ASSOC);

    $imageMap = [];

    foreach ($images as $image){
        $imageMap[$image['MOUNTAIN_ID']] = [
            'imageName' => $image['IMAGE'], //圖片檔名
            'imageType' => $image['IMAGE_TYPE'], //圖片類型
            'mountainId' => $image['MOUNTAIN_ID'] //山岳編號
        ];
    }

    // 回傳成功結果
    // JSON_UNESCAPED_UNICODE - 確保中文字不會被轉換成 Unicode
    echo json_encode([
        'success' => true,                     // 成功標記
        'data' => $imageMap,                    // 圖片資料（以 MOUNTAIN_ID 為 key）
        'count' => count($imageMap),           // 總筆數
        'message' => '圖片資料取得成功'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    // 資料庫錯誤處理
    // PDOException 專門處理資料庫相關的錯誤
    http_response_code(500);  // 設定 HTTP 狀態碼為 500（伺服器內部錯誤）
    
    echo json_encode([
        'success' => false,
        'message' => '資料庫查詢失敗',
        'error' => $e->getMessage(),  // 錯誤訊息（開發時使用，正式環境建議隱藏）
        'data' => []
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // 其他錯誤處理
    // Exception 處理所有其他類型的錯誤
    http_response_code(500);
    
    echo json_encode([
        'success' => false,
        'message' => '伺服器錯誤',
        'error' => $e->getMessage(),
        'data' => []
    ], JSON_UNESCAPED_UNICODE);
}


?>