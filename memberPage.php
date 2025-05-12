<?php
session_start();
require 'db.php';

// 檢查是否已登入
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 取得使用者資料
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email, avatar FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $avatar);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>會員中心</title>
    <style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f0d8b8; /* 溫暖底色 */
        color: #4e342e;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 500px;
        margin: 80px auto;
        background-color: #fff3e0; /* 中間卡片淺米色 */
        padding: 30px;
        border-radius: 16px;
        text-align: center;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    h2 {
        color: #4e342e;
        margin-bottom: 20px;
    }

    .btn {
        display: block;
        margin: 12px 0;
        padding: 12px 20px;
        color: white;
        text-decoration: none;
        border-radius: 10px;
        font-weight: bold;
        transition: background-color 0.3s ease;
        box-shadow: inset 0 -2px 0 rgba(0,0,0,0.1);
    }

    .btn-profile {
        background-color: #c69c6d;
    }
    .btn-profile:hover {
        background-color: #b8865c;
    }

    .btn-orders {
        background-color: #d6a66c;
    }
    .btn-orders:hover {
        background-color: #c18f55;
    }

    .btn-logout {
        background-color: #e0b07d;
    }
    .btn-logout:hover {
        background-color: #cc9c67;
    }

    .avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        margin-bottom: 20px;
        object-fit: cover;
        border: 2px solid #d7ccc8;
    }

    .back-home {
        text-align: center;
        margin-top: 30px;
    }

   .back-home a {
    background-color: #795548;
    color: white;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: bold;
    display: inline-block;
    transition: background-color 0.3s ease;
}

.back-home a:hover {
    background-color: #5d4037;
}

</style>


</head>
<body>

<div style="text-align: center; margin-top: 30px;">
    <div class="back-home">
    <a href="index.php">回到主頁</a>
</div>

</div>

<div class="container">
    <h2>歡迎<?php echo htmlspecialchars($name); ?></h2>

    <?php if ($avatar): ?>
        <!-- 顯示頭像 -->
        <img src="usersAvatar/<?php echo htmlspecialchars($avatar); ?>" alt="頭像" class="avatar">
    <?php else: ?>
        <!-- 預設頭像 -->
        <img src="usersAvatar/default-avatar.png" alt="預設頭像" class="avatar">
    <?php endif; ?>

    
    <a href="editProfile.php" class="btn btn-profile">帳戶詳細資料</a>
    <a href="orderStatus.php" class="btn btn-orders">訂單狀態</a>
    <a href="logout.php" class="btn btn-logout">登出</a>


</div>

</body>
</html>
