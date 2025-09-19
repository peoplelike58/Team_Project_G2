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


$sql = "SELECT msg.MESSAGE_ID, msg.MEMBER_ID, msg.CONTENT, msg.CREATED_AT, m.MOUNTAIN_NAME, m.MOUNTAIN_ID
FROM MESSAGE msg
	JOIN MOUNTAIN m
		ON msg.MOUNTAIN_ID = m.MOUNTAIN_ID
WHERE msg.MEMBER_ID = :me
ORDER BY msg.CREATED_AT DESC";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':me',$me , PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success'=>true, 'data'=>$rows], JSON_UNESCAPED_UNICODE);



?>