<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    die("你沒有權限進入此頁面");
}

require 'db.php';

// 新增商品
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $target_file = '';

    // 處理圖片上傳
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'goodImage/';
        $image_name = basename($_FILES['image']['name']);
        $target_file = $upload_dir . $image_name;

        // 確認檔案為圖片
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($image_file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                echo "圖片上傳失敗";
                $target_file = '';
            }
        } else {
            echo "僅允許上傳 JPG, JPEG, PNG 和 GIF 圖片檔案。";
            $target_file = '';
        }
    }

    // 寫入資料庫
    $stmt = $conn->prepare("INSERT INTO goods (name, size, color, price, amount, image_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $_POST['name'], $_POST['size'], $_POST['color'], $_POST['price'], $_POST['amount'], $target_file);
    $stmt->execute();
    $stmt->close();

    header("Location: product_manage.php");
    exit;
}

// 刪除商品
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM goods WHERE no = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
}

// 更新商品
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
    $stmt = $conn->prepare("UPDATE goods SET name=?, size=?, color=?, price=?, amount=? WHERE no=?");
    $stmt->bind_param("sssiii", $_POST['name'], $_POST['size'], $_POST['color'], $_POST['price'], $_POST['amount'], $_POST['no']);
    $stmt->execute();
    $stmt->close();
}

$result = $conn->query("SELECT * FROM goods ORDER BY no DESC");
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : null;
?>


<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>商品管理</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans TC', sans-serif;
            background-color: #f2e2d2;
            color: #4e342e;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 30px;
            background-color: #f5e0c8;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            border: 1px solid #d2b48c;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #e4bfa3;
        }
        tr:nth-child(even) {
            background-color: #f9e5d3;
        }
        input[type=text], input[type=number] {
            padding: 5px;
            width: 100px;
        }
        button {
            padding: 5px 10px;
            background-color: #b08968;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #a17256;
        }
        a.button-link {
            background-color: #b08968;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }
        a.button-link:hover {
            background-color: #a17256;
        }
        .form-section {
            margin-top: 40px;
            text-align: center;
        }
        .form-inline input {
            margin: 0 5px;
        }
        .back-link {
            margin-top: 30px;
            text-align: center;
        }
        .back-link a {
            color: #5d4037;
            text-decoration: none;
            font-size: 16px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>商品管理</h1>

    <table>
        <tr>
            <th>編號</th>
            <th>名稱</th>
            <th>尺寸</th>
            <th>顏色</th>
            <th>價格</th>
            <th>庫存</th>
            <th>操作</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php if ($edit_id === intval($row['no'])): ?>
                <!-- 編輯模式 -->
                <form method="post">
                    <tr>
                        <td><?= $row['no'] ?></td>
                        <td><input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required></td>
                        <td><input type="text" name="size" value="<?= htmlspecialchars($row['size']) ?>" required></td>
                        <td><input type="text" name="color" value="<?= htmlspecialchars($row['color']) ?>" required></td>
                        <td><input type="number" name="price" value="<?= $row['price'] ?>" required></td>
                        <td><input type="number" name="amount" value="<?= $row['amount'] ?>" required></td>
                        <td>
                            <input type="hidden" name="edit" value="1">
                            <input type="hidden" name="no" value="<?= $row['no'] ?>">
                            <button type="submit">儲存</button>
                            <a class="button-link" href="product_manage.php">取消</a>
                        </td>
                    </tr>
                </form>
            <?php else: ?>
                <!-- 顯示模式 -->
                <tr>
                    <td><?= $row['no'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['size']) ?></td>
                    <td><?= htmlspecialchars($row['color']) ?></td>
                    <td><?= $row['price'] ?></td>
                    <td><?= $row['amount'] ?></td>
                    <td>
                        <a class="button-link" href="?edit=<?= $row['no'] ?>">編輯</a>
                        <a class="button-link" href="?delete=<?= $row['no'] ?>" onclick="return confirm('確定要刪除嗎？');">刪除</a>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endwhile; ?>
    </table>

    <div class="form-section">
    <h2>新增商品</h2>
    <form method="post" enctype="multipart/form-data" class="form-inline">
        <input type="hidden" name="add" value="1">
        名稱 <input type="text" name="name" required>
        尺寸 <input type="text" name="size" required>
        顏色 <input type="text" name="color" required>
        價格 <input type="number" name="price" required>
        庫存 <input type="number" name="amount" required>
        圖片 <input type="file" name="image" accept="image/*">
        <button type="submit">新增</button>
    </form>
</div>


    <div class="back-link">
        <a href="admin.php">← 回到管理員首頁</a>
    </div>
</div>
</body>
</html>

<?php $conn->close(); ?>
