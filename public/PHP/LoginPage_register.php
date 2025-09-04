<?php

$member=json_decode(file_get_contents("php://input"), true);//接收前端來的東西，做json檔的解碼，file_get_contents("php://input") → 抓到整個 JSON 字串，json_decode(..., true) → 把 JSON 轉成 關聯陣列 (associative array)


// //解鎖資料庫（登入的賬號密碼）
//MySQL相關資訊
$db_host = "127.0.0.1";
$db_user = "tibamefe_since2021";
$db_pass = "vwRBSb.j&K#E";
$db_select = "tibamefe_tjd102g2";  //table名要記得改 tibamefe_tjd102g2

//建立資料庫連線物件
$dsn = "mysql:host=".$db_host.";dbname=".$db_select.";charset=utf8";

//建立PDO物件，並放入指定的相關資料
$pdo = new PDO($dsn, $db_user, $db_pass);


$statement = $pdo->prepare("select EMAIL from MEMBER where EMAIL = :email");
$statement->bindValue(":email", $member["email"]);
$statement->execute();
$checkEmail = $statement->fetch();

if($checkEmail){
    $respBody['fail'] = false ;
    $respBody['message'] = '此email已註冊過,註冊失敗!' ;
}else{
    $sql = "
    insert into MEMBER(EMAIL,NAME,PW,PHONE,CREATED_AT)
    values(:email,:username ,:password ,:phone,now())
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


echo json_encode( $respBody ) ;
