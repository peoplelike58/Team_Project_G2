<?php  /* LoginPage_updateProfile.php更新個人資料-會員中心 */

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

// 更新資料庫成功後，同時更新 session 中的頭像資料
// 這樣其他頁面呼叫 hydrateFromSession() 時就會拿到最新的頭像檔名
$_SESSION['member']['nickname'] = $update['nickname'];
$_SESSION['member']['about'] = $update['about'];
$_SESSION['member']['birthday'] = $update['birthday'];
$_SESSION['member']['phone'] = $update['phone'];
$_SESSION['member']['address'] = $update['address'];


echo json_encode(['success' => true, 'message' => '個人資料已更新','profileData'=>$profileData],JSON_UNESCAPED_UNICODE);
?>