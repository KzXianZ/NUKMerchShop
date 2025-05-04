<?php
session_start();        // 一定要先開啟 session
require 'db.php';       // 請確認 db.php 用 mysqli 建立了 $conn

// 1. 取得表單資料
$email    = $_POST['email'];
$phone    = $_POST['phone'];
$raw_pw   = $_POST['password'];
$hashedPw = password_hash($raw_pw, PASSWORD_DEFAULT);

// 2. 檢查此 Email 是否已經被註冊
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    // 已存在，導回註冊頁或顯示錯誤
    echo "此 Email 已經註冊過";
    exit;
}
$stmt->close();

// 3. 寫入資料庫
$stmt = $conn->prepare("INSERT INTO users (email, phone, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $email, $phone, $hashedPw);
if ($stmt->execute()) {
    // 4. 寫入成功，取得自動編號，自動登入並導向會員頁
    $newId = $conn->insert_id;
    $_SESSION['user_id'] = $newId;
    $_SESSION['email']   = $email;
    header("Location: memberPage.php");
    exit;
} else {
    echo "註冊失敗：" . $stmt->error;
}
?>
