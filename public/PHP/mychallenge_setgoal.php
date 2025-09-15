<?php
    session_start();

    // 導入資料庫連線的資料檔
    include 'conn.php'; 
    //---------------------------------------------------

    $MEMBER_ID = $_SESSION["memberID"] = "1";


    if (isset($input['BIG_TARGET']) || isset($input['SMALL_TARGET'])) {
        
        $sql_select = "SELECT BIG_TARGET, SMALL_TARGET 
                       FROM GOAL
                       WHERE MEMBER_ID = ?";
        $stmt_select = $pdo->prepare($sql_select);
        $stmt_select->execute([$MEMBER_ID]);
        $existing = $stmt_select->fetch(PDO::FETCH_ASSOC);
        
        $BIG_TARGET = $input['BIG_TARGET'] ?? ($existing['BIG_TARGET'] ?? '');;
        $SMALL_TARGET = $input['SMALL_TARGET'] ?? ($existing['SMALL_TARGET'] ?? '');

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