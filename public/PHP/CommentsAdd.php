<?php

header('Access-Control-Allow-Credentials: true');
header('Vary: Origin'); // 避免快取混淆

// 處理預檢請求（OPTIONS）
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
  http_response_code(204);
  exit;
}
// ===== Session：確認登入 =====
session_start();
$memberId = 0;
// 兩種常見 key 都支援：請依你的登入程式實際寫入的 key 為準
if (isset($_SESSION['member']['id']))        $memberId = intval($_SESSION['member']['id']);
if (isset($_SESSION['member']['MEMBER_ID'])) $memberId = intval($_SESSION['member']['MEMBER_ID']);
if ($memberId <= 0) {
  echo json_encode(['success'=>false, 'message'=>'尚未登入']); exit;
}

// ===== 讀取輸入（同時支援 multipart/form-data 與 application/json）=====
$ct = $_SERVER['CONTENT_TYPE'] ?? '';
$MOUNTAIN_ID = 0;
$CONTENT     = '';
$imageKey    = null;   // 最後要寫進 DB 的「相對路徑」，例如 comments/2025/09/xxx.jpg

if (stripos($ct, 'application/json') !== false) {
  // --- JSON 版本 ---
  $payload = json_decode(file_get_contents("php://input"), true) ?? [];
  // 允許大小寫兩種鍵名（你前端目前是小寫）
  $MOUNTAIN_ID = intval($payload['mountain_id'] ?? $payload['MOUNTAIN_ID'] ?? 0);
  $CONTENT     = trim($payload['content'] ?? $payload['CONTENT'] ?? '');

  // 若有 base64 圖片（鍵名：image 或 IMAGE），就解碼另存檔案
  $imageBase64 = $payload['image'] ?? $payload['IMAGE'] ?? null;
  if (is_string($imageBase64) && $imageBase64 !== '') {
    // 允許 data URL，先拆 MIME/資料
    $mime = 'image/jpeg'; $data = $imageBase64;
    if (strpos($imageBase64, ',') !== false) {
      [$head, $data] = explode(',', $imageBase64, 2);
      if (preg_match('#data:(.*?);base64#i', $head, $m)) { $mime = strtolower(trim($m[1])); }
    }
    $bin = base64_decode($data);

    // 依 MIME 決定副檔名
    $extMap = ['image/jpeg'=>'jpg','image/jpg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    $ext = $extMap[$mime] ?? 'jpg';

    // 準備儲存路徑
    $UPLOAD_BASE_FS  = __DIR__ . '/../uploads';            // 實體根目錄（public\uploads）
    $subDir = 'comments/' . date('Y/m');                   // comments/2025/09
    $unique = bin2hex(random_bytes(8));
    $imageKey = $subDir . '/' . $unique . '.' . $ext;      // 存 DB 的相對路徑 key
    $absPath  = $UPLOAD_BASE_FS . '/' . $imageKey;

    // 建資料夾並寫檔
    if (!is_dir(dirname($absPath))) { @mkdir(dirname($absPath), 0777, true); }
    if ($bin === false || file_put_contents($absPath, $bin) === false) {
      echo json_encode(['success'=>false,'message'=>'圖片儲存失敗']); exit;
    }
  }

} else {
  // --- multipart/form-data 版本（推薦） ---
  $MOUNTAIN_ID = intval($_POST['mountain_id'] ?? $_POST['MOUNTAIN_ID'] ?? 0);
  $CONTENT     = trim($_POST['content'] ?? $_POST['CONTENT'] ?? '');

  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $orig = $_FILES['image']['name'];
    $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    $allow = ['jpg','jpeg','png','webp'];
    if (!in_array($ext, $allow)) {
      echo json_encode(['success'=>false,'message'=>'圖片格式僅支援 jpg/jpeg/png/webp']); exit;
    }
    $UPLOAD_BASE_FS  = __DIR__ . '/../uploads';
    $subDir = 'comments/' . date('Y/m');
    $unique = bin2hex(random_bytes(8));
    $imageKey = $subDir . '/' . $unique . '.' . $ext;
    $absPath  = $UPLOAD_BASE_FS . '/' . $imageKey;

    if (!is_dir(dirname($absPath))) { @mkdir(dirname($absPath), 0777, true); }
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $absPath)) {
      echo json_encode(['success'=>false,'message'=>'上傳失敗：無法寫入檔案']); exit;
    }
  }
}

// 檢查必要欄位
if ($MOUNTAIN_ID <= 0 || $CONTENT === '') {
  echo json_encode(['success'=>false,'message'=>'參數不完整']); exit;
}

// ===== DB 連線（用你的 conn.php）=====
require __DIR__ . '/conn.php'; // 需提供 $pdo（PDO 物件）與正確的 charset

try {
  // ===== 寫入 MESSAGE =====
  // MESSAGE(MESSAGE_ID, MEMBER_ID, CONTENT, CREATED_AT, IMAGE, MOUNTAIN_ID)
  $sql = "INSERT INTO MESSAGE (MOUNTAIN_ID, MEMBER_ID, CONTENT, IMAGE)
          VALUES (:mountain_id, :member_id, :content, :image)";
  $pstmt = $pdo->prepare($sql);
  $pstmt->bindValue(':mountain_id', $MOUNTAIN_ID, PDO::PARAM_INT);
  $pstmt->bindValue(':member_id',  $memberId,    PDO::PARAM_INT);
  $pstmt->bindValue(':content',    $CONTENT,     PDO::PARAM_STR);
  if ($imageKey === null || $imageKey === '') {
    $pstmt->bindValue(':image', null, PDO::PARAM_NULL);
  } else {
    $pstmt->bindValue(':image', $imageKey, PDO::PARAM_STR); // 存相對路徑
  }
  $pstmt->execute();

  $newId = intval($pdo->lastInsertId());

  // ===== 回查剛新增那筆（JOIN MEMBER），避免 IMAGE 欄位衝突用別名 =====
  $sql2 = "SELECT
             msg.MESSAGE_ID,
             msg.MOUNTAIN_ID,
             msg.CONTENT,
             msg.CREATED_AT,
             msg.IMAGE        AS MESSAGE_IMAGE,
             m.MEMBER_ID,
             m.NICKNAME,
             m.IMAGE          AS MEMBER_IMAGE
           FROM MESSAGE msg
           JOIN MEMBER  m ON m.MEMBER_ID = msg.MEMBER_ID
           WHERE msg.MESSAGE_ID = :id";
  $p2 = $pdo->prepare($sql2);
  $p2->bindValue(':id', $newId, PDO::PARAM_INT);
  $p2->execute();
  $row = $p2->fetch();

  // 直接幫你組一個可用的 URL（避免前端組錯）
  $UPLOAD_BASE_URL = '/TeamProject/public/uploads';
  $row['MESSAGE_IMAGE_URL'] = $row['MESSAGE_IMAGE'] ? ($UPLOAD_BASE_URL . '/' . $row['MESSAGE_IMAGE']) : null;

  echo json_encode(['success'=>true,'message'=>'新增留言成功','data'=>$row]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
