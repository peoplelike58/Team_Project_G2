<?php

include __DIR__ . '/conn.php';
header("Access-Control-Allow-Methods: POST, OPTIONS");// 允許的請求方法
header("Access-Control-Allow-Headers: Content-Type");// 允許的自訂標頭


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

//後台傳來JSON 轉成php的物件或陣列
$addproduct = json_decode(file_get_contents("php://input"), true);

if (empty($addproduct)) {
    echo json_encode(['success' => false, 'message' => '沒有接收到資料']);
    exit;
}


$sql= "INSERT INTO PRODUCT (PRODUCT_NAME, PRICE, GENDER, DESCRIPTION, PRODUCT_TYPE,PRODUCT_STATUS,IMAGE)
        VALUES(:pname, :price, :gender, :desc, :ptype, :pstatus, :image)";
    
$pstmt = $pdo->prepare($sql);
$pstmt->bindValue(":pname", $addproduct['PRODUCT_NAME']);
$pstmt->bindValue(":price", $addproduct['PRICE']);
$pstmt->bindValue(":gender", $addproduct['GENDER']);
$pstmt->bindValue(":desc", $addproduct['DESCRIPTION']);
$pstmt->bindValue(":ptype", $addproduct['PRODUCT_TYPE']);
$pstmt->bindValue(":pstatus", $addproduct['PRODUCT_STATUS']);
$pstmt->bindValue(":image", !empty($addproduct['IMAGE'])? $addproduct['IMAGE'] : null);

$ok = $pstmt->execute();

if ($ok && $pstmt->rowCount() > 0) {
    echo json_encode(['success' => true, 'message' => '新增成功']);
} else {
    echo json_encode(['success' => false, 'message' => '新增失敗']);
}


// 取得新增的 PRODUCT_ID
$productId = $pdo->lastInsertId();

//insert 尺寸
$sizes = explode(',', $addproduct['SIZES']);
$sqlsize = "INSERT INTO PRODUCT_SIZE(PRODUCT_ID, SIZE)
            VALUES(:pid, :size)";
$sstmt = $pdo->prepare($sqlsize);
foreach($sizes as $size)
   $sstmt->execute([
        ':pid' =>$productId,
        ':size' =>trim($size)
   ]);

//insert COLOR
$colors = explode(',', $addproduct['COLORS']);
$sqlcolor = "INSERT INTO PRODUCT_COLOR(PRODUCT_ID, COLOR)
            VALUES(:pid, :color)";
$cstmt = $pdo->prepare($sqlcolor);
foreach($colors as $color)
   $cstmt->execute([
        ':pid' =>$productId,
        ':color' =>trim($color)
   ]);