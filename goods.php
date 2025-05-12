<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'nukmerchshop');
if ($conn->connect_error) {
    die("資料庫連接失敗: " . $conn->connect_error);
}

$productName = $_GET['name'] ?? '';
$productName = urldecode($productName);

$sql = "SELECT * FROM goods WHERE name = ?";
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

$productImages = [
    '高雄大學紀念短褲' => 'https://www.costco.com.tw/medias/sys-master/images/hf1/hb5/259572020936734.jpg',
    '高雄大學紀念T恤' => 'https://ec.blueco.com.tw/Uploads/Images/產品說明/衣服類/衣服款式/圓領圓筒T恤1.jpg',
    '高雄大學20周年紀念鋼筆' => 'https://thumbnail10.coupangcdn.com/thumbnails/remote/492x492ex/image/product/image/vendoritem/2018/09/19/3285925450/761a0af0-716e-4c19-967f-29d4c65b44d3.jpg',
    '高雄大學20周年紀念帆布袋' => 'https://shoplineimg.com/5f4760ee70e52e003f4199b5/5fb5e7b9e3728f003556db6a/800x.jpg',
    '高雄大學紀念外套' => 'https://cdn.store-assets.com/s/774393/i/41986309.jpg?width=1024',
    '高雄大學紀念襯衫' => 'https://img.cloudimg.in/uploads/shops/19623/products/fa/fae2e7c0ab1aa603193e661af35f162c.jpg',
    '高雄大學紀念大學T' => 'https://shoplineimg.com/59551e7e595630172500089b/5e20c41ebae0a200154ba298/800x.jpg?',
    '高雄大學紀念長褲' => 'https://s.yimg.com/zp/MerchandiseImages/D792F653CB-SP-12248789.jpg',
    '高雄大學紀念後背包' => 'https://www.costco.com.tw/medias/sys-master/images/hcc/h27/257772281954334.jpg',
    '高雄大學紀念棒球外套' => 'https://diz36nn4q02zr.cloudfront.net/webapi/imagesV3/Cropped/SalePage/10175889/4/638820261409530000?v=1',
    '高雄大學紀念帽T' => 'https://shoplineimg.com/57a8189d617069559a8e0400/63490d84e2d9bf002bf520de/800x.jpg'
];

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
            max-width: 100%;
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
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #b08968;
            color: white;
            text-decoration: none;
            border-radius: 24px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .back-btn:hover {
            background-color: #8b5c3e;
        }
        .back-btn-container {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div>
            <a href="index.php" class="back-btn">← 返回商品列表</a>
        </div>

        <div class="product-image">
            <img src="<?= htmlspecialchars($productImages[$productName] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($productName) ?>">
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
