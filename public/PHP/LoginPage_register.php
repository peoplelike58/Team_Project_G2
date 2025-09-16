<?php

$member=json_decode(file_get_contents("php://input"), true);//接收前端來的東西，做json檔的解碼，file_get_contents("php://input") → 抓到整個 JSON 字串，json_decode(..., true) → 把 JSON 轉成 關聯陣列 (associative array)


include 'conn.php';


//不是機器人驗證 (YUKI)
include 'verifyRecaptcha.php';
if (!verifyRecaptcha($member['recaptcha'])){
    echo json_encode([
        "sucess" => false,
         "message" => "請先通過驗證"
    ]);
    exit;
}


$statement = $pdo->prepare("select EMAIL from MEMBER where EMAIL = :email");
$statement->bindValue(":email", $member["email"]);
$statement->execute();
$checkEmail = $statement->fetch();

if($checkEmail){
    $respBody['fail'] = false ;
    $respBody['message'] = '此email已註冊過,註冊失敗!' ;
}else{
    $sql = "
    insert into MEMBER(EMAIL,NAME,PW,PHONE,CREATED_AT,STATUS)
    values(:email,:username ,:password ,:phone,now(),'啟用')
    ";

    $pstmt = $pdo->prepare($sql);
    $pstmt->bindValue(":email", $member["email"]);
    $pstmt->bindValue( ":username", $member["name"]);
    $pstmt->bindValue(":password", $member["password"]);
    $pstmt->bindValue(":phone", $member["phone"]);
    $register=$pstmt->execute();

    $respBody['success'] = true ;
    $respBody['message'] = '此email未註冊過,註冊成功!' ;
}


echo json_encode( $respBody ,JSON_UNESCAPED_UNICODE) ;
