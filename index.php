<?php
// 連接資料庫
$conn = new mysqli('localhost', 'root', '', 'nukmerchshop');
if ($conn->connect_error) {
    die("資料庫連接失敗: " . $conn->connect_error);
}

// 從資料庫獲取獨特商品列表（按名稱分組）
$sql = "SELECT MIN(no) as no, name FROM goods GROUP BY name";
$result = $conn->query($sql);
$products = [];

// 商品圖片映射表
$productImages = [
    '高雄大學紀念短褲' => 'https://www.costco.com.tw/medias/sys_master/images/hf1/hb5/259572020936734.jpg',
    '高雄大學紀念T恤' => 'https://ec.blueco.com.tw/Uploads/Images/產品說明/衣服類/衣服款式/圓領圓筒T恤1.jpg',
    '高雄大學20周年紀念鋼筆' => 'https://thumbnail10.coupangcdn.com/thumbnails/remote/492x492ex/image/product/image/vendoritem/2018/09/19/3285925450/761a0af0-716e-4c19-967f-29d4c65b44d3.jpg',
    '高雄大學20周年紀念帆布袋' => 'https://shoplineimg.com/5f4760ee70e52e003f4199b5/5fb5e7b9e3728f003556db6a/800x.jpg',
    '高雄大學紀念外套' => 'https://cdn.store-assets.com/s/774393/i/41986309.jpg?width=1024',
    '高雄大學紀念襯衫' => 'https://img.cloudimg.in/uploads/shops/19623/products/fa/fae2e7c0ab1aa603193e661af35f162c.jpg',
    '高雄大學紀念大學T' => 'https://shoplineimg.com/59551e7e595630172500089b/5e20c41ebae0a200154ba298/800x.jpg?',
    '高雄大學紀念長褲' => 'https://s.yimg.com/zp/MerchandiseImages/D792F653CB-SP-12248789.jpg'
];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['no'],
            'name' => $row['name'],
            'image' => $productImages[$row['name']] ?? 'default.jpg'
        ];
    }
}
$conn->close();
?>

<!DOCTYPE html> 
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>高大周邊首頁</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fdf6ec;
            color: #4e342e;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
            background-color: #e6c3a5;
        }

        .search-box input {
            padding: 6px;
            width: 200px;
            border: 3px solid #b08968;
            border-radius: 4px;
        }

        .search-box button {
            padding: 6px 12px;
            background-color: #b08968;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .icons {
            display: flex;
            gap: 10px;
        }

        .icons button {
            background-color: #b08968;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .container {
            display: flex;
            min-height: calc(100vh - 60px);
        }

        .sidebar {
            width: 200px;
            background-color: #f0d5b6;
            padding: 0;
        }

        .sidebar button {
            width: 100%;
            padding: 14px 16px;
            background-color: #d7a86e;
            border: none;
            border-bottom: 1px solid #c28e5c;
            color: white;
            font-size: 15px;
            cursor: pointer;
            text-align: left;
        }

        .sidebar button:hover {
            background-color: #c48a53;
        }

        .content {
            flex-grow: 1;
            padding: 20px;
            background-color: #fff8f0;
        }

        .gallery {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
        }

        .gallery a {
            display: block;
            width: 250px;
            border: 2px solid #e2c7ab;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            text-decoration: none;
            color: #4b3a2d;
            background: white;
        }

        .gallery a:hover {
            transform: scale(1.05);
        }

        .gallery img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            display: block;
        }

        .product-name {
            padding: 15px;
            font-weight: bold;
            text-align: center;
        }

        #category-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: #5d4037;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- 頂部導航 -->
<div class="header">
    <form action="search.php" method="GET" class="search-box">
        <input type="text" name="query" placeholder="搜尋..." required>
        <button type="submit">搜尋</button>
    </form>
    <div class="icons">
        <button onclick="window.location.href='cart.php'">購物車</button>
        <button onclick="window.location.href='member.php'">會員</button>
    </div>
</div>

<!-- 主容器 -->
<div class="container">
    <!-- 商品分類 -->
    <div class="sidebar">
        <button onclick="showCategory('all')">🏠 全部商品</button>
        <button onclick="showCategory('hot')">🔥 熱銷</button>
        <button onclick="showCategory('tops')">👕 上衣</button>
        <button onclick="showCategory('pants')">👖 褲子</button>
        <button onclick="showCategory('bags')">👜 包包</button>
        <button onclick="showCategory('stationery')">✏️ 文具</button>
    </div>

    <!-- 商品顯示區 -->
    <div class="content">
        <h2 id="category-title">全部商品</h2>
        <div class="gallery">
            <?php foreach ($products as $product): ?>
                <a href="goods.php?name=<?= urlencode($product['name']) ?>">
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
    function showCategory(category) {
        const title = document.getElementById("category-title");
        let categoryName = "全部商品";
        
        switch(category) {
            case 'hot': categoryName = "🔥 熱銷商品"; break;
            case 'tops': categoryName = "👕 上衣類"; break;
            case 'pants': categoryName = "👖 褲子類"; break;
            case 'bags': categoryName = "👜 包包類"; break;
            case 'stationery': categoryName = "✏️ 文具類"; break;
        }
        
        title.innerText = categoryName;
        
        // 這裡可以添加AJAX請求來獲取分類商品
        // 目前先顯示全部商品
    }
</script>

</body>
</html>