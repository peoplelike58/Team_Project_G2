<?php

    // 導入資料庫連線的資料檔
    include 'conn.php'; 

try {

    header('Content-Type: application/json; charset=utf-8');
    
    // SQL指令查詢對應資料庫
    $sql = "SELECT EVENT_ID,EVENT_NAME,JOIN_QTY,EVENT_DATE,EVENT_TIME,START_DATE,START_TIME,END_DATETIME,
            STATUS,CONTENT,MEETING_PLACE,DISTANCE,MOUNTAIN_ID 
            FROM EVENT 
            ORDER BY EVENT_DATE DESC ";

    // 執行活動查詢
    $eventStatement = $pdo->query($sql);
    $events = $eventStatement->fetchAll(PDO::FETCH_ASSOC);
    
    // 查詢圖片資料
    // 只取得 IMAGE_TYPE = 'main'
    $sqlImages = "SELECT IMAGE,IMAGE_TYPE,MOUNTAIN_ID
                 FROM MOUNTAIN_IMAGE 
                 WHERE IMAGE_TYPE = 'main' 
                 AND MOUNTAIN_ID IS NOT NULL";
    
    // 圖片查詢
    $imageStatement = $pdo->query($sqlImages);
    $images = $imageStatement->fetchAll(PDO::FETCH_ASSOC);
    
    // 建立圖片索引
    // 將圖片資料整理 MOUNTAIN_ID 為 key 的陣列
    $imageMap = [];
    foreach ($images as $image) {
        $imageMap[$image['MOUNTAIN_ID']] = $image['IMAGE'];
    }
    
    // 整合活動與圖片資料
    $formattedEvents = [];
    foreach ($events as $event) {
        // 根據活動的 MOUNTAIN_ID 找到對應的圖片
        // 如果找不到對應圖片，則設為 null（會使用預設圖片）
        $imageName = isset($imageMap[$event['MOUNTAIN_ID']]) 
                    ? $imageMap[$event['MOUNTAIN_ID']] 
                    : null;
        
        // 組合完整的活動資料
        $formattedEvents[] = [
            'id' => $event['EVENT_ID'],                // 活動編號
            'title' => $event['EVENT_NAME'],           // 活動標題
            'date' => $event['EVENT_DATE'],            // 活動日期
            'ctaUrl' => '/activities/' . $event['EVENT_ID'], // 活動詳情連結
            'joinQty' => $event['JOIN_QTY'],           // 報名人數
            'eventTime' => $event['EVENT_TIME'],       // 活動時間
            'meetingPlace' => $event['MEETING_PLACE'], // 集合地點
            'distance' => $event['DISTANCE'],          // 路程
            'content' => $event['CONTENT'],            // 活動簡介
            'status' => $event['STATUS'],              // 活動狀態
            'mountainId' => $event['MOUNTAIN_ID'],     // 山岳編號
            'imageName' => $imageName,                 // 圖片檔名
            'imageType' => $imageName ? 'main' : null  // 圖片類型
        ];
    }
    
    // 回傳成功結果
    echo json_encode([
        'success' => true,
        'data' => $formattedEvents,
        'count' => count($formattedEvents),
        'message' => '活動資料取得成功'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    // 資料庫錯誤處理
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '資料庫查詢失敗',
        'error' => $e->getMessage(),
        'data' => []
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // 其他錯誤處理
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '伺服器錯誤',
        'error' => $e->getMessage(),
        'data' => []
    ], JSON_UNESCAPED_UNICODE);
}
?>