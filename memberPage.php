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
            font-family: Arial, sans-serif;
            background-color: #fdf6ec;
            color: #4e342e;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 80px auto;
            background-color: #fff8f0;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #5d4037;
        }

        a {
            display: block;  /* 改為塊級元素，使其垂直排列 */
            margin: 12px 0;
            padding: 10px 20px;
            background-color: #b08968;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }


        a:hover {
            background-color: #a17256;
        }

        img.avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>歡迎，<?php echo htmlspecialchars($name); ?></h2>

    <?php if ($avatar): ?>
        <!-- 顯示頭像 -->
        <img src="usersAvatar/<?php echo htmlspecialchars($avatar); ?>" alt="頭像" class="avatar">
    <?php else: ?>
        <!-- 預設頭像 -->
        <img src="usersAvatar/default-avatar.png" alt="預設頭像" class="avatar">
    <?php endif; ?>

    
    <a href="editProfile.php">帳戶詳細資料</a>
    <a href="logout.php">登出</a>
</div>

</body>
</html>
