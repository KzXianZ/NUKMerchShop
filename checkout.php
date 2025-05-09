<?php
session_start();

// 檢查是否登入
if (!isset($_SESSION['user_id'])) {
    die("請先登入才能結帳");
}

$conn = new mysqli('localhost', 'root', '', 'nukmerchshop');
if ($conn->connect_error) {
    die("資料庫連接失敗: " . $conn->connect_error);
}

$userId = $_SESSION['user_id'];

// 如果表單送出
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $email = $_POST['email'];

    // 查詢購物車
    $stmt = $conn->prepare("SELECT product_name, size, color, price, amount FROM cart_items WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $items = $result->fetch_all(MYSQLI_ASSOC);

    if (empty($items)) {
        die("購物車是空的，無法結帳");
    }

    // 計算總金額
    $total = 0;
    foreach ($items as $item) {
        $total += $item['price'] * $item['amount'];
    }

    // 插入訂單
    $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, phone, address, email, total_price, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $status = '未出貨';
    $stmt->bind_param("issssis", $userId, $name, $phone, $address, $email, $total, $status);
    $stmt->execute();
    $orderId = $stmt->insert_id;

    // 插入每筆商品
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_name, size, color, price, amount) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($items as $item) {
        $stmt->bind_param("isssii", $orderId, $item['product_name'], $item['size'], $item['color'], $item['price'], $item['amount']);
        $stmt->execute();
    }

    // 清空購物車
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    echo <<<HTML
    <!DOCTYPE html>
    <html lang="zh-Hant">
    <head>
        <meta charset="UTF-8">
        <title>結帳完成</title>
        <style>
            body {
                font-family: 'Segoe UI', sans-serif;
                background-color: #fdf6ee;
                color: #4b3a2d;
                padding: 40px;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .message-box {
                background-color: #fff7ed;
                padding: 30px;
                border: 1px solid #e2c7ab;
                border-radius: 10px;
                text-align: center;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }
            .message-box a {
                display: inline-block;
                margin-top: 15px;
                text-decoration: none;
                background-color: #8b5c3e;
                color: white;
                padding: 10px 20px;
                border-radius: 6px;
            }
        </style>
    </head>
    <body>
        <div class="message-box">
            <h2>✅ 訂單已成立</h2>
            <p>感謝您的購買！</p>
            <a href="index.php">返回首頁</a>
        </div>
    </body>
    </html>
    HTML;

    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>結帳</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #fdf6ee;
            color: #4b3a2d;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            color: #8b5c3e;
            margin-bottom: 20px;
        }
        form {
            background-color: #fff7ed;
            padding: 20px;
            border: 1px solid #e2c7ab;
            max-width: 500px;
            width: 100%;
            margin: 0 auto;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #8b5c3e;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 6px;
        }
        .note {
            margin-top: 10px;
            font-style: italic;
            color: #7b4a35;
        }
    </style>
</head>
<body>
    <h1>🧾 結帳資訊</h1>
    <form method="post">
        <label>姓名：
            <input type="text" name="name" required>
        </label>
        <label>電話號碼：
            <input type="text" name="phone" required>
        </label>
        <label>地址：
            <textarea name="address" required></textarea>
        </label>
        <label>電子郵件：
            <input type="email" name="email" required>
        </label>

        <p class="note">⚠️ 目前只支援貨到付款。</p>

        <button type="submit">確認結帳</button>
    </form>
</body>
</html>
