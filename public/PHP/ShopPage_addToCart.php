<?php

session_start();
include 'conn.php';

$data=json_decode(file_get_contents("php://input"), true);//接收前端來的東西，做json檔的解碼，file_get_contents("php://input") → 抓到整個 JSON 字串，json_decode(..., true) → 把 JSON 轉成 關聯陣列 (associative array)


$cartItems = $data['cartItems'];

if (!isset($_SESSION['member']) || !isset($_SESSION['member']['id'])) {
  echo json_encode(['success' => false, 'message' => '尚未登入']); exit;
}

$memberId = $_SESSION['member']['id']; // 從 Session 拿會員ID

$sql = "
insert into CART(MEMBER_ID,PRODUCT_ID,SIZE,COLOR,QUANTITY)
values (:memberId ,:productID ,:size ,:color,:quantity)
ON DUPLICATE KEY UPDATE 
  QUANTITY = :quantity
";

foreach ($cartItems as $item) {   //cartItems裡面是陣列包物件
  $pstmt = $pdo->prepare($sql);
  $pstmt->bindValue( ":memberId", $memberId);
  $pstmt->bindValue(":productID", $item["productId"]);
  $pstmt->bindValue(":size", $item["size"]);
  $pstmt->bindValue(":color", $item["color"]);
  $pstmt->bindValue(":quantity", $item["qty"]);
  $pstmt->execute();
}

  echo json_encode(['success' => true, 'message' => '購物車已同步新增到後端']);
?>