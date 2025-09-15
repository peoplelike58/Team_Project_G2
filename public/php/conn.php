<?php
// 共用 連線資料庫

      //MySQL相關資訊（本機端測試）
      $db_host = "127.0.0.1";
      $db_user = "root";
      $db_pass = "password";
      $db_select = "hou_shan";

      //=====================================重要
      //上傳上傳到TibaMe伺服器時改成如下
      //$db_host = "127.0.0.1";
      // $db_user = "tibamefe_since2021";
      // $db_pass = "vwRBSb.j&K#E";
      // $db_select = "tibamefe_tjd102g2";


      //建立資料庫連線物件
      $dsn = "mysql:host=".$db_host.";dbname=".$db_select.";charset=utf8";

      //建立PDO物件，並放入指定的相關資料
      $pdo = new PDO($dsn, $db_user, $db_pass);

      //Fetch API with CORS （共用設定） 這樣其它頁面就不用寫
      // header("Access-Control-Allow-Origin: *");
      
      // 若前端在 http://localhost:5173
      header('Access-Control-Allow-Origin: http://localhost:5173'); // ⚠️ 不能用 *
      header('Access-Control-Allow-Credentials: true');
      header('Access-Control-Allow-Headers: Content-Type');
      header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
      header('Content-Type: application/json; charset=utf-8');
   




