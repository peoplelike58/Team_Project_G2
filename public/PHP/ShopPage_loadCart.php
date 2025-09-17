<?php  /* 更新載入購物車php */

include 'conn.php';

session_start();

if (!isset($_SESSION['member']) || !isset($_SESSION['member']['id'])) {
  echo json_encode(['success' => false, 'message' => '尚未登入']); exit;
}

$memberId = $_SESSION['member']['id']; // 從 Session 拿會員ID

$sql ="SELECT 
  CART.CART_ID,
  CART.MEMBER_ID,
  CART.PRODUCT_ID,
  CART.SIZE,
  CART.COLOR,
  CART.QUANTITY,
  PRODUCT.PRODUCT_NAME,
  PRODUCT.PRICE,
  PRODUCT.IMAGE
FROM CART 
JOIN PRODUCT ON CART.PRODUCT_ID = PRODUCT.PRODUCT_ID
WHERE CART.MEMBER_ID = :memberId
ORDER BY CART.CREATED_AT DESC";


$pstmt = $pdo->prepare($sql);
$pstmt->bindValue( ":memberId",$memberId );   //前端傳來的值放在陣列裡，把這個值給到：email去sql裡尋找，：是佔位符號，：email是命名參數
$pstmt->execute();                               //這步把這個準備好的 SQL，真的送去資料庫執行
$Items = $pstmt->fetchAll(PDO::FETCH_ASSOC);  //取消重複鍵值


echo json_encode(['success' => true, 'message' => '購物車已同步新增到後端','cartItems'=>$Items],JSON_UNESCAPED_UNICODE);
?>