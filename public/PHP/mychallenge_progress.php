<?php
    session_start();

    // 導入資料庫連線的資料檔
    include 'conn.php'; 
    //---------------------------------------------------
    if (!isset($_SESSION['member']['id']) || empty($_SESSION['member']['id'])) {
        $response = [
            'success' => true,
            'data' => [
                'member_id' => null,
                'mountain_types' => [
                    'big_mountain' => [
                        'type' => '大百岳',
                        'count' => 0,
                        'target' => 10,
                    ],
                    'small_mountain' => [
                        'type' => '小百岳',
                        'count' => 0,
                        'target' => 10,
                    ],
                ],
            ],
            'message' => '尚未登入，顯示預設值'
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $MEMBER_ID = $_SESSION['member']['id'];

    $sql = "SELECT M.TYPE,
	               COUNT(DISTINCT F.MOUNTAIN_ID) as count
            FROM FOOT F
            LEFT JOIN MOUNTAIN M
            ON F.MOUNTAIN_ID = M.MOUNTAIN_ID
            WHERE F.MEMBER_ID = ?
            GROUP BY M.TYPE";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$MEMBER_ID]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $big_mountain_count = 0;
    $small_mountain_count = 0;

    foreach ($result as $row) {
        switch ($row['TYPE']) {
            case '大百岳':
                $big_mountain_count = (int)$row['count'];
                break;
            case '小百岳':
                $small_mountain_count = (int)$row['count'];
                break;
        }
    }

    $sql_goal = "SELECT BIG_TARGET, SMALL_TARGET 
                 FROM GOAL
                 WHERE MEMBER_ID = ?";

    $stmt_goal = $pdo->prepare($sql_goal);
    $stmt_goal->execute([$MEMBER_ID]);
    $goal_result = $stmt_goal->fetch(PDO::FETCH_ASSOC);

    $big_target = $goal_result ? (int)$goal_result['BIG_TARGET'] : 10;
    $small_target = $goal_result ? (int)$goal_result['SMALL_TARGET'] : 10;

    $response = [
            'success' => true,
            'data' => [
                'member_id' => $MEMBER_ID,
                'mountain_types' => [
                    'big_mountain' => [
                        'type' => '大百岳',
                        'count' => $big_mountain_count,
                        'target' => $big_target,
                    ],
                    'small_mountain' => [
                        'type' => '小百岳',
                        'count' => $small_mountain_count,
                        'target' => $small_target,
                    ],
                ],
            ],
            'message' => '資料獲取成功'
        ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE);

?>