<?php

include __DIR__ . '/conn.php';
header("Access-Control-Allow-Methods: POST, OPTIONS");// 允許的請求方法
header("Access-Control-Allow-Headers: Content-Type");// 允許的自訂標頭


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

//後台傳來JSON 轉成php的物件或陣列
$updatenews = json_decode(file_get_contents("php://input"), true);

//COALESCE() Function (回傳第一個非空值的值，如果是NULL，就用原本的值)
$sql= "UPDATE EVENT 
        SET EVENT_NAME=COALESCE(:ename , EVENT_NAME),
            EVENT_DATE=COALESCE(:edate, EVENT_DATE),
            EVENT_TIME=COALESCE(:etime, EVENT_TIME),
            START_DATE=COALESCE(:sdate, START_DATE),
            START_TIME=COALESCE(:stime, START_TIME),
            END_DATETIME=COALESCE(:edatetime, END_DATETIME),
            STATUS=COALESCE(:status, STATUS),
            CONTENT=COALESCE(:content, CONTENT),
            MEETING_PLACE=COALESCE(:mplace, MEETING_PLACE),
            DISTANCE=COALESCE(:distance, DISTANCE)
        WHERE EVENT_ID = :id";

    
$pstmt = $pdo->prepare($sql);
//如果不是空字串，就傳值，如果空字串，就轉成null
$pstmt->bindValue(":ename", $updatenews['EVENT_NAME'] !=='' ? $updatenews['EVENT_NAME'] : null);
$pstmt->bindValue(":edate", $updatenews['EVENT_DATE']!=='' ? $updatenews['EVENT_DATE'] : null);
$pstmt->bindValue(":etime", $updatenews['EVENT_TIME']!=='' ? $updatenews['EVENT_TIME'] : null);
$pstmt->bindValue(":sdate", $updatenews['START_DATE']!=='' ? $updatenews['START_DATE'] : null);
$pstmt->bindValue(":stime", $updatenews['START_TIME']!=='' ? $updatenews['START_TIME'] : null);
$pstmt->bindValue(":edatetime", $updatenews['END_DATETIME']!=='' ? $updatenews['END_DATETIME'] : null);
$pstmt->bindValue(":status", $updatenews['STATUS']!=='' ? $updatenews['STATUS'] : null);
$pstmt->bindValue(":content", $updatenews['CONTENT']!=='' ? $updatenews['CONTENT'] : null);
$pstmt->bindValue(":distance", $updatenews['DISTANCE']!=='' ? $updatenews['DISTANCE'] : null);
$pstmt->bindValue(":mplace", $updatenews['MEETING_PLACE']!=='' ? $updatenews['MEETING_PLACE'] : null);
$pstmt->bindValue(":id", $updatenews['EVENT_ID']);
$ok = $pstmt->execute();



if ($ok && $pstmt->rowCount() > 0) {
    echo json_encode(['success' => true, 'message' => '修改成功']);
} else {
    echo json_encode(['success' => false, 'message' => '修改失敗']);
}
