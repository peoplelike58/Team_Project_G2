<?php

// 導入資料庫連線的資料檔
include 'conn.php'; 

session_start();

// 後端判斷是否登入
$me = intval($_SESSION['member']['id'] ?? 0);
if ($me <= 0){
  http_response_code(401);
  echo json_encode(['success'=>false,'message'=>'尚未登入']);
  exit; 
}
$payload = json_decode(file_get_contents('php://input'), true);
$messageId = intval($payload['MESSAGE_ID'] ?? 0);

$sql = "DELETE FROM MESSAGE 
WHERE MESSAGE_ID = :msgId AND MEMBER_ID = :me";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':msgId',$messageId , PDO::PARAM_INT);
$stmt->bindValue(':me',$me , PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success'=>true, 'data'=>$rows], JSON_UNESCAPED_UNICODE);



?>