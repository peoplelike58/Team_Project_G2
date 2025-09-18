<?php
$member=json_decode(file_get_contents("php://input"), true);//接收前端來的東西，做json檔的解碼



include 'conn.php';

//不是機器人驗證 (YUKI)
// include 'verifyRecaptcha.php';

// if (!verifyRecaptcha($member['recaptcha'])){
//     echo json_encode([
//         "success" => false, 
//         "message" => "請先通過驗證"
//     ],JSON_UNESCAPED_UNICODE);
//     exit;
// }

// 密碼加密函數（與前端相同的加密方式）-（待考慮）
// function encryptPassword($password) {
//     // 使用 SHA-256 雜湊
//     return hash('sha256', $password);
// }

// 接收前端傳來的加密密碼，並再次加密以比對資料庫-（待考慮）
// $encryptedPassword = encryptPassword($member["password"]);

$sql = "SELECT MEMBER_ID,EMAIL,NAME,IMAGE from MEMBER WHERE EMAIL = :email and PW = :passwords ";

$pstmt = $pdo->prepare($sql);
$pstmt->bindValue( ":email", $member["email"]);   //前端傳來的值放在陣列裡，把這個值給到：email去sql裡尋找，：是佔位符號，：email是命名參數
$pstmt->bindValue(":passwords", $member["password"]);                //$encryptedPassword 加密
$pstmt->execute();                               //這步把這個準備好的 SQL，真的送去資料庫執行
$member = $pstmt->fetchAll();

$respBody['success'] = count($member) > 0 ; //count($member) > 0 or !empty($member) or $member != null
if ($respBody['success']) {
    session_start();
    // $_SESSION['member'] = $member;
    $_SESSION['member'] = [
    "email" => $member[0]["EMAIL"],     // 左邊 是存進session的 key: 你自己命名,右邊 table 的欄位名是 EMAIL
    "name"  => $member[0]["NAME"],
    "id"  => $member[0]["MEMBER_ID"],
    "avatar" => $member[0]["IMAGE"]
    ];

}

echo json_encode( $respBody ) ;
?>