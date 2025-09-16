<?php
// 共用 連線資料庫

      //MySQL相關資訊（本機端測試）
      // $db_host = "127.0.0.1";
      // $db_user = "root";
      // $db_pass = "password";
      // $db_select = "hou_shan";

      //=====================================重要
      // 上傳上傳到TibaMe伺服器時改成如下
      $db_host = "127.0.0.1";
      $db_user = "tibamefe_since2021";
      $db_pass = "vwRBSb.j&K#E";
      $db_select = "tibamefe_tjd102g2";


      //建立資料庫連線物件
      $dsn = "mysql:host=".$db_host.";dbname=".$db_select.";charset=utf8";

      //建立PDO物件，並放入指定的相關資料
      $pdo = new PDO($dsn, $db_user, $db_pass);

      //Fetch API with CORS （共用設定） 這樣其它頁面就不用寫
      header("Access-Control-Allow-Origin: *");

      
      
      //若前端在 http://localhost:5173
      // header('Access-Control-Allow-Origin: http://localhost:5173'); // ⚠️ 不能用 * 會無法使用 Cookie/Session（瀏覽器限制）， 精確匹配，告訴瀏覽器允許這個來源存取資源
      // header('Access-Control-Allow-Credentials: true');             // ！允許發送 Cookie/Session - 登入需要它，是必須的
      // header('Access-Control-Allow-Headers: Content-Type');         // 允許前端發送 Content-Type header
      // header('Access-Control-Allow-Methods: GET, POST, OPTIONS');   // 允許的 HTTP 方法
      // header('Content-Type: application/json; charset=utf-8');      // application/json：內容是 JSON 格式，charset=utf-8：使用 UTF-8 編碼（支援中文）

      // ============== 新的 CORS 設定（支援本地+正式機）==============
      $allowed = [                          // 允許的來源域名白名單
          'http://localhost:5173',          // 本地開發
          'https://tibamef2e.com'           // 正式環境
      ];
      
      $origin = $_SERVER['HTTP_ORIGIN'] ?? '';          // 取得請求來源
      
      if (in_array($origin, $allowed, true)) {
          header("Access-Control-Allow-Origin: $origin");                
          header('Access-Control-Allow-Credentials: true');
          header('Access-Control-Allow-Headers: Content-Type');
          header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
      }
      header('Content-Type: application/json; charset=utf-8');
      
      // 處理瀏覽器的預檢請求（某些情況下瀏覽器會先發 OPTIONS 請求確認權限）
      if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
          http_response_code(204);
          exit;
      }




