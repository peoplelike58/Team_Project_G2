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
    // 取得請求參數
    $memberId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;
    $eventId = isset($_GET['eventId']) ? intval($_GET['eventId']) : 0;
    
    // 驗證參數
    if ($memberId <= 0 || $eventId <= 0) {
        throw new Exception('缺少必要參數或參數無效');
    }
    
    // 建立資料庫連接
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_select);
    
    // 檢查連接
    if ($conn->connect_error) {
        throw new Exception('資料庫連接失敗：' . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    
    // 查詢使用者是否已報名此活動
    $sql = "SELECT MEMBER_ID,EVENT_ID,STATUS,JOIN_AT
            FROM MEMBER_EVENT 
            WHERE MEMBER_ID = ? 
            AND EVENT_ID = ? 
            AND STATUS != 'cancelled'
            LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('SQL 準備失敗：' . $conn->error);
    }
    
    // 綁定參數
    $stmt->bind_param("ii", $memberId, $eventId);
    
    // 執行查詢
    if (!$stmt->execute()) {
        throw new Exception('查詢執行失敗：' . $stmt->error);
    }
    
    // 取得結果
    $result = $stmt->get_result();
    
    // 準備回應資料
    $response = [
        'success' => true,
        'hasRegistered' => false,
        'registrationInfo' => null
    ];
    
    // 檢查是否有報名記錄
    if ($result->num_rows > 0) {
        $registration = $result->fetch_assoc();
        $response['hasRegistered'] = true;
        $response['registrationInfo'] = [
            'memberId' => $registration['MEMBER_ID'],
            'eventId' => $registration['EVENT_ID'],
            'status' => $registration['STATUS'],
            'joinDate' => $registration['JOIN_AT']
        ];
    }
    
    // 關閉連接
    $stmt->close();
    $conn->close();
    
    // 回傳 JSON 結果
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // 錯誤處理
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