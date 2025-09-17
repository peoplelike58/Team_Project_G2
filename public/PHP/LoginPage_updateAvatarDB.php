<?php
// LoginPage_updateAvatarDB.php - 專門處理資料庫頭像檔名更新

include 'conn.php';  // 引入資料庫連接檔案

$data=json_decode(file_get_contents("php://input"), true);

try {

    // 檢查 JSON 解析是否成功
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "JSON 格式錯誤"
        ), JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    // 🔧 檢查必要參數
    if (!isset($data['member_id']) || !isset($data['avatar_filename'])) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "缺少必要參數：member_id 或 avatar_filename"
        ), JSON_UNESCAPED_UNICODE);
        exit();
    }

    session_start();
    if (!isset($_SESSION['member']) || !isset($_SESSION['member']['id'])) {
        echo json_encode(['success' => false, 'message' => '尚未登入'],JSON_UNESCAPED_UNICODE); exit;
    }
    $memberId = $_SESSION['member']['id']; // 從 Session 拿會員ID
    

    $avatar_filename = trim($data['avatar_filename']); // 去除空白
    // 🔧 基本驗證
    if ($memberId <= 0) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "會員 ID 無效"
        ), JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    if (empty($avatar_filename)) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "頭像檔名不能為空"
        ), JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    // 檢查檔案是否真的存在
    $file_path = '../uploads/avatars/' . $avatar_filename;
    if (!file_exists($file_path)) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "指定的頭像檔案不存在"
        ), JSON_UNESCAPED_UNICODE);
        exit();
    }
    

    $sql = "UPDATE MEMBER SET IMAGE = :avatar_filename WHERE MEMBER_ID = :member_id";
    $stmt = $pdo->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("SQL 準備失敗: " . $pdo->error);
    }
    
    //  綁定參數並執行
    $stmt->bindParam(':avatar_filename', $avatar_filename, PDO::PARAM_STR);
    $stmt->bindParam(':member_id', $memberId, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        //  檢查是否真的有更新到資料
        if ($stmt) {
            // 成功更新
            http_response_code(200);
            echo json_encode(array(
                "success" => true,
                "message" => "頭像檔名更新成功",
                "data" => array(
                    "member_id" => $memberId,
                    "avatar_filename" => $avatar_filename
                )
            ), JSON_UNESCAPED_UNICODE);
        } else {
            // SQL 執行成功但沒有更新任何資料
            http_response_code(404);
            echo json_encode(array(
                "success" => false,
                "message" => "找不到指定的會員或資料未變更"
            ), JSON_UNESCAPED_UNICODE);
        }
    } else {
        // SQL 執行失敗
        throw new Exception("資料庫更新失敗: " . $stmt->error);
    }
    
    
} catch (Exception $e) {
    // 錯誤處理
    error_log("更新頭像資料庫錯誤: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "系統發生錯誤",
        "error" => $e->getMessage()  // 🔧 開發時可以顯示詳細錯誤，生產時建議移除
    ), JSON_UNESCAPED_UNICODE);
}


?>