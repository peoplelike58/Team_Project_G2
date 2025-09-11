<?php
    session_start();

    // 若前端在 http://localhost:5173
    header('Access-Control-Allow-Origin: http://localhost:5173'); // ⚠️ 不能用 *
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);  // 直接結束，不執行後續程式碼
}
    
    header('Content-Type: application/json; charset=utf-8');

    // 導入資料庫連線的資料檔
    include 'conn.php'; 

    //---------------------------------------------------

    if (!isset($_SESSION["memberID"])) {
        $_SESSION["memberID"] = 1;  // 測試用的固定用戶ID
    }

    $MEMBER_ID = $_SESSION["memberID"];

    $sql = "SELECT COALESCE(SUM(HEIGHT), 0) as heightTotal,
                   COALESCE(SUM(DISTANCE), 0) as kiloTotal,
                   COALESCE(SUM(DURATION), 0) as timeTotal
            FROM FOOT
            WHERE MEMBER_ID = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$MEMBER_ID]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // 🔧 格式化數值 (保留2位小數)
        $response = [
            'success' => true,
            'member_id' => $MEMBER_ID,
            'heightTotal' => number_format($result['heightTotal'], 2),
            'kiloTotal' => number_format($result['kiloTotal'], 2),
            'timeTotal' => number_format($result['timeTotal'], 2)
        ];

        echo json_encode($response, JSON_UNESCAPED_UNICODE);



?>