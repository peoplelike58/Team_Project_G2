<?php
include 'conn.php';


//---------------------------------------------------

// 密鑰 - 與發送驗證碼時使用的相同
    define('SECRET_KEY', '690313d321d0de67118799a8bff29f867eccb717e0978babbf0720e6ac985e23');

    $data = json_decode(file_get_contents("php://input"), true);

    // 驗證必要參數
    if (!isset($data['verify_token']) || empty($data['verify_token'])) {
        throw new Exception('缺少驗證token');
    }

    if (!isset($data['verification_code']) || empty($data['verification_code'])) {
        throw new Exception('請輸入驗證碼');
    }

    $verifyToken = $data['verify_token'];
    $inputCode = trim($data['verification_code']);

    // 驗證驗證碼格式（6位數字）
    if (!preg_match('/^\d{6}$/', $inputCode)) {
        throw new Exception('驗證碼格式錯誤，請輸入6位數字');
    }

    // 驗證token並取得資料
    $payload = verifyToken($verifyToken);

    if (!$payload) {
        throw new Exception('驗證連結無效或已過期，請重新申請');
    }

    // 檢查驗證碼是否正確
    if ($payload['code'] !== $inputCode) {
        throw new Exception('驗證碼錯誤，請重新輸入');
    }

    // 驗證成功，生成重設密碼的token
    $resetToken = createPasswordResetToken($payload['email']);

    $respBody = [
        'success' => true,
        'message' => '驗證碼驗證成功',
        'reset_token' => $resetToken,
        'email' => $payload['email']
    ];

    echo json_encode($respBody, JSON_UNESCAPED_UNICODE);


    /**
     * 驗證token
     */
    function verifyToken($token) {
        $parts = explode('.', $token);
        
        if (count($parts) !== 2) {
            return false;
        }
        
        list($encodedPayload, $encodedSignature) = $parts;
        
        // 驗證簽名
        $expectedSignature = hash_hmac('sha256', $encodedPayload, SECRET_KEY);
        $actualSignature = base64url_decode($encodedSignature);
        
        if (!hash_equals($expectedSignature, $actualSignature)) {
            return false;
        }
        
        // 解碼payload
        $payload = json_decode(base64url_decode($encodedPayload), true);
        
        if (!$payload) {
            return false;
        }
        
        // 檢查必要欄位和用途
        if (!isset($payload['email'], $payload['code'], $payload['purpose'], $payload['exp']) || 
            $payload['purpose'] !== 'code_verification') {
            return false;
        }
        
        // 檢查是否過期
        if (time() > $payload['exp']) {
            return false;
        }
        
        return $payload;
    }

    /**
     * 創建重設密碼的token
     */
    function createPasswordResetToken($email) {
        $payload = [
            'email' => $email,
            'purpose' => 'password_reset',
            'exp' => time() + 1800, // 30分鐘有效期
            'iat' => time()
        ];
        
        // 編碼payload
        $encodedPayload = base64url_encode(json_encode($payload));
        
        // 創建簽名
        $signature = hash_hmac('sha256', $encodedPayload, SECRET_KEY);
        $encodedSignature = base64url_encode($signature);
        
        return $encodedPayload . '.' . $encodedSignature;
    }

    /**
     * Base64 URL安全編碼
     */
    function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL安全解碼
     */
    function base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

?>