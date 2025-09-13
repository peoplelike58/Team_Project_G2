<?php
// CORS 設定 - 確保這些沒有被註解
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 處理 OPTIONS 預檢請求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 導入資料庫連線
include 'conn.php'; 

try {
    // 檢查連線
    if (!isset($pdo)) {
        throw new Exception('資料庫連線失敗');
    }
    
    // 取得活動 ID
    $eventId = isset($_GET['id']) ? intval($_GET['id']) : 1;
    
    // 查詢實際存在的欄位
    $sql = "SELECT EVENT_ID, EVENT_NAME, JOIN_QTY, EVENT_DATE, EVENT_TIME,
            START_DATE, START_TIME, END_DATETIME, STATUS, CONTENT, 
            MEETING_PLACE, DISTANCE 
            FROM EVENT 
            WHERE EVENT_ID = :eventId";
    
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':eventId', $eventId, PDO::PARAM_INT);
    $statement->execute();
    
    $event = $statement->fetch(PDO::FETCH_ASSOC);
    
    if ($event) {
        // 格式化資料 - 為不存在的欄位提供預設值
        $formattedEvent = [
            'id' => $event['EVENT_ID'],
            'title' => $event['EVENT_NAME'] ?? '活動標題',
            'date' => $event['EVENT_DATE'] ?? '2025-08-05',
            'time' => $event['EVENT_TIME'] ?? '09:00:00',
            'startDate' => $event['START_DATE'] ?? '2025-08-15',
            'startTime' => $event['START_TIME'] ?? '09:00:00',
            'joinQty' => $event['JOIN_QTY'] ?? 0,
            'meetingPlace' => $event['MEETING_PLACE'] ?? '集合地點',
            'distance' => $event['DISTANCE'] ?? 15,
            'content' => $event['CONTENT'] ?? '活動簡介',
            'status' => ($event['STATUS'] == '報名中') ? '揪團中' : '已截止',
            
            // 不在資料庫中，給予預設值
            // 'route' => '冷水坑 → 七星公園 → 夢幻湖',  // 預設路線
            'duration' => 4,  // 預設時長
            'notes' => '請穿著輕便衣物與防滑鞋;建議攜帶水壺、帽子、防蚊液;活動前3日若遇大雨將公告延期',
            'imageUrl' => '@/assets/images/eventCard/cardimg1.jpg',
            'registrationDeadlineDate' => '2025-08-13',
            'registrationDeadlineTime' => '12:00:00'
        ];
        
        // 回傳成功結果
        echo json_encode([
            'success' => true,
            'data' => $formattedEvent,
            'message' => '資料載入成功'
        ], JSON_UNESCAPED_UNICODE);
        
    } else {
        // 找不到資料
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => '找不到活動資料 (ID: ' . $eventId . ')'
        ], JSON_UNESCAPED_UNICODE);
    }
    
} catch (PDOException $e) {
    // 資料庫錯誤
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '資料庫查詢錯誤',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // 其他錯誤
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '伺服器錯誤',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>