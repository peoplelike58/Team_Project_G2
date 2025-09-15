<?php

include __DIR__ . '/conn.php';
header("Access-Control-Allow-Origin: *");


    //建立SQL語法
    $sql = "SELECT 
                p.PRODUCT_ID,
                p.PRODUCT_NAME,
                p.IMAGE,
                p.PRICE,
                p.GENDER,
                p.DESCRIPTION,
                p.PRODUCT_TYPE,
                p.PRODUCT_STATUS,
                GROUP_CONCAT(DISTINCT c.COLOR ORDER BY c.COLOR SEPARATOR '/')AS COLORS,
                GROUP_CONCAT(DISTINCT s.SIZE ORDER BY s.SIZE SEPARATOR '/')AS SIZES
            FROM PRODUCT p
            LEFT JOIN PRODUCT_COLOR c ON p.PRODUCT_ID = c.PRODUCT_ID
            LEFT JOIN PRODUCT_SIZE s ON p.PRODUCT_ID = s.PRODUCT_ID
            GROUP BY p.PRODUCT_ID;
            ";


    //執行並查詢，會回傳查詢結果的物件，必須使用fetch、fetchAll...等方式取得資料
    $statement = $pdo->query($sql);

    //抓出全部且依照順序封裝成一個二維陣列
    $data = $statement->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data, JSON_UNESCAPED_UNICODE);


