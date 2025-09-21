<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'conn.php';

try {
    // 檢查連線
    if (!isset($pdo)) {
        throw new Exception('資料庫連線失敗');
    }

    // 獲取 mountainId 參數
    $mountainId = $_GET['mountainId'] ?? null;

    if (empty($mountainId)) {
        throw new Exception('缺少必要參數 mountainId');
    }

    // 驗證 mountainId 是否為數字
    if (!is_numeric($mountainId)) {
        throw new Exception('mountainId 必須是數字');
    }

    $sql = "SELECT MOUNTAIN_ID,MOUNTAIN_NAME,REGION,INTRO,TOWN,DIFF,LEVEL,TRAFFIC,DISTANCE,
            TIME,TYPE,LONGITUDE,LATITUDE,AREA
            FROM MOUNTAIN 
            WHERE MOUNTAIN_ID = :mountainId";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':mountainId', $mountainId, PDO::PARAM_INT);
    $stmt->execute();

    $mountainData = $stmt->fetch();

    if (!$mountainData) {
        throw new Exception('找不到指定的山岳資料');
    }

    // 檢查經緯度資料是否存在
    if (empty($mountainData['LATITUDE']) || empty($mountainData['LONGITUDE'])) {
        throw new Exception('該山岳缺少位置資訊');
    }

    // 確保經緯度為數值型態
    $mountainData['LATITUDE'] = (float) $mountainData['LATITUDE'];
    $mountainData['LONGITUDE'] = (float) $mountainData['LONGITUDE'];
    $mountainData['MOUNTAIN_ID'] = (int) $mountainData['MOUNTAIN_ID'];

    // 回傳成功結果
    echo json_encode([
        'success' => true,
        'message' => '山岳資料獲取成功',
        'data' => $mountainData
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    // 資料庫錯誤
    error_log("Database Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '資料庫連線錯誤'
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // 一般錯誤
    error_log("Error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);

} finally {
    // 關閉資料庫連線
    $pdo = null;
}
?>