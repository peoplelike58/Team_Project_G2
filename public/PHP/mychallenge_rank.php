<?php
    // 導入資料庫連線的資料檔
    include 'conn.php'; 

    //---------------------------------------------------

    //建立SQL語法
    $sql = "SELECT MB.MEMBER_ID,
                   COALESCE(NULLIF(MB.NICKNAME, ''), MB.NAME) AS name,
                   COALESCE(MB.IMAGE, '')          AS image,
                   COUNT(DISTINCT F.MOUNTAIN_ID)   AS climb_count,
                   SUM(F.HEIGHT)                   AS height,
                   SUM(F.DISTANCE)                 AS kilo,
                   SUM(F.DURATION)                 AS time,
                   COUNT(DISTINCT CASE WHEN MT.TYPE='大百岳' AND F.IS_CLIMBED = 1 THEN F.MOUNTAIN_ID END) AS big,
                   COUNT(DISTINCT CASE WHEN MT.TYPE='小百岳' AND F.IS_CLIMBED = 1 THEN F.MOUNTAIN_ID END) AS small
                   FROM FOOT F
                   JOIN MOUNTAIN MT ON MT.MOUNTAIN_ID = F.MOUNTAIN_ID
                   JOIN MEMBER  MB ON MB.MEMBER_ID    = F.MEMBER_ID
                   WHERE F.IS_CLIMBED = 1
                   GROUP BY MB.MEMBER_ID
                   ORDER BY climb_count DESC, height DESC, kilo DESC, time DESC
                   LIMIT 5;";

    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 計算需要補充的空資料數量
    $realDataCount = count($rows);
    $needEmptyCount = 5 - $realDataCount;

    // 如果沒有資料時的空排行榜，補滿5筆
    for ($i = 0; $i < $needEmptyCount; $i++) {
        $rows[] = [
            'MEMBER_ID' => null,
            'name' => '等待勇士',
            'image' => 'default-avatar.png',
            'climb_count' => 0,
            'height' => 0,
            'kilo' => 0,
            'time' => 0,
            'big' => 0,
            'small' => 0
        ];
    }

    echo json_encode($rows);

?>