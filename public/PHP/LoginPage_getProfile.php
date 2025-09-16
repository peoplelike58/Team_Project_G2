<?php  /* 抓取個人資料-會員中心 */

include 'conn.php';

session_start();
if (!isset($_SESSION['member']) || !isset($_SESSION['member']['id'])) {
echo json_encode(['success' => false, 'message' => '尚未登入'],JSON_UNESCAPED_UNICODE); exit;
}

$memberId = $_SESSION['member']['id']; // 從 Session 拿會員ID

$sql = "SELECT NICKNAME, BIRTHDAY,PHONE,ADDRESS,ABOUT_ME FROM MEMBER WHERE MEMBER_ID = :memberId";

$pstmt = $pdo->prepare($sql);
$pstmt->bindValue( ":memberId", $memberId);
$pstmt->execute();
$profileData = $pstmt->fetch(PDO::FETCH_ASSOC);

// 回傳成功結果
// echo json_encode([
//     'nickname' => $profileData['NICKNAME'],
//     'birthday' => $profileData['BIRTHDAY'],
//     'phone' => $profileData['PHONE'],
//     'address' => $profileData['ADDRESS'],
//     'aboutme'=> $profileData['ABOUTME']
// ]);

//  檢查是否有找到資料
if ($profileData) {
    // 成功找到資料，回傳結果
    // 為了配合前端邏輯，將單筆資料包裝成陣列
    echo json_encode([
        'success' => true, 
        'message' => '成功取得個人資料',
        'profileData' => [$profileData]  // 包成陣列格式，讓前端可以用 [0] 取得
    ], JSON_UNESCAPED_UNICODE);
} else {
    // 沒找到資料
    echo json_encode([
        'success' => false, 
        'message' => '找不到會員資料，會員ID: ' . $memberId
    ], JSON_UNESCAPED_UNICODE);
}
?>
