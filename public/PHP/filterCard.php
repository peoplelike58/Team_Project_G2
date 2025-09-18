<?php

// header("Access-Control-Allow-Methods: POST, OPTIONS"); // 允許的請求方法
// header("Access-Control-Allow-Headers: Content-Type"); // 允許的自訂標頭
header('Content-Type: application/json; charset=utf-8');// 回傳 JSON
$response = ['ok' => false, 'data' => [], 'error' => null];   // 預設回傳

// 導入資料庫連線的資料檔
include 'conn.php'; 


$sql = "SELECT m.MOUNTAIN_ID, m.MOUNTAIN_NAME, m.REGION, m.DIFF, m.LEVEL, m.TYPE, img.IMAGE, m.TRAFFIC, m.DISTANCE, m.TIME, m.AREA
FROM MOUNTAIN m
	JOIN MOUNTAIN_IMAGE img
    ON m.MOUNTAIN_ID = img.MOUNTAIN_ID
WHERE img.IMAGE_TYPE = 'main'";

$pstmt = $pdo->prepare($sql);
$pstmt->execute();
$members = $pstmt->fetchAll(PDO::FETCH_ASSOC);






echo json_encode($members,JSON_UNESCAPED_UNICODE) ;
?>