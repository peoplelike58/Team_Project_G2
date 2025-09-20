<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// 處理 OPTIONS 請求（預檢請求）
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'conn.php';

try {
    // 取得請求參數
    $memberId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;
    $eventId = isset($_GET['eventId']) ? intval($_GET['eventId']) : 0;
    
    // 驗證參數
    if ($memberId <= 0 || $eventId <= 0) {
        throw new Exception('缺少必要參數或參數無效');
    }
    
    // 檢查 PDO 連接是否存在
    if (!isset($pdo)) {
        throw new Exception('資料庫連接失敗');
    }
    
    // 查詢使用者是否已報名此活動 (使用 PDO)
    $sql = "SELECT MEMBER_ID, EVENT_ID, STATUS, JOIN_AT
            FROM MEMBER_EVENT 
            WHERE MEMBER_ID = :memberId 
            AND EVENT_ID = :eventId 
            AND STATUS != 'cancelled'
            LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('SQL 準備失敗');
    }
    
    // 綁定參數
    $stmt->bindParam(':memberId', $memberId, PDO::PARAM_INT);
    $stmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
    
    // 執行查詢
    if (!$stmt->execute()) {
        throw new Exception('查詢執行失敗');
    }
    
    // 取得結果
    $registration = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // 準備回應資料
    $response = [
        'success' => true,
        'hasRegistered' => false,
        'registrationInfo' => null
    ];
    
    // 檢查是否有報名記錄
    if ($registration) {
        $response['hasRegistered'] = true;
        $response['registrationInfo'] = [
            'memberId' => $registration['MEMBER_ID'],
            'eventId' => $registration['EVENT_ID'],
            'status' => $registration['STATUS'],
            'joinDate' => $registration['JOIN_AT']
        ];
    }
    
    // 回傳 JSON 結果
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    // PDO 錯誤處理
    $errorResponse = [
        'success' => false,
        'hasRegistered' => false,
        'message' => '資料庫查詢錯誤',
        'error' => $e->getMessage()
    ];
    
    http_response_code(500);
    echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // 一般錯誤處理
    $errorResponse = [
        'success' => false,
        'hasRegistered' => false,
        'message' => $e->getMessage()
    ];
    
    // 設定 HTTP 狀態碼
    http_response_code(400);
    
    // 回傳錯誤訊息
    echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE);
}
?>