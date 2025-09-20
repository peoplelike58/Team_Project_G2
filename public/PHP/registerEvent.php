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

// 開啟 session
session_start();

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
    
    $memberId = isset($inputData['userId']) ? intval($inputData['userId']) : 0;
    $eventId = isset($inputData['eventId']) ? intval($inputData['eventId']) : 0;
    $userName = isset($inputData['userName']) ? trim($inputData['userName']) : '';
    $userEmail = isset($inputData['userEmail']) ? trim($inputData['userEmail']) : '';
    
    // 參數驗證
    if ($memberId <= 0 || $eventId <= 0) {
        throw new Exception('會員ID或活動ID無效');
    }
    
    if (empty($userName) || empty($userEmail)) {
        throw new Exception('會員姓名或電子郵件不能為空');
    }
    
    // 驗證 email 格式
    if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('電子郵件格式無效');
    }
    
    // 檢查 PDO 連接是否存在
    if (!isset($pdo)) {
        throw new Exception('資料庫連接失敗');
    }
    
    // 開始資料庫交易
    $pdo->beginTransaction();
    
    try {
        // 先檢查會員是否已經報名過此活動
        $checkSql = "SELECT MEMBER_ID, STATUS 
                     FROM MEMBER_EVENT 
                     WHERE MEMBER_ID = :memberId 
                     AND EVENT_ID = :eventId 
                     AND STATUS != 'cancelled'";
        
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->bindParam(':memberId', $memberId, PDO::PARAM_INT);
        $checkStmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $checkStmt->execute();
        
        $existingRegistration = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingRegistration) {
            throw new Exception('您已經報名過此活動，目前狀態為：' . $existingRegistration['STATUS']);
        }
        
        // 檢查活動資訊和報名狀況（從 EVENT 表獲取活動資訊）
        $eventSql = "SELECT EVENT_ID, EVENT_NAME, JOIN_QTY, END_DATETIME, STATUS, EVENT_DATE,
                            EVENT_TIME, START_DATE, START_TIME, MEETING_PLACE
                     FROM EVENT 
                     WHERE EVENT_ID = :eventId 
                     AND STATUS = '報名中'";
        
        $eventStmt = $pdo->prepare($eventSql);
        $eventStmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $eventStmt->execute();
        
        $eventInfo = $eventStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$eventInfo) {
            throw new Exception('活動不存在或已關閉報名');
        }
        
        // 檢查報名截止時間
        if ($eventInfo['END_DATETIME'] && 
            strtotime($eventInfo['END_DATETIME']) < time()) {
            throw new Exception('報名已截止');
        }
        
        // 計算目前的報名人數
        $countSql = "SELECT COUNT(*) as current_count 
                     FROM MEMBER_EVENT 
                     WHERE EVENT_ID = :eventId 
                     AND STATUS != 'cancelled'";
        
        $countStmt = $pdo->prepare($countSql);
        $countStmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $countStmt->execute();
        
        $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
        $currentCount = $countResult['current_count'];
        
        // 檢查是否有名額限制（如果需要的話可以取消註解）
        // if ($eventInfo['JOIN_QTY'] > 0 && $currentCount >= $eventInfo['JOIN_QTY']) {
        //     throw new Exception('活動名額已滿');
        // }
        
        // 新增報名記錄到 MEMBER_EVENT 表
        $insertSql = "INSERT INTO MEMBER_EVENT 
                      (MEMBER_ID, EVENT_ID, STATUS, JOIN_AT) 
                      VALUES (:memberId, :eventId, 'registered', NOW())";
        
        $insertStmt = $pdo->prepare($insertSql);
        $insertStmt->bindParam(':memberId', $memberId, PDO::PARAM_INT);
        $insertStmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        
        if (!$insertStmt->execute()) {
            throw new Exception('報名失敗，請稍後再試');
        }
        
        // 更新 EVENT 表的報名人數（JOIN_QTY）
        $newJoinQty = $currentCount + 1;
        $updateSql = "UPDATE EVENT 
                      SET JOIN_QTY = :joinQty 
                      WHERE EVENT_ID = :eventId";
        
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->bindParam(':joinQty', $newJoinQty, PDO::PARAM_INT);
        $updateStmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        
        if (!$updateStmt->execute()) {
            throw new Exception('更新報名人數失敗');
        }
        
        // 提交交易
        $pdo->commit();
        
        // 成功回應
        $response = [
            'success' => true,
            'message' => '報名成功！',
            'data' => [
                'eventName' => $eventInfo['EVENT_NAME'],
                'eventDate' => $eventInfo['EVENT_DATE'],
                'eventTime' => $eventInfo['EVENT_TIME'],
                'meetingPlace' => $eventInfo['MEETING_PLACE'],
                'currentParticipants' => $newJoinQty,
                'registrationDate' => date('Y-m-d H:i:s')
            ]
        ];
        
        // 回傳成功結果
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        // 回滾交易
        $pdo->rollback();
        throw $e;
    }
    
} catch (PDOException $e) {
    // PDO 錯誤處理
    $errorResponse = [
        'success' => false,
        'message' => '資料庫操作錯誤',
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
    
    // 設定 HTTP 狀態碼
    http_response_code(400);
    
    // 回傳錯誤訊息
    echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE);
}
?>