<?php
// 若前端在 http://localhost:5173
header('Access-Control-Allow-Origin: http://localhost:5173'); // ⚠️ 不能用 *
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');


// $URL = "mysql:host=localhost:3306;charset=utf8mb4;dbname=hou_shan";
// $USERNAME = "root";
// $PASSWORD = "password";
// $pdo = new PDO($URL, $USERNAME, $PASSWORD);


// 導入資料庫連線的資料檔
include 'conn.php'; 


$sql = "

SELECT
  m.MOUNTAIN_ID,                                         
  m.MOUNTAIN_NAME, m.INTRO, m.REGION, m.TOWN,           
  m.LEVEL, m.TRAFFIC, m.DISTANCE,                        
  JSON_ARRAYAGG(img.IMAGE) AS imgDetail
FROM mountain  m                                      
LEFT JOIN mountain_image img                         
  ON m.MOUNTAIN_ID = img.MOUNTAIN_ID                      
GROUP BY
  m.MOUNTAIN_ID, m.MOUNTAIN_NAME, m.INTRO, m.REGION,    
  m.TOWN, m.LEVEL, m.TRAFFIC, m.DISTANCE;

   
";

$pstmt = $pdo->prepare($sql);
$pstmt->execute();
$members = $pstmt->fetchAll(PDO::FETCH_ASSOC);






echo json_encode($members,JSON_UNESCAPED_UNICODE) ;
?>