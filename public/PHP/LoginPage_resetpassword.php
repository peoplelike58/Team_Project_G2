<!-- LoginPage_resetpassword 重置密碼php -->
<?php
include 'conn.php';

//---------------------------------------------------
// 密鑰 - 與前面步驟使用的相同
define('SECRET_KEY', '690313d321d0de67118799a8bff29f867eccb717e0978babbf0720e6ac985e23');

/**
 * 驗證重設密碼token
 */
function verifyResetToken($token) {
    $parts = explode('.', $token);
    
    if (count($parts) !== 2) {
        error_log("重設Token格式錯誤 - 部分數量: " . count($parts));
        return false;
    }
    
    list($encodedPayload, $encodedSignature) = $parts;
    
    // 驗證簽名
    $expectedSignature = hash_hmac('sha256', $encodedPayload, SECRET_KEY);
    $actualSignature = base64url_decode($encodedSignature);
    
    if (!hash_equals($expectedSignature, $actualSignature)) {
        error_log("重設Token簽名驗證失敗");
        return false;
    }
    
    // 解碼payload
    $payload = json_decode(base64url_decode($encodedPayload), true);
    
    if (!$payload) {
        error_log("重設Token Payload解碼失敗");
        return false;
    }
    
    // 檢查必要欄位和用途
    if (!isset($payload['email'], $payload['purpose'], $payload['exp']) || 
        $payload['purpose'] !== 'password_reset') {
        error_log("重設Token欄位檢查失敗");
        return false;
    }
    
    // 檢查是否過期
    if (time() > $payload['exp']) {
        error_log("重設Token已過期");
        return false;
    }
    
    return $payload;
}

/**
 * Base64 URL安全解碼
 */
function base64url_decode($data) {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

try {

    error_log("=== 重設密碼API調用開始 ===");
    
    $data = json_decode(file_get_contents("php://input"), true);

    if ($data === null) {
        $jsonError = json_last_error();
        throw new Exception('JSON解析失敗: ' . json_last_error_msg());
    }

    error_log("接收到的數據: " . json_encode($data));

    // 驗證必要參數
    if (!isset($data['reset_token']) || empty($data['reset_token'])) {
        throw new Exception('缺少重設密碼token');
    }

    if (!isset($data['new_password']) || empty($data['new_password'])) {
        throw new Exception('請提供新密碼');
    }

    $resetToken = $data['reset_token'];
    $newPassword = trim($data['new_password']);

    // 記錄調試資訊
    error_log("收到重設密碼請求 - Token: " . substr($resetToken, 0, 20) . "...");
    error_log("新密碼長度: " . strlen($newPassword));

    // 驗證新密碼強度
    if (strlen($newPassword) < 8) {
        throw new Exception('密碼長度至少需要8個字元');
    }

    // 驗證重設token
    $payload = verifyResetToken($resetToken);

    if (!$payload) {
        error_log("重設Token驗證失敗");
        throw new Exception('重設連結無效或已過期，請重新申請');
    }

    $email = $payload['email'];
    error_log("Token驗證成功 - Email: " . $email);

    // 檢查用戶是否存在
    $checkUserSql = "SELECT MEMBER_ID, EMAIL, PW FROM MEMBER WHERE TRIM(LOWER(EMAIL)) = TRIM(LOWER(?))";
    $checkStmt = $pdo->prepare($checkUserSql);
    $checkStmt->bindValue(1, $email, PDO::PARAM_STR);
    
    if (!$checkStmt->execute()) {
        $errorInfo = $checkStmt->errorInfo();
        error_log("查詢用戶失敗: " . print_r($errorInfo, true));
        throw new Exception('查詢用戶失敗，請稍後再試');
    }
    
    $user = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        error_log("用戶不存在 - Email: " . $email);
        
        $debugSql = "SELECT EMAIL FROM MEMBER";
        $debugStmt = $pdo->prepare($debugSql);
        $debugStmt->execute();
        $allEmails = $debugStmt->fetchAll(PDO::FETCH_COLUMN);
        error_log("資料庫中的所有Email: " . json_encode($allEmails));
        error_log("嘗試查找的Email: '" . $email . "'");
        
        throw new Exception('用戶不存在');
    }

    error_log("找到用戶 - ID: " . $user['MEMBER_ID']);

    $hashedPassword = md5($newPassword);
    
    // 檢查新舊密碼是否相同
    if (password_verify($newPassword, $user['PW'])) {
        throw new Exception('新密碼不能與舊密碼相同');
    }
    
    // 更新密碼 - 直接儲存，與登入時的比對方式一致
    $updateSql = "UPDATE MEMBER SET PW = ? WHERE MEMBER_ID = ?";
    $updateStmt = $pdo->prepare($updateSql);
    
    // 根據你的資料庫欄位類型選擇
    $updateStmt->bindValue(1, $hashedPassword, PDO::PARAM_STR);
    $updateStmt->bindValue(2, $user['MEMBER_ID'], PDO::PARAM_INT);

    if ($updateStmt->execute()) {
        $affectedRows = $updateStmt->rowCount();
        error_log("SQL執行成功，影響行數: " . $affectedRows);
        
        if ($affectedRows > 0) {
            $verifySql = "SELECT PW FROM MEMBER WHERE MEMBER_ID = ?";
            $verifyStmt = $pdo->prepare($verifySql);
            $verifyStmt->bindValue(1, $user['MEMBER_ID'], PDO::PARAM_INT);
            $verifyStmt->execute();
            $updatedUser = $verifyStmt->fetch(PDO::FETCH_ASSOC);
            
            $respBody = [
                'success' => true,
                'message' => '密碼重設成功，請使用新密碼登入',
                'debug' => [
                    'email' => $user['EMAIL'],
                    'affected_rows' => $affectedRows,
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ];

            error_log("密碼重設成功 - Email: " . $email . ", 影響行數: " . $affectedRows);
        } else {
            
            error_log("UPDATE沒有影響任何行");
            throw new Exception('密碼更新失敗，請稍後再試');
        }
    } else {
        $errorInfo = $updateStmt->errorInfo();
        error_log("SQL執行失敗: " . print_r($errorInfo, true));
        throw new Exception('密碼更新失敗: ' . $errorInfo[2]);
    }


} catch (Exception $e) {
    error_log("重設密碼錯誤: " . $e->getMessage());
    
    $respBody = [
        'success' => false,
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ];
}

error_log("返回響應: " . json_encode($respBody));
echo json_encode($respBody, JSON_UNESCAPED_UNICODE);


?>