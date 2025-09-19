<?php  /* 抓取訂單明細-會員中心 */

include 'conn.php';

session_start();
if (!isset($_SESSION['member']) || !isset($_SESSION['member']['id'])) {
echo json_encode(['success' => false, 'message' => '尚未登入'],JSON_UNESCAPED_UNICODE); exit;
}

$memberId = $_SESSION['member']['id']; // 從 Session 拿會員ID
try {
$sql = "SELECT 
            ol.ORDER_ID,           
            ol.ORDER_CODE,         
            ol.ORDER_STATUS,       
            ol.TTL_AMT,           
            ol.MEMBER_NAME,        
            od.PRODUCT_ID,         
            od.QTY,               
            od.UNIT_PRICE,        
            od.SIZE,             
            od.COLOR,
            p.PRODUCT_NAME,
            p.IMAGE             
        FROM ORDER_LIST ol       
        LEFT JOIN ORDER_DETAIL od 
            ON ol.ORDER_ID = od.ORDER_ID
        LEFT JOIN PRODUCT p                     -- 左連接商品表
            ON od.PRODUCT_ID = p.PRODUCT_ID     
        WHERE ol.MEMBER_ID = :memberId      
        ORDER BY ol.ORDER_ID DESC, od.PRODUCT_ID ASC";

$pstmt = $pdo->prepare($sql);
$pstmt->bindValue( ":memberId", $memberId,PDO::PARAM_INT);
$pstmt->execute();
$orderData  = $pstmt->fetchAll(PDO::FETCH_ASSOC);

// 回傳成功結果
    // 將查詢結果重新組織成前端需要的格式
    $orders = [];
    $currentOrderId = null;
    
    // 遍歷查詢結果，將訂單和商品明細分組
    foreach ($orderData as $row) {
        // 「建立新的訂單項目」是指在程式中建立一個新的資料結構
        if ($currentOrderId !== $row['ORDER_ID']) {
            $currentOrderId = $row['ORDER_ID'];
            
            // 建立新的訂單項目
            $orders[] = [
                'id' => $row['ORDER_ID'],
                'orderNumber' => $row['ORDER_CODE'],
                'amount' => 'NT$' . number_format($row['TTL_AMT']), // 格式化金額
                'status' => $row['ORDER_STATUS'],
                'memberName' => $row['MEMBER_NAME'],                //收件人
                'products' => [],                                   // 初始化商品陣列
                'isExpanded' => false                               // 預設不展開
            ];
        }
        
        // 如果有商品明細，加入到當前訂單的商品陣列中
        if ($row['PRODUCT_ID']) {
            $lastIndex = count($orders) - 1;                        // 取得最後一個訂單的索引
            $orders[$lastIndex]['products'][] = [
                'id' => $row['PRODUCT_ID'],
                'name' => $row['PRODUCT_NAME'] ?: '商品名稱未設定',
                'image' => $row['IMAGE'] ?: '暫無商品圖片',
                'qty' => $row['QTY'],
                'unitPrice' => $row['UNIT_PRICE'],
                'size' => $row['SIZE'],
                'color' => $row['COLOR']
            ];
        }
    }
    
    // 檢查是否有找到資料
    if (count($orders) > 0) {
        // 成功找到資料，回傳結果
        echo json_encode([
            'success' => true,
            'message' => '成功取得訂單資料',
            'orders' => $orders // 回傳組織好的訂單資料
        ], JSON_UNESCAPED_UNICODE);
    } else {
        // 沒找到資料
        echo json_encode([
            'success' => false,
            'message' => '找不到訂單資料，會員ID: ' . $memberId
        ], JSON_UNESCAPED_UNICODE);
    }
    
} catch (PDOException $e) {
    // 資料庫錯誤處理
    echo json_encode([
        'success' => false,
        'message' => '資料庫查詢錯誤: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
