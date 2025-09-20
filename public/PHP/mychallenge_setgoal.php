<?php
    // 導入資料庫連線的資料檔
    include 'conn.php'; 
    
    session_start();

    //---------------------------------------------------
    if (!isset($_SESSION['member']['id']) || empty($_SESSION['member']['id'])) {
        $response = [
            'success' => false,
            'message' => '尚未登入',
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $MEMBER_ID = $_SESSION['member']['id'];

    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['BIG_TARGET']) || isset($input['SMALL_TARGET'])) {
        
        $sql_select = "SELECT BIG_TARGET, SMALL_TARGET 
                       FROM GOAL
                       WHERE MEMBER_ID = ?";
        $stmt_select = $pdo->prepare($sql_select);
        $stmt_select->execute([$MEMBER_ID]);
        $existing = $stmt_select->fetch(PDO::FETCH_ASSOC);
        
        // 預設值
        $defaultTarget = 10;

        if (isset($input['BIG_TARGET'])) {
            // 設定大百岳目標
            $BIG_TARGET = (int)$input['BIG_TARGET'];
            $SMALL_TARGET = $existing ? (int)$existing['SMALL_TARGET'] : $defaultTarget;
        } else {
            // 設定小百岳目標  
            $BIG_TARGET = $existing ? (int)$existing['BIG_TARGET'] : $defaultTarget;
            $SMALL_TARGET = (int)$input['SMALL_TARGET'];
        }

        // 讀每個分類的目標與完成數
        $sql = "INSERT INTO GOAL(MEMBER_ID, BIG_TARGET, SMALL_TARGET)
            VALUES( ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                BIG_TARGET = VALUES(BIG_TARGET),
                SMALL_TARGET = VALUES(SMALL_TARGET)";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$MEMBER_ID, $BIG_TARGET, $SMALL_TARGET]);
        
        if ($result) {
            $response = [
                'success' => true,
                'message' => '目標設定成功',
                'data' => [
                    'member_id' => $MEMBER_ID,
                    'big_target' => $BIG_TARGET,
                    'small_target' => $SMALL_TARGET
                    ]
                ];
            } else {
                $response = [
                'success' => false,
                'message' => '資料庫操作失敗'
                ];
            }
            
        } else {
            $response = [
                'success' => true,
                'message' => '維持現有設定'
            ];
        }

    echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>