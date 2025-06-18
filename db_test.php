<?php
$host = 'sql206.infinityfree.com';
$dbname = 'if0_38988364_nukmerchshop';
$user = 'if0_38988364';
$pass = 'oFFNHrcFfxtT05';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
} else {
    echo "成功連接資料庫！";
}
?>