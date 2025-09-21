<?php

$member=json_decode(file_get_contents("php://input"), true);//接收前端來的東西，做json檔的解碼，file_get_contents("php://input") → 抓到整個 JSON 字串，json_decode(..., true) → 把 JSON 轉成 關聯陣列 (associative array)


include 'conn.php';

//密碼驗證函數
function validatePassword($password) {
    // 檢查長度 8-16 位
    if (strlen($password) < 8 || strlen($password) > 16) {
        return [
            'valid' => false, 
            'message' => '密碼長度必須為8-16位'
        ];
    }
    
    // 檢查是否包含小寫字母 (使用正則表達式)
    if (!preg_match('/[a-z]/', $password)) {
        return [
            'valid' => false, 
            'message' => '密碼必須包含小寫英文字母'
        ];
    }
    
    // 檢查是否包含大寫字母
    if (!preg_match('/[A-Z]/', $password)) {
        return [
            'valid' => false, 
            'message' => '密碼必須包含大寫英文字母'
        ];
    }
    
    return ['valid' => true, 'message' => '密碼格式正確'];
}

// 密碼驗證檢查
$passwordCheck = validatePassword($member["password"]);
if (!$passwordCheck['valid']) {
    echo json_encode([
        "success" => false,
        "message" => "註冊失敗: " . $passwordCheck['message']
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
//不是機器人驗證 (YUKI)
// include 'verifyRecaptcha.php';
// if (!verifyRecaptcha($member['recaptcha'])){
//     echo json_encode([
//         "sucess" => false,
//          "message" => "請先通過驗證"
//     ],JSON_UNESCAPED_UNICODE);
//     exit;
// }

$hashedPassword = md5($member["password"]);    // 將使用者輸入的密碼用 MD5 加密

$statement = $pdo->prepare("select EMAIL from MEMBER where EMAIL = :email");
$statement->bindValue(":email", $member["email"]);
$statement->execute();
$checkEmail = $statement->fetch();

if($checkEmail){
    $respBody['success'] = false ;
    $respBody['message'] = '註冊失敗: 此email已註冊過 !' ;
    echo json_encode( $respBody ,JSON_UNESCAPED_UNICODE) ;
    exit;
}else{
    $sql = "
    insert into MEMBER(EMAIL,NAME,PW,NICKNAME,PHONE,CREATED_AT,STATUS)
    values(:email,:username ,:password ,:nickname,:phone,now(),'啟用')
    ";

    $pstmt = $pdo->prepare($sql);
    $pstmt->bindValue(":email", $member["email"]);
    $pstmt->bindValue( ":username", $member["name"]);
    $pstmt->bindValue(":password", $hashedPassword);    // 原本是 $member["password"]，改為 $hashedPassword
    $pstmt->bindValue(":nickname", $member["name"]);
    $pstmt->bindValue(":phone", $member["phone"]);
    $register=$pstmt->execute();

    if ($register) {
        // 3. 取得新插入的會員 ID
        $newMemberId = $pdo->lastInsertId();
        
        // 4. 查詢完整的會員資料（用於建立 session）
        $selectSql = "SELECT MEMBER_ID, EMAIL, NAME, NICKNAME, PHONE, ADDRESS, IMAGE 
                      FROM MEMBER WHERE MEMBER_ID = :memberId";
        $selectStmt = $pdo->prepare($selectSql);
        $selectStmt->bindValue(":memberId", $newMemberId);
        $selectStmt->execute();
        $newMember = $selectStmt->fetch(PDO::FETCH_ASSOC);
        
        // 5. 建立 session（自動登入）
        session_start();
        $_SESSION['member'] = [
            "id" => $newMember["MEMBER_ID"],
            "email" => $newMember["EMAIL"],
            "name" => $newMember["NAME"],
            "nickname" => $newMember["NICKNAME"] ?? '',
            "phone" => $newMember["PHONE"] ?? '',
            "address" => $newMember["ADDRESS"] ?? '',
            "avatar" => $newMember["IMAGE"] ?? ''
        ];
        
        // 6. 回傳成功結果（包含會員資料，讓前端可以直接更新 Pinia）
        echo json_encode([
            "success" => true,
            "message" => "恭喜註冊成功!",
            "member" => $_SESSION['member'],
            "autoLogin" => true
        ], JSON_UNESCAPED_UNICODE);
        
    } else {
        throw new Exception("資料庫插入失敗");
    }
}


// $respBody['success'] = true ;
// $respBody['message'] = '此email未註冊過,註冊成功!' ;
// echo json_encode( $respBody ,JSON_UNESCAPED_UNICODE) ;
