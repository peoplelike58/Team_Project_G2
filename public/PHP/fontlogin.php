<?php  /* 這是第一次測試可跑的版本 */
$member=json_decode(file_get_contents("php://input"), true);//接收前端來的東西，做json檔的解碼

// //解鎖資料庫（登入的賬號密碼）
// $URL = "mysql:host=localhost:3306;dbname=MountainPeak";
// $USERNAME = "root";
// $PASSWORD = "password";
// $pdo = new PDO($URL, $USERNAME, $PASSWORD);

$USERNAME = "tibamefe_since2021";
$PASSWORD = "vwRBSb.j&K#E";
$URL = "mysql:host=127.0.0.1:3306;dbname=tibamefe_tjd102g2";  //table名記得核對
$pdo = new PDO($URL, $USERNAME, $PASSWORD);

$sql = "
select EMAIL,PW from MEMBER where EMAIL = :email and PW = :password 
";//這裡要記得核對資料庫的欄位名稱

$pstmt = $pdo->prepare($sql);
$pstmt->bindValue( ":email", $member["email"]);
$pstmt->bindValue(":password", $member["password"]);
$pstmt->execute();
$member = $pstmt->fetchAll();

$respBody['success'] = $member != null;
if ($respBody['success']) {
    $_SESSION['member'] = $member;
}

echo json_encode( $respBody ) ;

?>