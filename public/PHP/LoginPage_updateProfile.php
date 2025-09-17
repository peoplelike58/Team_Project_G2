<?php  /* 更新個人資料-會員中心 */

include 'conn.php';

$update=json_decode(file_get_contents("php://input"), true);


session_start();
if (!isset($_SESSION['member']) || !isset($_SESSION['member']['id'])) {
echo json_encode(['success' => false, 'message' => '尚未登入'],JSON_UNESCAPED_UNICODE); exit;
}

$memberId = $_SESSION['member']['id']; // 從 Session 拿會員ID

$sql = "UPDATE MEMBER SET NICKNAME = :nickname,ABOUT_ME = :about,BIRTHDAY = :birthday,PHONE = :phone, ADDRESS = :address WHERE MEMBER_ID = :memberId";

$pstmt = $pdo->prepare($sql);
$pstmt->bindValue( ":nickname", $update['nickname']);
$pstmt->bindValue( ":about", $update['about']);
$pstmt->bindValue( ":birthday", $update['birthday']);
$pstmt->bindValue( ":phone", $update['phone']);
$pstmt->bindValue( ":address", $update['address']);
$pstmt->bindValue( ":memberId", $memberId);
$pstmt->execute();
$profileData = $pstmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'message' => '個人資料已更新','profileData'=>$profileData],JSON_UNESCAPED_UNICODE);
?>