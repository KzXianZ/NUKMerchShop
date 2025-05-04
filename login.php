<?php
session_start();
require 'db.php';  // 確保 $conn 已連線

$email  = $_POST['email'] ?? '';
$raw_pw = $_POST['password'] ?? '';

// 1. 讀出該 Email 的 id 與雜湊後密碼
$stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $errorMsg = "查無此帳號";
} else {
    $stmt->bind_result($id, $hashedPw);
    $stmt->fetch();
    if (!password_verify($raw_pw, $hashedPw)) {
        $errorMsg = "密碼錯誤";
    } else {
        // 驗證成功
        $_SESSION['user_id'] = $id;
        $_SESSION['email']   = $email;
        header("Location: memberPage.php");
        exit;
    }
}
$stmt->close();

// 以下為錯誤顯示與自動跳轉
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>登入失敗</title>
    <!-- 3 秒後自動跳回登入頁 -->
    <meta http-equiv="refresh" content="3;url=member.php">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fdf6ec;
            color: #4e342e;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 400px;
            margin: 100px auto;
            background-color: #fff8f0;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        h2 {
            color: #5d4037;
            margin-bottom: 20px;
        }
        p {
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #b08968;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #a17256;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>登入失敗</h2>
        <p style="color: #d32f2f;"><?php echo htmlspecialchars($errorMsg); ?></p>
        <p>3 秒後自動回到登入頁，或點下方按鈕立即返回。</p>
        <a href="member.php" class="btn">回到登入頁</a>
    </div>
</body>
</html>
