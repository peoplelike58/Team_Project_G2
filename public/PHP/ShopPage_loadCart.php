<?php
include 'conn.php';


session_start();

if (!isset($_SESSION['member']) || !isset($_SESSION['member']['id'])) {
  echo json_encode(['success' => false, 'message' => '尚未登入']); exit;
}

$memberId = $_SESSION['member']['id']; // 從 Session 拿會員ID

$sql = "select * from CART where MEMBER_ID = $memberId";

$sql = "
select MEMBER_ID,EMAIL,NAME from MEMBER where EMAIL = :email and PW = :password 
";

$pstmt = $pdo->prepare($sql);
$pstmt->bindValue( ":email", $member["email"]);   //前端傳來的值放在陣列裡，把這個值給到：email去sql裡尋找，：是佔位符號，：email是命名參數
$pstmt->execute();                               //這步把這個準備好的 SQL，真的送去資料庫執行
$Items = $pstmt->fetchAll();

  echo json_encode(['success' => true, 'message' => '購物車已同步新增到後端']);
?>