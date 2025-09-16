<?php  /* 抓取個人資料-會員中心 */

include 'conn.php';

session_start();
if (!isset($_SESSION['member']) || !isset($_SESSION['member']['id'])) {
echo json_encode(['success' => false, 'message' => '尚未登入'],JSON_UNESCAPED_UNICODE); exit;
}

$memberId = $_SESSION['member']['id']; // 從 Session 拿會員ID

$sql = "SELECT NICKNAME, BIRTHDAY,PHONE,ADDRESS,ABOUTME FROM MEMBER WHERE MEMBER_ID = :memberId";

$pstmt = $pdo->prepare($sql);
$pstmt->bindValue( ":memberId", $memberId);
$pstmt->execute();
$profileData = $pstmt->fetchAll(PDO::FETCH_ASSOC);

// 回傳成功結果
// echo json_encode([
//     'nickname' => $profileData['NICKNAME'],
//     'birthday' => $profileData['BIRTHDAY'],
//     'phone' => $profileData['PHONE'],
//     'address' => $profileData['ADDRESS'],
//     'aboutme'=> $profileData['ABOUTME']
// ]);


echo json_encode(['success' => true, 'message' => '個人資料已更新','profileData'=>$profileData],JSON_UNESCAPED_UNICODE);
?>
