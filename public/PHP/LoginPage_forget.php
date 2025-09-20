<?php
include 'conn.php';

require_once __DIR__ . '/../../libs/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../../libs/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../libs/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//---------------------------------------------------

try{


    // 密鑰 - 出社會後的專案使用時請改成強密鑰，並存在環境變數中
    define('SECRET_KEY', '690313d321d0de67118799a8bff29f867eccb717e0978babbf0720e6ac985e23');
    
    $data = json_decode(file_get_contents("php://input"), true);//接收前端來的東西，做json檔的解碼
    
    // 驗證email是否存在
    if (!isset($data['email']) || empty($data['email'])) {
        throw new Exception('請提供電子郵件地址');
    }
    
    $email = trim($data['email']);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('請提供有效的電子郵件格式');
    }
    
    $sql = "SELECT EMAIL, NAME, MEMBER_ID
            from MEMBER
            where EMAIL = ?
    ";
    
    $pstmt = $pdo->prepare($sql);
    $pstmt->bindValue(1, $email, PDO::PARAM_STR);
    $pstmt->execute();
    
    $member = $pstmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$member) {
            // 用戶不存在
            $respBody = [
                'success' => false,
                'message' => '如果該電子郵件已註冊，您將收到重設密碼連結'];
        } else {
    
            // 生成6位數驗證碼
            $verificationCode = sprintf('%06d', mt_rand(0, 999999));
            
            // 創建包含驗證碼的token（用於驗證階段）
            $verifyToken = createVerificationToken($email, $verificationCode);
    
            // 發送驗證碼郵件
            if (sendVerificationCodeEmail($email, $member['NAME'], $verificationCode)) {
                $respBody = [
                    'success' => true,
                    'message' => '驗證碼已發送到您的信箱，請在10分鐘內輸入',
                    'verify_token' => $verifyToken // 返回給前端，用於下一步驗證
                ];
            } else {
                throw new Exception('郵件發送失敗，請稍後再試');
            }
        }
}catch (PDOException $e) {
    // 資料庫錯誤
    error_log("資料庫錯誤: " . $e->getMessage());
    $respBody = [
        'success' => false,
        'message' => '資料庫連接錯誤，請稍後再試'
    ];
} catch (Exception $e) {
    // 其他錯誤
    error_log("一般錯誤: " . $e->getMessage());
    $respBody = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($respBody, JSON_UNESCAPED_UNICODE);


/**
 * 創建重設密碼的簽名token
 */
function createVerificationToken($email, $verificationCode) {
    $payload = [
        'email' => $email,
        'code' => $verificationCode,
        'purpose' => 'code_verification',
        'exp' => time() + 600, // 10分鐘有效期
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
 * 使用PHPMailer發送驗證碼郵件
 */
function sendVerificationCodeEmail($email, $name, $code) {
    try {
        $mail = new PHPMailer(true);
        
        // 伺服器設定
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'shanshangjian28560@gmail.com';
        $mail->Password   = 'vzpsmdhrrmqzsbnq';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        
        // 寄件人設定
        $mail->setFrom('shanshangjian28560@gmail.com', '山上見');
        $mail->addAddress($email, $name);
        
        // 郵件內容
        $mail->isHTML(false);
        $mail->Subject = '密碼重設驗證碼';
        $mail->Body    = "
親愛的 {$name}，

您的密碼重設驗證碼是：{$code}

此驗證碼將在10分鐘後失效。

請在重設密碼頁面輸入此驗證碼以繼續操作。

如果您沒有請求重設密碼，請忽略此郵件。
        ";
        
        $mail->send();
        error_log("郵件發送成功到: " . $email);
        return true;
        
    } catch (Exception $e) {
        error_log("郵件發送失敗: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * 測試版本
 */
// function sendVerificationCodeEmail($email, $name, $code) {
//     // 測試模式：記錄到檔案
//     file_put_contents('verification_codes.log', 
//         date('Y-m-d H:i:s') . " - {$email}: {$code}\n", FILE_APPEND);
//     error_log("模擬發送驗證碼 {$code} 到 {$email}");
//     return true; // 總是返回成功

//     $subject = '密碼重設驗證碼';
//     $message = "親愛的 {$name}，

// 您的密碼重設驗證碼是：{$code}

// 此驗證碼將在10分鐘後失效。

// 請在重設密碼頁面輸入此驗證碼以繼續操作。

// 如果您沒有請求重設密碼，請忽略此郵件。";
    
//     $headers = [
//         'From: noreply@tibamef2e.com',
//         'Reply-To: noreply@tibamef2e.com',
//         'Content-Type: text/plain; charset=UTF-8'
//     ];
    
//     if (mail($email, $subject, $message, implode("\r\n", $headers))) {
//         error_log("郵件發送成功到: " . $email);
//         return true;
//     } else {
//         error_log("郵件發送失敗到: " . $email);
//         return false;
//     }

?>