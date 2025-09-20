<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// 處理 OPTIONS 請求（預檢請求）
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'conn.php';

try {
    // 確保是 POST 請求
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('只接受 POST 請求');
    }
    
    // 取得 POST 資料
    $inputData = json_decode(file_get_contents('php://input'), true);
    
    // 驗證必要參數
    if (!$inputData) {
        throw new Exception('無效的請求資料');
    }
    
    $memberId = isset($inputData['memberId']) ? intval($inputData['memberId']) : 0;
    $eventId = isset($inputData['eventId']) ? intval($inputData['eventId']) : 0;
    
    // 參數驗證
    if ($memberId <= 0 || $eventId <= 0) {
        throw new Exception('會員ID或活動ID無效');
    }
    
    // 檢查 PDO 連接
    if (!isset($pdo)) {
        throw new Exception('資料庫連接失敗');
    }
    
    $pdo->beginTransaction();
    
    try {
        // 檢查報名記錄是否存在
        $checkSql = "SELECT MEMBER_ID, EVENT_ID, STATUS 
                     FROM MEMBER_EVENT 
                     WHERE MEMBER_ID = :memberId 
                     AND EVENT_ID = :eventId 
                     AND STATUS != 'cancelled'";
        
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->bindParam(':memberId', $memberId, PDO::PARAM_INT);
        $checkStmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $checkStmt->execute();
        
        $registration = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$registration) {
            throw new Exception('找不到此報名記錄或已經取消');
        }
        
        // 更新報名狀態為 'cancelled'
        $updateSql = "UPDATE MEMBER_EVENT 
                      SET STATUS = 'cancelled' 
                      WHERE MEMBER_ID = :memberId 
                      AND EVENT_ID = :eventId";
        
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->bindParam(':memberId', $memberId, PDO::PARAM_INT);
        $updateStmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        
        if (!$updateStmt->execute()) {
            throw new Exception('取消報名失敗');
        }
        
        // 更新活動的報名人數
        $updateEventSql = "UPDATE EVENT 
                          SET JOIN_QTY = GREATEST(0, JOIN_QTY - 1) 
                          WHERE EVENT_ID = :eventId";
        
        $updateEventStmt = $pdo->prepare($updateEventSql);
        $updateEventStmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        
        if (!$updateEventStmt->execute()) {
            throw new Exception('更新活動報名人數失敗');
        }
        
        // 取得活動資訊以回傳
        $eventSql = "SELECT EVENT_NAME, JOIN_QTY 
                     FROM EVENT 
                     WHERE EVENT_ID = :eventId";
        
        $eventStmt = $pdo->prepare($eventSql);
        $eventStmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $eventStmt->execute();
        
        $eventInfo = $eventStmt->fetch(PDO::FETCH_ASSOC);
        
        $pdo->commit();
        
        $response = [
            'success' => true,
            'message' => '已成功取消報名',
            'data' => [
                'eventId' => $eventId,
                'eventName' => $eventInfo['EVENT_NAME'],
                'currentParticipants' => $eventInfo['JOIN_QTY'],
                'cancelDate' => date('Y-m-d H:i:s')
            ]
        ];
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        // 回滾交易
        $pdo->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    $errorResponse = [
        'success' => false,
        'message' => $e->getMessage()
    ];
    
    http_response_code(400);
    echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE);
}
?>