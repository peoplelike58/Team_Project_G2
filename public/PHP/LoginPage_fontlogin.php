<?php
$member=json_decode(file_get_contents("php://input"), true);//接收前端來的東西，做json檔的解碼

include 'toMysql.php';

$sql = "
select EMAIL,NAME from MEMBER where EMAIL = :email and PW = :password 
";

$pstmt = $pdo->prepare($sql);
$pstmt->bindValue( ":email", $member["email"]);   //前端傳來的值放在陣列裡，把這個值給到：email去sql裡尋找，：是佔位符號，：email是命名參數
$pstmt->bindValue(":password", $member["password"]);
$pstmt->execute();                               //這步把這個準備好的 SQL，真的送去資料庫執行
$member = $pstmt->fetchAll();

$respBody['success'] = count($member) > 0 ; //count($member) > 0 or !empty($member) or $member != null
if ($respBody['success']) {
    session_start();
    // $_SESSION['member'] = $member;
    $_SESSION['member'] = [
    "email" => $member[0]["EMAIL"],     // 左邊 是存進session的 key: 你自己命名,右邊 table 的欄位名是 EMAIL
    "name"  => $member[0]["NAME"]     
    ];

}

echo json_encode( $respBody ) ;
?>