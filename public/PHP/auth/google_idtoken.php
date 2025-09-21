<?php
ini_set('display_errors','0');
ini_set('log_errors','1');
ini_set('error_log', __DIR__ . '/../debug.log');

header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../conn.php';

// 讀 body
$input   = json_decode(file_get_contents('php://input'), true) ?: [];
$idToken = $input['idToken'] ?? '';
if (!$idToken) {
  http_response_code(400);
  echo json_encode(['ok'=>false,'step'=>'input','error'=>'MISSING_TOKEN'], JSON_UNESCAPED_UNICODE);
  exit;
}

// 讀 Client ID（同層）
$config   = require __DIR__ . '/config.php';
$clientId = $config['GOOGLE_CLIENT_ID'] ?? '';
if (!$clientId) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'step'=>'config','error'=>'MISSING_GOOGLE_CLIENT_ID']);
  exit;
}

// 驗證 Token
$verifyUrl = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken);
$resp     = @file_get_contents($verifyUrl);
$payload  = $resp ? json_decode($resp, true) : null;

if (!$payload || ($payload['aud'] ?? '') !== $clientId) {
  http_response_code(401);
  echo json_encode(['ok'=>false,'step'=>'verify_token','error'=>'INVALID_TOKEN_OR_AUD_MISMATCH']);
  exit;
}

$email  = $payload['email']   ?? '';
$name   = $payload['name']    ?? '';
$avatar = $payload['picture'] ?? null;

// upsert by EMAIL
$stmt = $pdo->prepare('SELECT * FROM MEMBER WHERE EMAIL=? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  $stmt = $pdo->prepare('INSERT INTO MEMBER (EMAIL, NAME, IMAGE, STATUS) VALUES (?, ?, ?, ?)');
  $stmt->execute([$email, $name, $avatar, '啟用']);
  $id = $pdo->lastInsertId();
  $user = [
    'MEMBER_ID' => $id,
    'EMAIL'     => $email,
    'NAME'      => $name,
    'IMAGE'     => $avatar,
    'STATUS'    => '啟用'
  ];
}

// 寫 Session
$_SESSION['user'] = $user;

echo json_encode(['ok'=>true, 'member'=>$user], JSON_UNESCAPED_UNICODE);
exit;