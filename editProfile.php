<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$name = $phone = $email = $address = $gender = $avatar = '';

$stmt = $conn->prepare("SELECT name, phone, email, address, gender, avatar FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $phone, $email, $address, $gender, $avatar);

if (!$stmt->fetch()) {
    echo "找不到使用者資料";
    exit;
}
$stmt->close();

$avatarPath = 'usersAvatar/' . ($avatar ?: 'default.png');
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>修改資料</title>
    <style>
        body {
            background-color: #fdf6ec;
            font-family: Arial, sans-serif;
            padding: 20px;
            color: #4e342e;
        }

        h2 {
            color: #5d4037;
        }

        .avatar-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .avatar-container img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }

        form {
            background-color: #fff8f0;
            padding: 20px;
            border: 1px solid #d7a86e;
            border-radius: 8px;
            max-width: 400px;
            margin: 0 auto;
        }

        input[type="text"], input[type="email"], input[type="password"], select, input[type="file"] {
            width: 100%;
            padding: 8px;
            margin: 6px 0 12px;
            border: 1px solid #c28e5c;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #b08968;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #a17455;
        }
    </style>
</head>
<body>

<h2>修改會員資料</h2>

<div class="avatar-container">
    <img src="<?php echo htmlspecialchars($avatarPath); ?>" alt="頭像">
</div>

<form method="POST" action="updateProfile.php" enctype="multipart/form-data">
    姓名: <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>"><br>

    性別:
    <select name="gender">
        <option value="男" <?php if ($gender === '男') echo 'selected'; ?>>男</option>
        <option value="女" <?php if ($gender === '女') echo 'selected'; ?>>女</option>
        <option value="其他" <?php if ($gender === '其他') echo 'selected'; ?>>其他</option>
    </select><br>

    電話: <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>"><br>
    電子郵件: <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>"><br>
    地址: <input type="text" name="address" value="<?php echo htmlspecialchars($address); ?>"><br>
    密碼: <input type="password" name="password">（如不更改請留空）<br>

    頭像: <input type="file" name="avatar"><br>

    <input type="submit" value="儲存">
</form>

</body>
</html>
