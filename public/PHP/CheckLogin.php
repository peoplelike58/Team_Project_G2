<?php
$member=json_decode(file_get_contents("php://input"), true);//接收前端來的東西，做json檔的解碼，file_get_contents("php://input") → 抓到整個 JSON 字串，json_decode(..., true) → 把 JSON 轉成 關聯陣列 (associative array)


// //解鎖資料庫（登入的賬號密碼）
//MySQL相關資訊
$db_host = "127.0.0.1";
$db_user = "tibamefe_since2021";
$db_pass = "vwRBSb.j&K#E";
$db_select = "tibamefe_tjd102g2";  //table名要記得改 tibamefe_tjd102g2

//建立資料庫連線物件
$dsn = "mysql:host=".$db_host.";dbname=".$db_select.";charset=utf8";

//建立PDO物件，並放入指定的相關資料
$pdo = new PDO($dsn, $db_user, $db_pass);

session_start();
header('Content-Type: application/json');

if (isset($_SESSION['member'])) {
  echo json_encode([
    'isLogin' => true,
    'member'  => $_SESSION['member']
  ]);
} else {
  echo json_encode([
    'isLogin' => false
  ]);
}