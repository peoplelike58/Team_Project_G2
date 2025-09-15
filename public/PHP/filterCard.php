<?php

// header("Access-Control-Allow-Methods: POST, OPTIONS"); // 允許的請求方法
// header("Access-Control-Allow-Headers: Content-Type"); // 允許的自訂標頭
header('Content-Type: application/json; charset=utf-8');// 回傳 JSON
$response = ['ok' => false, 'data' => [], 'error' => null];   // 預設回傳

// 導入資料庫連線的資料檔
include 'conn.php'; 


$sql = "

select m.MOUNTAIN_ID, m.MOUNTAIN_NAME, m.REGION, m.DIFF, m.TYPE, img.IMAGE,
	m.TRAFFIC, m.TIME
from mountain m
	join mountain_image img
    on m.MOUNTAIN_ID = img.MOUNTAIN_ID
where img.IMAGE_TYPE = 'main'
   
";

$pstmt = $pdo->prepare($sql);
$pstmt->execute();
$members = $pstmt->fetchAll(PDO::FETCH_ASSOC);






echo json_encode($members,JSON_UNESCAPED_UNICODE) ;
?>