<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// 處理 OPTIONS 請求（預檢請求）
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'conn.php';

try {
    // 取得請求參數
    $memberId = isset($_GET['memberId']) ? intval($_GET['memberId']) : 0;
    
    // 驗證參數
    if ($memberId <= 0) {
        throw new Exception('無效的會員 ID');
    }
    
    // 檢查 PDO 連接
    if (!isset($pdo)) {
        throw new Exception('資料庫連接失敗');
    }
    
    // 查詢會員的所有報名活動（只查詢未取消的）
    $sql = "SELECT me.MEMBER_ID,me.EVENT_ID,me.JOIN_AT,me.STATUS as REGISTRATION_STATUS,e.EVENT_NAME,
                e.EVENT_DATE,e.EVENT_TIME,e.START_DATE,e.START_TIME,e.END_DATETIME,e.STATUS as EVENT_STATUS,
                e.CONTENT,e.MEETING_PLACE,e.DISTANCE,e.MOUNTAIN_ID,e.JOIN_QTY
            FROM MEMBER_EVENT me
            INNER JOIN EVENT e ON me.EVENT_ID = e.EVENT_ID
            WHERE me.MEMBER_ID = :memberId
            AND me.STATUS != 'cancelled'
            ORDER BY me.JOIN_AT DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':memberId', $memberId, PDO::PARAM_INT);
    $stmt->execute();
    
    $events = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // 格式化日期時間
        $joinAt = $row['JOIN_AT'] ? new DateTime($row['JOIN_AT']) : new DateTime();
        $startDate = $row['START_DATE'] ? new DateTime($row['START_DATE']) : null;
        $startDateTime = null;
        
        // 組合出發日期和時間
        if ($row['START_DATE'] && $row['START_TIME']) {
            $startDateTime = new DateTime($row['START_DATE'] . ' ' . $row['START_TIME']);
        }
        
        $events[] = [
            'id' => $row['EVENT_ID'],
            'name' => $row['EVENT_NAME'],
            'registerDate' => $joinAt->format('Y/m/d'),
            'registerTime' => $joinAt->format('H:i'),
            'departureDate' => $startDate ? $startDate->format('Y/m/d') : '',
            'departureTime' => $row['START_TIME'] ? substr($row['START_TIME'], 0, 5) : '',
            'eventDate' => $row['EVENT_DATE'],
            'eventTime' => $row['EVENT_TIME'],
            'status' => $row['EVENT_STATUS'],
            'registrationStatus' => $row['REGISTRATION_STATUS'],
            'content' => $row['CONTENT'],
            'meetingPlace' => $row['MEETING_PLACE'],
            'distance' => $row['DISTANCE'],
            'mountainId' => $row['MOUNTAIN_ID'],
            'joinQty' => $row['JOIN_QTY']
        ];
    }
    
    // 成功回應
    $response = [
        'success' => true,
        'data' => $events,
        'count' => count($events),
        'memberId' => $memberId
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    // PDO 錯誤處理
    $errorResponse = [
        'success' => false,
        'message' => '資料庫查詢錯誤',
        'error' => $e->getMessage()
    ];
    
    http_response_code(500);
    echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // 一般錯誤處理
    $errorResponse = [
        'success' => false,
        'message' => $e->getMessage()
    ];
    
    http_response_code(400);
    echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE);
}
?>