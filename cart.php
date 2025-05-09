<?php
session_start();

// 確認使用者是否登入
if (!isset($_SESSION['user_id'])) {
    die("請先登入才能查看購物車");
}

// 連接資料庫
$conn = new mysqli('localhost', 'root', '', 'nukmerchshop');
if ($conn->connect_error) {
    die("資料庫連接失敗: " . $conn->connect_error);
}

$userId = $_SESSION['user_id'];

// 處理表單提交，更新購物車商品數量
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 更新數量
    if (isset($_POST['update_cart'])) {
        $cartItemId = $_POST['cart_item_id'];
        $newAmount = $_POST['amount'];

        // 檢查數量是否有效
        if ($newAmount <= 0) {
            die("數量必須大於 0");
        }

        // 更新購物車中的商品數量
        $updateSql = "UPDATE cart_items SET amount = ? WHERE id = ? AND user_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("iii", $newAmount, $cartItemId, $userId);
        $updateStmt->execute();
    }

    // 刪除商品
    if (isset($_POST['delete_cart_item'])) {
        $cartItemId = $_POST['cart_item_id'];

        // 刪除購物車中的商品
        $deleteSql = "DELETE FROM cart_items WHERE id = ? AND user_id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("ii", $cartItemId, $userId);
        $deleteStmt->execute();
    }
}

// 查詢購物車內容
$sql = "SELECT id, product_name, size, color, price, amount FROM cart_items WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$items = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>購物車</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #fdf6ee;
            color: #4b3a2d;
            padding: 20px;
        }
        h1 {
            color: #8b5c3e;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff7ed;
        }
        th, td {
            border: 1px solid #e2c7ab;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #f1d6b8;
        }
        .total {
            text-align: right;
            margin-top: 20px;
            font-size: 18px;
        }
        input[type="number"] {
            width: 60px;
            text-align: center;
        }
        button {
            background-color: #c89b7b;
            color: white;
            border: none;
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #a7795c;
        }
        .delete-btn {
            background-color: #e74c3c;
            padding: 6px 12px;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

<div style="text-align: center; margin-top: 30px;">
    <a href="index.php" style="
        background-color: #d7ccc8;
        color: #4e342e;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: bold;
        display: inline-block;
    "> 回到主頁</a>
</div>

    <h1>🛒 我的購物車</h1>

    <?php if (empty($items)): ?>ko
    <?php else: ?>
        <form method="POST">
            <table>
                <thead>
                    <tr>
                        <th>商品名稱</th>
                        <th>尺寸</th>
                        <th>顏色</th>
                        <th>單價</th>
                        <th>數量</th>
                        <th>小計</th>
                        <th>更新</th>
                        <th>刪除</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($items as $item):
                        $subtotal = $item['price'] * $item['amount'];
                        $total += $subtotal;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= htmlspecialchars($item['size']) ?></td>
                        <td><?= htmlspecialchars($item['color']) ?></td>
                        <td>NT$<?= $item['price'] ?></td>
                        <td>
                            <input type="number" name="amount" value="<?= $item['amount'] ?>" min="1" required>
                            <input type="hidden" name="cart_item_id" value="<?= $item['id'] ?>">
                        </td>
                        <td>NT$<?= $subtotal ?></td>
                        <td><button type="submit" name="update_cart">更新</button></td>
                        <td>
                            <button type="submit" name="delete_cart_item" class="delete-btn">刪除</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>

        <div class="total"><strong>總金額：</strong> NT$<?= $total ?></div>

        <form action="checkout.php" method="get">
        <center><button type="submit">前往結帳</button></center>
</form>
    <?php endif; ?>

</body>
</html>

<?php $conn->close(); ?>
