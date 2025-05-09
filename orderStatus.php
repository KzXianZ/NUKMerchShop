<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, created_at AS order_date, total_price, status FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>我的訂單狀態</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fdf6ec;
            color: #4e342e;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff8f0;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #5d4037;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff7ed;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #e2c7ab;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #f1d6b8;
        }

        td {
            background-color: #f9f1e1;
        }

        .status {
            font-weight: bold;
            color: #8b5c3e;
        }

    </style>
</head>
<body>

<div class="container">
    <h2>我的訂單狀態</h2>
    <table>
        <thead>
            <tr>
                <th>訂單ID</th>
                <th>創建時間</th>
                <th>總金額</th>
                <th>訂單狀態</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['order_date']) ?></td>
                    <td>NT$ <?= htmlspecialchars($row['total_price']) ?></td>
                    <td class="status"><?= htmlspecialchars($row['status']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <br>
    <a href="index.php" style="background-color: #d7ccc8; color: #4e342e; padding: 10px 20px; border-radius: 6px; text-decoration: none;">回到首頁</a>
</div>

</body>
</html>
