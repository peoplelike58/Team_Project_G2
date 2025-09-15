<?php
$jsonFile = __DIR__ . '/../json/products/products.json';
$jsonData = file_get_contents($jsonFile);
$json = json_decode($jsonData, true);


include 'conn.php';

/* 匯入product表資料 */
$sql = "INSERT INTO PRODUCT 
        (PRODUCT_ID, PRODUCT_NAME, PRICE, IMAGE, GENDER, DESCRIPTION, PRODUCT_TYPE) 
        VALUES 
        (:id, :name, :price, :image, :gender, :description, :category)
        ON DUPLICATE KEY UPDATE
            PRODUCT_NAME = VALUES(PRODUCT_NAME),
            PRICE = VALUES(PRICE),
            IMAGE = VALUES(IMAGE),
            GENDER = VALUES(GENDER),
            DESCRIPTION = VALUES(DESCRIPTION),
            PRODUCT_TYPE = VALUES(PRODUCT_TYPE)";
$stmt = $pdo->prepare($sql);

$stmtDeColor = $pdo->prepare("DELETE FROM PRODUCT_COLOR WHERE PRODUCT_ID = :id");
$stmtInColor = $pdo->prepare("
  INSERT INTO PRODUCT_COLOR (PRODUCT_ID, COLOR)
  VALUES (:id, :color)
");
$stmtDeSize = $pdo->prepare("DELETE FROM PRODUCT_SIZE WHERE PRODUCT_ID = :id");
$stmtInSize = $pdo->prepare("
  INSERT INTO PRODUCT_SIZE (PRODUCT_ID, SIZE)
  VALUES (:id, :size)
");



$pdo->beginTransaction();

// 逐筆匯入或更新
foreach ($json as $item) {
    $stmt->execute([
        ":id" => $item['id'],
        ":name" => $item['name'],
        ":price" => $item['price'],
        ":image" => $item['image'],
        ":gender" => $item['gender'],
        ":description" => $item['description'],
        ":category" => $item['category']
    ]);

    /* 匯入PRODUCT_COLOR表資料 */
    // 刪掉舊的 color、size
    $stmtDeColor->execute([':id' => $item['id']]);
    $stmtDeSize->execute([':id' => $item['id']]);
    

    // 插入新的 color、size
    if (!empty($item['color']) && is_array($item['color'])) {
        foreach ($item['color'] as $c) {
            $stmtInColor->execute([
                ':id'   => $item['id'],
                ':color' => $c
            ]);
        }
    }
    if (!empty($item['size']) && is_array($item['size'])) {
        foreach ($item['size'] as $s) {
            $stmtInSize->execute([
                ':id'   => $item['id'],
                ':size' => $s
            ]);
        }
    }
}

$pdo->commit();
echo "✅ PRODUCT 與 PRODUCT_COLOR 匯入/更新完成";
echo "匯入完成！";

?>