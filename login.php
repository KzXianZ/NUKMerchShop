
<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php';  // 確保 $conn 已連線

$email  = $_POST['email'] ?? '';
$raw_pw = $_POST['password'] ?? '';

// 管理員帳號寫死
if ($email === 'user' && $raw_pw === '1111') {
    $_SESSION['user_id'] = -1;
    $_SESSION['email']   = 'admin';
    $_SESSION['is_admin'] = true;
    header("Location: admin.php");
    exit;
}

// 1. 讀出該 Email 的 id 與雜湊後密碼（一般使用者）
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
        $_SESSION['user_id'] = $id;
        $_SESSION['email']   = $email;
        $_SESSION['is_admin'] = false;
        header("Location: memberPage.php");
        exit;
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>登入失敗</title>
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
    </style>
</head>
<body>
<div class="container">
    <h2>登入失敗</h2>
    <p><?php echo $errorMsg ?? '請重新登入'; ?></p>
    <p>將在 3 秒後自動返回登入頁...</p>
</div>
</body>
</html>