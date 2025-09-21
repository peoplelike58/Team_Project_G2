<?php   /* LoginPage_fontLogout.php 登出php */

include 'conn.php';

session_start();

// 清掉 session
// 完全銷毀會員資料，而不是只清空陣列
unset($_SESSION['member']);  // 完全移除 member 這個 key
// $_SESSION['member'] = []; 只是清空陣列，但 key 還存在

// 檢查是否有使用 cookie 來儲存 session ID,刪除 session cookie
if (ini_get("session.use_cookies")) {                               // ini_get("session.use_cookies") 檢查 PHP 設定中是否啟用了 session cookie,通常這個值是 "1"（啟用），如果是 "0" 就表示不使用 cookie
    
    $params = session_get_cookie_params();                          // 取得目前 session cookie 的所有參數
    // 這會回傳一個陣列，包含：
    // $params["lifetime"] - cookie 生命週期
    // $params["path"] - cookie 有效路徑
    // $params["domain"] - cookie 有效網域
    // $params["secure"] - 是否只在 HTTPS 下傳送
    // $params["httponly"] - 是否只能透過 HTTP 存取（JavaScript 無法讀取）
    
    setcookie(                 // 3. 設定一個相同參數但已過期的 cookie 來覆蓋原本的 cookie
        session_name(),             // cookie 名稱（通常是 PHPSESSID）
        '',                    // cookie 值設為空字串
        time() - 42000,        // 過期時間設為 42000 秒前（約 11.6 小時前）
        $params["path"],       // 使用原本的路徑
        $params["domain"],     // 使用原本的網域
        $params["secure"],     // 使用原本的安全設定
        $params["httponly"]    // 使用原本的 httponly 設定
    );
}

// 完全清理 session
session_unset();    // 清除所有 session 變數
session_destroy();  // 銷毀 session 檔案
session_write_close(); // 強制寫入並關閉 session

echo json_encode(['success' => true, 'msg' => '已登出'],JSON_UNESCAPED_UNICODE);

?>
