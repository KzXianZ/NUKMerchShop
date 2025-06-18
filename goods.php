<?php
session_start();

// 連接資料庫
$conn = new mysqli('sql206.infinityfree.com', 'if0_38988364', 'oFFNHrcFfxtT05', 'if0_38988364_nukmerchshop');
if ($conn->connect_error) {
    die("資料庫連接失敗: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
$productName = trim($_GET['name'] ?? '');
$productName = urldecode($productName);

$sql = "SELECT * FROM goods WHERE TRIM(name) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $productName);
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);

if (empty($products)) {
    die("商品不存在");
}

$sizes = array_unique(array_column($products, 'size'));
$colors = array_unique(array_column($products, 'color'));


// 初始預設值
$selectedProduct = $products[0];
$selectedSize = $selectedProduct['size'];
$selectedColor = $selectedProduct['color'];
$stock = $selectedProduct['amount'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 更新選擇的尺寸和顏色
    if (isset($_POST['size']) && isset($_POST['color'])) {
        $selectedSize = $_POST['size'];
        $selectedColor = $_POST['color'];
        
        // 根據選擇的尺寸和顏色來選擇對應的商品資料
        foreach ($products as $product) {
            if ($product['size'] == $selectedSize && $product['color'] == $selectedColor) {
                $selectedProduct = $product;
                $stock = $product['amount'];
                break;
            }
        }
    }

    // 處理加入購物車的邏輯
    if (isset($_POST['add_to_cart'])) {
        if (!isset($_SESSION['user_id'])) {
            echo "<script>alert('請先登入才能加入購物車'); window.location.href='member.php';</script>";
            exit;
        }

        // 確保商品尚有庫存
        if ($stock > 0) {
            $user_id = $_SESSION['user_id'];
            $product_name = $selectedProduct['name'];  // 商品名稱
            $product_price = $selectedProduct['price']; // 商品價格
            $product_amount = $selectedProduct['amount']; // 庫存數量

            $stmt = $conn->prepare("SELECT amount FROM cart_items WHERE user_id = ? AND product_name = ? AND size = ? AND color = ?");
            $stmt->bind_param("isss", $_SESSION['user_id'], $product_name, $selectedSize, $selectedColor);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // 如果已經有該商品，更新數量
                $stmt = $conn->prepare("UPDATE cart_items SET amount = amount + 1 WHERE user_id = ? AND product_name = ? AND size = ? AND color = ?");
                $stmt->bind_param("isss", $_SESSION['user_id'], $product_name, $selectedSize, $selectedColor);
                $stmt->execute();
                echo "<script>alert('✅ 已將商品加入購物車');</script>";
            } else {
                // 如果沒有該商品，插入新記錄
                $stmt = $conn->prepare("INSERT INTO cart_items (user_id, product_name, size, color, price, amount) VALUES (?, ?, ?, ?, ?, 1)");
                $stmt->bind_param("isssd", $_SESSION['user_id'], $product_name, $selectedSize, $selectedColor, $product_price);
                $stmt->execute();
                echo "<script>alert('✅ 已將商品加入購物車');</script>";
            }

            $stmt->close();
        } else {
            echo "<script>alert('❌ 商品庫存不足'); window.location.href='product_detail.php?name=" . urlencode($product_name) . "';</script>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($productName) ?></title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #fdf6ee;
            color: #4b3a2d;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 1000px;
            margin: 40px auto;
            background-color: #fff7ed;
            border: 1px solid #e2c7ab;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            display: flex;
            gap: 40px;
        }
        .product-image {
            flex: 1;
            text-align: center;
        }
        .product-image img {
            width: 300px;            /* 固定寬度 */
            height: 300px;           /* 固定高度 */
            object-fit: cover;       /* 保持比例裁切，圖片不變形 */
            border-radius: 10px;
            border: 1px solid #ddd0c0;
        }
        .product-info {
            flex: 1;
        }
        h2 {
            color: #8b5c3e;
            margin-bottom: 20px;
        }
        .label {
            margin-top: 15px;
            font-weight: bold;
        }
        .price {
            font-size: 24px;
            margin: 20px 0;
            color: #8b5c3e;
        }
        .stock {
            font-size: 16px;
            margin: 10px 0;
            color: #666;
        }
        button {
            background-color: #c89b7b;
            color: white;
            border: none;
            padding: 10px 24px;
            font-size: 16px;
            border-radius: 24px;
            cursor: pointer;
            margin-top: 20px;
        }
        select, input[type="radio"] {
            margin-right: 10px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            font-size: 16px;
            color: #b08968;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div>
            <a href="index.php" class="back-link">← 返回商品列表</a>
        </div>

        <div class="product-image">
            <?php
            // 從選擇的商品中獲取圖片路徑，若沒有則顯示預設圖片
            $imagePath = $selectedProduct['image_path'] ?? '';

            if (empty($imagePath)) {
                $imagePath = 'goodImage/default.png';
            } elseif (!preg_match('/^https?:\/\//', $imagePath)) {
                // 如果不是 http 或 https 開頭，代表是本地圖片，加上 goodImage/
                $imagePath = 'goodImage/' . $imagePath;
            }
            ?>
        
               <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($selectedProduct['name']) ?>">

        </div>

        <div class="product-info">
            <h2><?= htmlspecialchars($productName) ?></h2>
            <form method="post">
                <div class="label">商品尺寸</div>
                <?php foreach ($sizes as $size): ?>
                    <label>
                        <input type="radio" name="size" value="<?= $size ?>"
                            <?= $size == $selectedSize ? 'checked' : '' ?>
                            onchange="this.form.submit()">
                        <?= $size ?>
                    </label>
                <?php endforeach; ?>

                <div class="label">商品顏色</div>
                <?php foreach ($colors as $color): ?>
                    <label>
                        <input type="radio" name="color" value="<?= $color ?>"
                            <?= $color == $selectedColor ? 'checked' : '' ?>
                            onchange="this.form.submit()">
                        <?= $color ?>
                    </label>
                <?php endforeach; ?>

                <div class="price">NT$<?= $selectedProduct['price'] ?></div>
                <div class="stock">庫存: <?= $stock ?> 件</div>

                <button type="submit" name="add_to_cart">加入購物車</button>
                <input type="hidden" name="update_selection" value="1">
            </form>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
