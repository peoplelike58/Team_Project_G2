<?php
$member=json_decode(file_get_contents("php://input"), true);//接收前端來的東西，做json檔的解碼，file_get_contents("php://input") → 抓到整個 JSON 字串，json_decode(..., true) → 把 JSON 轉成 關聯陣列 (associative array)

include 'toMysql.php';


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