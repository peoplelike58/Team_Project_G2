<?php
// upload-avatar.php - 頭像上傳 


// 檢查是否有上傳文件
if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "沒有上傳文件或上傳失敗"
    ), JSON_UNESCAPED_UNICODE);
    exit();
}

// 檢查會員 ID
if (!isset($_POST['member_id']) || empty($_POST['member_id'])) {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "缺少會員 ID"
    ), JSON_UNESCAPED_UNICODE);
    exit();
}

$file = $_FILES['avatar'];
$member_id = $_POST['member_id'];

// 檢查文件大小（限制 2MB）
$max_size = 2 * 1024 * 1024; // 2MB
if ($file['size'] > $max_size) {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "文件大小不能超過 2MB"
    ), JSON_UNESCAPED_UNICODE);
    exit();
}

// 檢查文件類型
$allowed_types = array('image/jpeg', 'image/png', 'image/jpg');
if (!in_array($file['type'], $allowed_types)) {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "只允許上傳 JPG、PNG 格式的圖片"
    ), JSON_UNESCAPED_UNICODE);
    exit();
}

try {
    // 創建上傳目錄（如果不存在）
    $upload_dir = '../uploads/avatars/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // 生成唯一的文件名
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = 'avatar_' . $member_id . '_' . time() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;
    
    // 移動上傳的文件
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        // 文件上傳成功，返回文件名
        http_response_code(200);
        echo json_encode(array(
            "success" => true,
            "message" => "頭像上傳成功",
            "data" => array(
                "filename" => $new_filename,
                "url" => "/uploads/avatars/" . $new_filename
            )
        ), JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(array(
            "success" => false,
            "message" => "文件上傳失敗"
        ), JSON_UNESCAPED_UNICODE);
    }

} catch(Exception $exception) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "上傳錯誤: " . $exception->getMessage()
    ), JSON_UNESCAPED_UNICODE);
}
?>