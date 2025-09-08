<?php
    // 設定跨域請求以及一些讓axios可以正確解析的設定
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');

    // 導入資料庫連線的資料檔
    include 'conn.php'; 

try {
    // SQL指令查詢對應資料庫
    $sql = "SELECT EVENT_ID,EVENT_NAME,JOIN_QTY,EVENT_DATE,EVENT_TIME,START_DATE,START_TIME,END_DATETIME,
            STATUS,CONTENT,MEETING_PLACE,DISTANCE 
            FROM EVENT 
            ORDER BY EVENT_DATE DESC ";

    //執行並查詢，會回傳查詢結果的物件，必須使用fetch、fetchAll...等方式取得資料
    $statement = $pdo->query($sql);

    //取得所有查詢結果 以關聯陣列格式返回
    $events = $statement->fetchAll(PDO::FETCH_ASSOC);

    //資料格式轉換
    $formattedEvents = [];
    foreach ($events as $event) {
        $formattedEvents[] = [
            'id' => $event['EVENT_ID'], //卡片編號
            'title' => $event['EVENT_NAME'], //卡片標題
            'date' => $event['EVENT_DATE'], // 卡片日期
            'ctaUrl' => '/activities' . $event['EVENT_ID'], //卡片網址
            'joinQty' => $event['JOIN_QTY'], //報名人數
            'eventTime' => $event['EVENT_TIME'], //活動時間
            'meetingPlace' => $event['MEETING_PLACE'], //集合地點
            'distance' => $event['DISTANCE'], //路程
            'content' => $event['CONTENT'], //活動簡介
            'status' => $event['STATUS'] //活動狀態（0 & 1 取決於顯示與不顯示或是揪團中or截止）
        ];
    }

    // 回傳成功結果
    echo json_encode([
        'success' => true,
        'data' => $formattedEvents,
        'count' => count($formattedEvents)
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    // 資料庫錯誤處理
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '資料庫查詢失敗',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // 其他錯誤處理
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '伺服器錯誤',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>