<?php  /* 更新個人資料-會員中心 */

include 'conn.php';

$update=json_decode(file_get_contents("php://input"), true);


session_start();
if (!isset($_SESSION['member']) || !isset($_SESSION['member']['id'])) {
echo json_encode(['success' => false, 'message' => '尚未登入'],JSON_UNESCAPED_UNICODE); exit;
}

$memberId = $_SESSION['member']['id']; // 從 Session 拿會員ID

$sql = "UPDATE MEMBER SET NICKNAME = ?,NAME = ?,BIRTHDAY = ?,PHONE = ?,ADDRESS = ?,IMAGE = ?WHERE MEMBER_ID = ?";

$pstmt = $pdo->prepare($sql);
$pstmt->bindValue( ":memberId", $memberId);
$pstmt->execute();
$profileData = $pstmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'message' => '個人資料已更新','profileData'=>$profileData],JSON_UNESCAPED_UNICODE);
?>