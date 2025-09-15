<?php

include __DIR__ . '/conn.php';
header("Access-Control-Allow-Methods: POST, OPTIONS");// 允許的請求方法
header("Access-Control-Allow-Headers: Content-Type");// 允許的自訂標頭


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

//後台傳來JSON 轉成php的物件或陣列
$updatproduct = json_decode(file_get_contents("php://input"), true);

$id = $updatproduct['PRODUCT_ID'];  // 主鍵
$sizes = isset($updatproduct['SIZES']) ? $updatproduct['SIZES'] : [];
$colors = isset($updatproduct['COLORS']) ? $updatproduct['COLORS'] : [];

//COALESCE() Function (回傳第一個非空值的值，如果是NULL，就用原本的值)
$sql= "UPDATE PRODUCT 
        SET PRODUCT_NAME=COALESCE(:pname , PRODUCT_NAME),
            PRICE=COALESCE(:price, PRICE),
            IMAGE=COALESCE(:image, IMAGE),
            GENDER=COALESCE(:gender, GENDER),
            DESCRIPTION=COALESCE(:desc, DESCRIPTION),
            PRODUCT_TYPE=COALESCE(:ptype, PRODUCT_TYPE),
            PRODUCT_STATUS=COALESCE(:pstatus, PRODUCT_STATUS)
        WHERE PRODUCT_ID = :id";

    
$pstmt = $pdo->prepare($sql);
//如果不是空字串，就傳值，如果空字串，就轉成null
$pstmt->bindValue(":pname", $updatproduct['PRODUCT_NAME'] !=='' ? $updatproduct['PRODUCT_NAME'] : null);
$pstmt->bindValue(":price", $updatproduct['PRICE']!=='' ? $updatproduct['PRICE'] : null);
$pstmt->bindValue(":image", $updatproduct['IMAGE']!=='' ? $updatproduct['IMAGE'] : null);
$pstmt->bindValue(":gender", $updatproduct['GENDER']!=='' ? $updatproduct['GENDER'] : null);
$pstmt->bindValue(":desc", $updatproduct['DESCRIPTION']!=='' ? $updatproduct['DESCRIPTION'] : null);
$pstmt->bindValue(":ptype", $updatproduct['PRODUCT_TYPE']!=='' ? $updatproduct['PRODUCT_TYPE'] : null);
$pstmt->bindValue(":pstatus", $updatproduct['PRODUCT_STATUS']!=='' ? $updatproduct['PRODUCT_STATUS'] : null);
$pstmt->bindValue(":id", $id);
$ok = $pstmt->execute();


//更新尺寸
$sqlDelSize= 'DELETE FROM `PRODUCT_SIZE` WHERE PRODUCT_ID = :id';
$spstmt = $pdo->prepare($sqlDelSize);
$spstmt->bindValue(":id", $id);
$spstmt->execute();

$sizes = explode(',', $updatproduct['SIZES']);
$sqlsize = "INSERT INTO PRODUCT_SIZE(PRODUCT_ID, SIZE)
            VALUES(:id, :size)";
$sstmt = $pdo->prepare($sqlsize);
foreach($sizes as $size){
$sstmt->execute([
    ':id' =>$id,
    ':size' =>trim($size)
]);
}


//更新顏色
$sqlDelColor= 'DELETE FROM `PRODUCT_COLOR` WHERE PRODUCT_ID = :id';
$cpstmt = $pdo->prepare($sqlDelColor);
$cpstmt->bindValue(":id", $id);
$cpstmt->execute();

$colors = explode(',', $updatproduct['COLORS']);
$sqlcolor = "INSERT INTO PRODUCT_COLOR(PRODUCT_ID, COLOR)
            VALUES(:id, :color)";
$cstmt  = $pdo->prepare($sqlcolor);
foreach($colors as $color){
$cstmt ->execute([
    ':id' =>$id,
    ':color' =>trim($color)
]);
}



if ($ok) {
    echo json_encode(['success' => true, 'message' => '修改成功']);
} else {
    echo json_encode(['success' => false, 'message' => '修改失敗']);
}
