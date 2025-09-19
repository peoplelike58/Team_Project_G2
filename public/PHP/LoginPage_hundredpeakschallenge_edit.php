<?php
    include 'conn.php'; 
    session_start();

    //---------------------------------------------------


    // 檢查是否已登入
    if (!isset($_SESSION['member']['id']) || empty($_SESSION['member']['id'])) {
        echo json_encode([
            'success' => false, 
            'message' => '請先登入'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $MEMBER_ID = $_SESSION['member']['id'];
    
    // 取得請求方法
    $method = $_SERVER['REQUEST_METHOD'];
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if ($method === 'POST' && ($input['action'] ?? '') === 'edit') {
        // 編輯想法
        
        $foot_id = $input['id'] ?? null;
        $new_content = $input['content'] ?? '';
        
        if (!$foot_id) {
            echo json_encode([
                'success' => false, 
                'message' => '缺少記錄ID'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 清理內容
        function sanitizeContent($input) {
            if (!is_string($input)) {
                return '';
            }
            
            $dangerous_tags = ['<script', '<iframe', '<object', '<embed', '<form'];
            
            foreach ($dangerous_tags as $tag) {
                $input = str_ireplace($tag, '', $input);
            }
            
            return trim(mb_substr($input, 0, 500, 'UTF-8'));
        }

        $clean_content = sanitizeContent($new_content);

        // 驗證該記錄是否屬於當前用戶
        $checkSql = "SELECT ID FROM FOOT WHERE ID = ? AND MEMBER_ID = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$foot_id, $MEMBER_ID]);
        
        if (!$checkStmt->fetch()) {
            echo json_encode([
                'success' => false, 
                'message' => '無權限編輯此記錄'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 更新
        $updateSql = "UPDATE FOOT 
                      SET CONTENT = ? 
                      WHERE ID = ? 
                      AND MEMBER_ID = ?";
        $updateStmt = $pdo->prepare($updateSql);
        $result = $updateStmt->execute([$clean_content, $foot_id, $MEMBER_ID]);

        if ($result) {
            echo json_encode([
                'success' => true, 
                'message' => '想法更新成功'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => '更新失敗'
            ], JSON_UNESCAPED_UNICODE);
        }

    } else {
        echo json_encode([
            'success' => false, 
            'message' => '不支援的請求方法'
        ], JSON_UNESCAPED_UNICODE);
    }
?>