<?php
session_start();
require 'db.php';

// 檢查是否為管理員
if (!isset($_SESSION['is_admin'])) {
    header("Location: login.php");
    exit;
}

// 處理訂單狀態更新
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "訂單狀態已更新！";
    } else {
        $_SESSION['error_message'] = "更新失敗: " . $conn->error;
    }
    $stmt->close();
}

// 處理訂單刪除
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "訂單已刪除！";
    } else {
        $_SESSION['error_message'] = "刪除失敗: " . $conn->error;
    }
    $stmt->close();
    
    header("Location: view_orders.php");
    exit;
}

// 獲取所有訂單
$result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>訂單管理</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #d4a373;
            --primary-dark: #b08a5d;
            --secondary: #fefae0;
            --accent: #e9edc9;
            --text: #5a4a42;
            --light-text: #8a735b;
            --error: #cc3333;
            --error-bg: #ffdddd;
            --success:rgb(217, 182, 112);
            --success-bg:rgb(240, 212, 167);
        }
        
        body {
            font-family: 'Noto Sans TC', sans-serif;
            background-color: #f8f4e9;
            color: var(--text);
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 30px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }
        h1 {
            color: var(--primary-dark);
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 15px;
            font-weight: 700;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            background-color: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        th, td {
            border: 1px solid #e8d5c5;
            padding: 14px 12px;
            text-align: center;
            vertical-align: middle;
        }
        th {
            background-color: var(--primary);
            color: white;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        tr:nth-child(even) {
            background-color: var(--secondary);
        }
        tr:hover {
            background-color: var(--accent);
        }
        .status-form {
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
        }
        select {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #d9c7b8;
            background-color: white;
            color: var(--text);
            font-family: 'Noto Sans TC', sans-serif;
            min-width: 120px;
        }
        select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(212, 163, 115, 0.3);
        }
        button, .btn {
            padding: 8px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            font-family: 'Noto Sans TC', sans-serif;
        }
        .btn-update {
            background-color: var(--primary);
            color: white;
        }
        .btn-update:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }
        .btn-delete {
            background-color: #c8553d;
            color: white;
            text-decoration: none;
            display: inline-block;
            margin-left: 8px;
            padding: 8px 16px;
        }
        .btn-delete:hover {
            background-color: #a94432;
            transform: translateY(-2px);
        }
        .message {
            padding: 14px;
            margin: 25px 0;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
            border: 1px solid transparent;
        }
        .success {
            background-color: var(--success-bg);
            color: var(--success);
            border-color:rgb(232, 215, 141);
        }
        .error {
            background-color: var(--error-bg);
            color: var(--error);
            border-color: #ffcdd2;
        }
        .back-link {
            text-align: center;
            margin-top: 35px;
        }
        .back-link a {
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.3s;
            display: inline-block;
        }
        .back-link a:hover {
            color: white;
            background-color: var(--primary);
            text-decoration: none;
        }
        .price {
            font-weight: 500;
            color: #b5651d;
        }
        .date {
            color: var(--light-text);
            font-size: 0.9em;
        }
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            th, td {
                padding: 10px 8px;
                font-size: 14px;
            }
            .status-form {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>訂單管理系統</h1>
        
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
                    <th>訂單ID</th>
                    <th>客戶姓名</th>
                    <th>聯絡電話</th>
                    <th>電子郵件</th>
                    <th>總金額</th>
                    <th>訂單日期</th>
                    <th>物流狀態</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($order = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['customer_name']) ?></td>
                            <td><?= htmlspecialchars($order['phone']) ?></td>
                            <td><?= htmlspecialchars($order['email']) ?></td>
                            <td class="price">$<?= number_format($order['total_price']) ?></td>
                            <td class="date"><?= $order['created_at'] ?></td>
                            <td>
                                <form method="post" class="status-form">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <select name="status">
                                        <option value="未出貨" <?= $order['status'] == '未出貨' ? 'selected' : '' ?>>未出貨</option>
                                        <option value="處理中" <?= $order['status'] == '處理中' ? 'selected' : '' ?>>處理中</option>
                                        <option value="已出貨" <?= $order['status'] == '已出貨' ? 'selected' : '' ?>>已出貨</option>
                                        <option value="已送達" <?= $order['status'] == '已送達' ? 'selected' : '' ?>>已送達</option>
                                        <option value="已取消" <?= $order['status'] == '已取消' ? 'selected' : '' ?>>已取消</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn-update">更新</button>
                                </form>
                            </td>
                            <td>
                                <a href="?delete=<?= $order['id'] ?>" class="btn-delete" onclick="return confirm('確定要刪除此訂單嗎？');">刪除</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 30px; color: var(--light-text); font-style: italic;">
                            目前沒有訂單記錄
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="back-link">
            <a href="admin.php">← 返回管理後台</a>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>