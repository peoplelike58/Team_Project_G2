<?php

include __DIR__ . '/conn.php';
header("Access-Control-Allow-Origin: *");


    //建立SQL語法
    $sql = "SELECT * FROM NEWS
                ORDER BY UPLOAD_AT DESC";

    //執行並查詢，會回傳查詢結果的物件，必須使用fetch、fetchAll...等方式取得資料
    $statement = $pdo->query($sql);

    //抓出全部且依照順序封裝成一個二維陣列
    //PDO::FETCH_ASSOC 只抓欄位名索引
    $data = $statement->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data, JSON_UNESCAPED_UNICODE);