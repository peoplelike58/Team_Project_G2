<?php

    include __DIR__ . '/conn.php';
    header("Access-Control-Allow-Methods: POST, OPTIONS"); // 允許的請求方法
    header("Access-Control-Allow-Headers: Content-Type"); // 允許的自訂標頭

   
    //判斷是否上傳成功
    if($_FILES["file"]["error"] > 0){
        echo json_encode(["success" => false, "message" => "上傳失敗: 錯誤代碼".$_FILES["file"]["error"]]);
        exit;
    }else{
        //取得上傳的檔案資訊=======================================
        $fileName = $_FILES["file"]["name"];    //檔案名稱含副檔名        
        $filePath_Temp = $_FILES["file"]["tmp_name"];   //Server上的暫存檔路徑含檔名        
        $fileType = $_FILES["file"]["type"];    //檔案種類        
        $fileSize = $_FILES["file"]["size"];    //檔案尺寸
        //=======================================================

        //Web根目錄真實路徑
        $ServerRoot = $_SERVER["DOCUMENT_ROOT"] . "/TeamProject/public";
        $uploadPath = $ServerRoot. "/images/Products/";


      


        //檔名
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newName = "MP". date('Ymd_His') . "." .$ext;
        //檔案最終存放位置
        $filePath = $uploadPath. $newName;

  
        //將暫存檔搬移到正確位置
        if(move_uploaded_file($filePath_Temp, $filePath)){

            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'];
            $basePath = '/TeamProject/public';

            echo json_encode([
                "success" => true,
                "path" => $protocol . $host . $basePath . "/images/Products/" . $newName,  // 完整 URL
                // "path" => "/images/Products/" . $newName,
                "type" =>$fileType,
                "size" =>$fileSize
            ]);
    }
    }


?>
