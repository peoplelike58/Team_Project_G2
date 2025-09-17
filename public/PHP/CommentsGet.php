<?php
// CORS（因為是公開 GET，其實可不帶 credentials。但為了統一，這樣寫也可）

// header('Access-Control-Allow-Credentials: true');
// header('Vary: Origin');

// if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
//   header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
//   header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
//   http_response_code(204);
//   exit;
// }

// header('Content-Type: application/json; charset=utf-8');

//（可有可無）開 Session，但不檢查會員
session_start();

// 取得參數（你是大寫命名）
$mountainId = isset($_GET['MOUNTAIN_ID']) ? intval($_GET['MOUNTAIN_ID']) : 0;
if ($mountainId <= 0) {
  echo json_encode(['success'=>false, 'message'=>'MOUNTAIN_ID 無效']);
  exit;
}

// 資料庫連線
require __DIR__ . '/conn.php';

// 撈資料（欄位大寫，並為圖片取別名）
$sql = "SELECT 
    msg.MESSAGE_ID,
    msg.MOUNTAIN_ID,
    msg.CONTENT,
    msg.IMAGE AS MESSAGE_IMAGE,
    msg.CREATED_AT,
    m.MEMBER_ID,
    m.NICKNAME,
    m.IMAGE AS MEMBER_IMAGE
  FROM MESSAGE msg
  JOIN MEMBER m ON m.MEMBER_ID = msg.MEMBER_ID
  WHERE msg.MOUNTAIN_ID = :mid
  ORDER BY msg.MESSAGE_ID DESC
";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':mid', $mountainId, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success'=>true, 'data'=>$rows], JSON_UNESCAPED_UNICODE);