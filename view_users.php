<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    die("你沒有權限進入此頁面");
}

require 'db.php';

// 確保userAvatar目錄存在
if (!file_exists('userAvatar')) {
    mkdir('userAvatar', 0755, true);
}

// 初始化變數
$result = null;
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : null;

// 新增用戶
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    // 先檢查email是否已存在
    $email = $_POST['email'];
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows > 0) {
        $_SESSION['error_message'] = "此Email已經被註冊，請使用其他Email地址。";
    } else {
        $avatar_only = 'default.png';

        // 處理頭像上傳
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'userAvatar/';
            $avatar_name = uniqid() . '_' . basename($_FILES['avatar']['name']);
            $avatar_only = $avatar_name;
            $target_file = $upload_dir . $avatar_name;

            // 確認檔案為圖片
            $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            if (in_array($image_file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
                if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
                    $_SESSION['error_message'] = "頭像上傳失敗";
                    $avatar_only = 'default.png';
                }
            } else {
                $_SESSION['error_message'] = "僅允許上傳 JPG, JPEG, PNG 和 GIF 圖片檔案。";
                $avatar_only = 'default.png';
            }
        }

        // 密碼哈希處理
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // 寫入資料庫
        $stmt = $conn->prepare("INSERT INTO users (name, gender, phone, email, address, password, avatar) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $_POST['name'], $_POST['gender'], $_POST['phone'], $email, $_POST['address'], $hashed_password, $avatar_only);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "用戶新增成功！";
            header("Location: view_users.php");
            exit;
        } else {
            $_SESSION['error_message'] = "新增用戶失敗: " . $conn->error;
        }
        $stmt->close();
    }
    $check_stmt->close();
    header("Location: view_users.php");
    exit;
}

// 刪除用戶
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    
    $_SESSION['success_message'] = "用戶刪除成功！";
    header("Location: view_users.php");
    exit;
}

// 更新用戶
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
    // 如果有上傳新頭像
    $avatar_only = $_POST['current_avatar'];
    
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'userAvatar/';
        $avatar_name = uniqid() . '_' . basename($_FILES['avatar']['name']);
        $avatar_only = $avatar_name;
        $target_file = $upload_dir . $avatar_name;

        // 確認檔案為圖片
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($image_file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
                $_SESSION['error_message'] = "頭像上傳失敗";
                $avatar_only = $_POST['current_avatar'];
            }
        } else {
            $_SESSION['error_message'] = "僅允許上傳 JPG, JPEG, PNG 和 GIF 圖片檔案。";
            $avatar_only = $_POST['current_avatar'];
        }
    }

    // 如果密碼欄位有值，則更新密碼
    if (!empty($_POST['password'])) {
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET name=?, gender=?, phone=?, email=?, address=?, password=?, avatar=? WHERE id=?");
        $stmt->bind_param("sssssssi", $_POST['name'], $_POST['gender'], $_POST['phone'], $_POST['email'], $_POST['address'], $hashed_password, $avatar_only, $_POST['id']);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, gender=?, phone=?, email=?, address=?, avatar=? WHERE id=?");
        $stmt->bind_param("ssssssi", $_POST['name'], $_POST['gender'], $_POST['phone'], $_POST['email'], $_POST['address'], $avatar_only, $_POST['id']);
    }
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "用戶更新成功！";
        header("Location: view_users.php");
        exit;
    } else {
        $_SESSION['error_message'] = "更新用戶失敗: " . $conn->error;
    }
    $stmt->close();
    header("Location: view_users.php");
    exit;
}

// 獲取用戶列表
$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
if (!$result) {
    die("查詢失敗: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>用戶管理</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans TC', sans-serif;
            background-color: #fdf6f0;
            color: #5a4a42;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 30px;
            background-color: #fff9f2;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
        }
        h1 {
            color: #b5651d;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
            border-bottom: 2px solid #e6c7a8;
            padding-bottom: 15px;
        }
        h2 {
            color: #8c4a1f;
            text-align: center;
            margin: 30px 0 20px;
            font-weight: 600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            table-layout: fixed;
            background-color: #fff;
        }
        th, td {
            border: 1px solid #e8d5c5;
            padding: 12px 10px;
            text-align: center;
            word-wrap: break-word;
        }
        th {
            background-color: #d4a373;
            color: #fff;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        tr:nth-child(even) {
            background-color: #f8efe6;
        }
        tr:hover {
            background-color: #f3e5d7;
        }
        input[type=text],
        input[type=email],
        input[type=password],
        input[type=tel],
        select {
            padding: 10px;
            border: 1px solid #d9c7b8;
            border-radius: 6px;
            width: 100%;
            box-sizing: border-box;
            margin: 5px 0;
            background-color: #fff;
            font-family: 'Noto Sans TC', sans-serif;
            transition: all 0.3s ease;
        }
        input:focus, select:focus {
            border-color: #b5651d;
            outline: none;
            box-shadow: 0 0 0 2px rgba(181, 101, 29, 0.2);
        }
        button, .button-link {
            padding: 10px 18px;
            background-color: #c08552;
            border: none;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
            font-size: 14px;
            font-weight: 500;
            font-family: 'Noto Sans TC', sans-serif;
            transition: all 0.3s ease;
        }
        button:hover, .button-link:hover {
            background-color: #a97148;
            transform: translateY(-2px);
        }
        .form-section {
            margin-top: 40px;
            padding: 25px;
            background-color: #f8efe6;
            border-radius: 10px;
            border: 1px solid #e8d5c5;
        }
        .form-group {
            margin-bottom: 18px;
            display: flex;
            align-items: center;
        }
        .form-group label {
            width: 80px;
            margin-right: 15px;
            text-align: right;
            color: #7a5c44;
            font-weight: 500;
        }
        .form-inline {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .form-inline .form-group {
            margin: 0;
            flex: 1 1 200px;
        }
        .back-link {
            margin-top: 30px;
            text-align: center;
        }
        .back-link a {
            color: #b5651d;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .back-link a:hover {
            color: #8c4a1f;
            text-decoration: underline;
        }
        .avatar-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e8d5c5;
            background-color: #f8efe6;
            display: block;
            margin: 0 auto;
        }
        .avatar-upload {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        .avatar-upload input[type="file"] {
            width: 100%;
            padding: 5px;
        }
        .message {
            padding: 12px 15px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .action-cell {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        .editing-row td {
            padding: 10px;
            background-color: #f8efe6;
        }
        .editing-row input, .editing-row select {
            width: 95%;
        }
        .no-users {
            text-align: center;
            padding: 30px;
            font-style: italic;
            color: #9c8a7a;
            font-size: 16px;
        }
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            .form-inline .form-group {
                flex: 1 1 100%;
            }
            th, td {
                padding: 8px 5px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>用戶管理</h1>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="message error">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="message success">
            <?= htmlspecialchars($_SESSION['success_message']) ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 10%;">姓名</th>
                <th style="width: 5%;">性別</th>
                <th style="width: 10%;">電話</th>
                <th style="width: 15%;">Email</th>
                <th style="width: 15%;">地址</th>
                <th style="width: 10%;">頭像</th>
                <th style="width: 10%;">註冊時間</th>
                <th style="width: 20%;">操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php if ($edit_id === intval($row['id'])): ?>
                        <!-- 編輯模式 -->
                        <form method="post" enctype="multipart/form-data">
                            <tr class="editing-row">
                                <td><?= $row['id'] ?></td>
                                <td><input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required></td>
                                <td>
                                    <select name="gender" required>
                                        <option value="男" <?= $row['gender'] === '男' ? 'selected' : '' ?>>男</option>
                                        <option value="女" <?= $row['gender'] === '女' ? 'selected' : '' ?>>女</option>
                                    </select>
                                </td>
                                <td><input type="tel" name="phone" value="<?= htmlspecialchars($row['phone']) ?>" required></td>
                                <td><input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" required></td>
                                <td><input type="text" name="address" value="<?= htmlspecialchars($row['address']) ?>"></td>
                                <td class="avatar-upload">
                                    <img src="userAvatar/<?= htmlspecialchars($row['avatar']) ?>" class="avatar-img" alt="頭像" onerror="this.onerror=null;this.src='userAvatar/default.png'">
                                    <input type="file" name="avatar" accept="image/*">
                                    <input type="hidden" name="current_avatar" value="<?= htmlspecialchars($row['avatar']) ?>">
                                </td>
                                <td><?= $row['created_at'] ?></td>
                                <td class="action-cell">
                                    <input type="hidden" name="edit" value="1">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <input type="password" name="password" placeholder="新密碼 (留空不變)">
                                    <button type="submit">儲存</button>
                                    <a class="button-link" href="view_users.php">取消</a>
                                </td>
                            </tr>
                        </form>
                    <?php else: ?>
                        <!-- 顯示模式 -->
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['gender']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['address']) ?></td>
                            <td><img src="userAvatar/<?= htmlspecialchars($row['avatar']) ?>" class="avatar-img" alt="頭像" onerror="this.onerror=null;this.src='userAvatar/default.png'"></td>
                            <td><?= $row['created_at'] ?></td>
                            <td class="action-cell">
                                <a class="button-link" href="?edit=<?= $row['id'] ?>">編輯</a>
                                <a class="button-link" href="?delete=<?= $row['id'] ?>" onclick="return confirm('確定要刪除此用戶嗎？');">刪除</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="no-users">沒有找到用戶</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="form-section">
        <h2>新增用戶</h2>
        <form method="post" enctype="multipart/form-data" class="form-inline">
            <input type="hidden" name="add" value="1">
            <div class="form-group">
                <label for="name">姓名:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="gender">性別:</label>
                <select id="gender" name="gender" required>
                    <option value="男">男</option>
                    <option value="女">女</option>
                </select>
            </div>
            <div class="form-group">
                <label for="phone">電話:</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="address">地址:</label>
                <input type="text" id="address" name="address">
            </div>
            <div class="form-group">
                <label for="password">密碼:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="avatar">頭像:</label>
                <input type="file" id="avatar" name="avatar" accept="image/*">
            </div>
            <div class="form-group" style="justify-content: center;">
                <button type="submit">新增</button>
            </div>
        </form>
    </div>

    <div class="back-link">
        <a href="admin.php">← 回到管理員首頁</a>
    </div>
</div>
</body>
</html>

<?php 
// 關閉資料庫連接
if (isset($conn)) {
    $conn->close();
}
?>