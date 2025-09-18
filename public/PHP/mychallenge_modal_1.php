<?php
    // 導入資料庫連線的資料檔
    include 'conn.php'; 
    session_start();

    //---------------------------------------------------

    if (!isset($_SESSION['member']['id']) || empty($_SESSION['member']['id'])) {
        $response = [
            'success' => true,
        ];

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $MEMBER_ID = $_SESSION['member']['id'];

    $input = json_decode(file_get_contents('php://input'), true);
    
    function sanitizeContent($input) {
        if (!is_string($input)) {
            return '';
        }
        
        // 只移除真正危險的標籤，而不是轉換所有特殊字符
        $dangerous_tags = ['<script', '<iframe', '<object', '<embed', '<form'];
        
        foreach ($dangerous_tags as $tag) {
            $input = str_ireplace($tag, '', $input);
        }
        
        // 限制長度但保持原始字符
        return trim(mb_substr($input, 0, 500, 'UTF-8'));
    }

    $mountain_id = $input['mountain_id'];
    $height = $input['height'];
    $gpx_distance = $input['distance'];
    $duration = $input['duration'];
    $content = sanitizeContent($input['content'] ?? '');  // 可為空字串
    $gpx_coords = $input['gpx_coords'] ?? [];

    // 取得山峰座標
    $mountainSql = "SELECT LATITUDE, LONGITUDE, MOUNTAIN_NAME FROM MOUNTAIN WHERE MOUNTAIN_ID = ?";
    $mountainStmt = $pdo->prepare($mountainSql);
    $mountainStmt->execute([$mountain_id]);
    $mountain = $mountainStmt->fetch(PDO::FETCH_ASSOC);

    if (!$mountain) {
        echo json_encode(['success' => false, 'message' => '找不到山峰資料']);
        exit;
    }

    $isClimbed = false;
    if (!empty($gpx_coords)) {
        foreach ($gpx_coords as $coord) {
            $distance = calculateDistance(
                $mountain['LATITUDE'], 
                $mountain['LONGITUDE'],
                $coord[1], // lat
                $coord[0]  // lon
            );
            
            if ($distance < 0.01) { // 10公尺內才算登頂
                $isClimbed = true;
                break;
            }
        }
    }

    //建立SQL語法
    $sql = "INSERT INTO FOOT(MEMBER_ID, MOUNTAIN_ID, HEIGHT, DISTANCE, DURATION, CONTENT, IS_CLIMBED, UPLOAD_AT) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $statement = $pdo->prepare($sql);
    $result = $statement->execute([$MEMBER_ID, $mountain_id, $height, $gpx_distance, $duration, $content, $isClimbed ? 1 : 0]);
    if ($result) {
        $message = $isClimbed ? '恭喜登頂成功！紀錄已儲存' : '軌跡已記錄，繼續挑戰登頂吧！';
        echo json_encode([
            'success' => true,
            'climbed' => $isClimbed,
            'message' => $message
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => '儲存失敗'
        ]);
    }

    function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $R = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }
?>