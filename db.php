<?php
$host = 'localhost';         // 或 127.0.0.1
$dbname = 'NUKMerchShop';   // 這裡改成你建立的資料庫名稱
$user = 'root';              // 根據你的資料庫帳號設定
$pass = '';                  // 如果有密碼要填上

$conn = new mysqli($host, $user, $pass, $dbname);

// 檢查連線
if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}

// 設定編碼為 UTF-8
$conn->set_charset("utf8");
?>
