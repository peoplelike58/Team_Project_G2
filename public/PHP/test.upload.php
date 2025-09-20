<?php
// 測試用的上傳檔案，不包含 conn.php

// 設定 CORS 標頭
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
header("Content-Type: application/json; charset=utf-8");

// 處理預檢請求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 除錯資訊
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("FILES: " . print_r($_FILES, true));

// 檢查是否有檔案上傳
if (!isset($_FILES["file"])) {
    echo json_encode(["success" => false, "message" => "沒有接收到檔案"]);
    exit;
}

//判斷是否上傳成功
if($_FILES["file"]["error"] > 0){
    echo json_encode(["success" => false, "message" => "上傳失敗: 錯誤代碼".$_FILES["file"]["error"]]);
    exit;
} else {
    //取得上傳的檔案資訊
    $fileName = $_FILES["file"]["name"];
    $filePath_Temp = $_FILES["file"]["tmp_name"];
    $fileType = $_FILES["file"]["type"];
    $fileSize = $_FILES["file"]["size"];

    // 根據環境設定不同的上傳路徑
    if ($_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
        // 本地開發環境
        $uploadPath = $_SERVER["DOCUMENT_ROOT"] . "/TeamProject/public/images/Products/products/";
    } else {
        // 正式環境
        $uploadPath = $_SERVER["DOCUMENT_ROOT"] . "/tjd102/g2/images/Products/products/";
    }

    // 除錯資訊
    error_log("Upload Path: " . $uploadPath);
    error_log("Document Root: " . $_SERVER["DOCUMENT_ROOT"]);

    // 確保目錄存在
    if (!is_dir($uploadPath)) {
        if (!mkdir($uploadPath, 0755, true)) {
            echo json_encode([
                "success" => false,
                "message" => "無法建立上傳目錄: " . $uploadPath
            ]);
            exit;
        }
    }

    //檔名
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // 檢查檔案類型
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowedTypes)) {
        echo json_encode([
            "success" => false,
            "message" => "不允許的檔案類型，僅支援: " . implode(', ', $allowedTypes)
        ]);
        exit;
    }

    $newName = "MP". date('Ymd_His') . "." .$ext;
    $filePath = $uploadPath. $newName;

    //將暫存檔搬移到正確位置
    if(move_uploaded_file($filePath_Temp, $filePath)){
        echo json_encode([
            "success" => true,
            "filename" => $newName,
            "type" => $fileType,
            "size" => $fileSize,
            "path" => $uploadPath // 除錯用
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "檔案搬移失敗，請確認資料夾權限與路徑: ".$filePath
        ]);
    }  
}
?>