<?php
    // 導入資料庫連線的資料檔
    include 'conn.php'; 

    //---------------------------------------------------

    //建立SQL語法
    $sql = "SELECT MB.MEMBER_ID,
                   MB.NICKNAME                     AS name,
                   COALESCE(MB.IMAGE, '')          AS image,
                   COUNT(DISTINCT F.MOUNTAIN_ID)   AS climb_count,
                   SUM(F.HEIGHT)                   AS height,
                   SUM(F.DISTANCE)                 AS kilo,
                   SUM(F.DURATION)                 AS time,
                   COUNT(DISTINCT CASE WHEN MT.TYPE='大百岳' THEN F.MOUNTAIN_ID END) AS big,
                   COUNT(DISTINCT CASE WHEN MT.TYPE='小百岳' THEN F.MOUNTAIN_ID END) AS small
                   FROM FOOT F
                   JOIN MOUNTAIN MT ON MT.MOUNTAIN_ID = F.MOUNTAIN_ID
                   JOIN MEMBER  MB ON MB.MEMBER_ID    = F.MEMBER_ID
                   GROUP BY MB.MEMBER_ID
                   ORDER BY climb_count DESC, height DESC, kilo DESC, time DESC
                   LIMIT 5;";

    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($rows);

?>