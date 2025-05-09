<?php
// 連接資料庫
$conn = new mysqli('localhost', 'root', '', 'nukmerchshop');
if ($conn->connect_error) {
    die("資料庫連接失敗: " . $conn->connect_error);
}

// 定義商品分類映射
$categories = [
    'tops' => ['高雄大學紀念T恤', '高雄大學紀念襯衫', '高雄大學紀念大學T', '高雄大學紀念帽T','高雄大學紀念棒球外套'],
    'pants' => ['高雄大學紀念短褲', '高雄大學紀念長褲'],
    'bags' => ['高雄大學20周年紀念帆布袋', '高雄大學紀念後背包'],
    'stationery' => ['高雄大學20周年紀念鋼筆'],
    'hot' => ['高雄大學紀念T恤', '高雄大學紀念短褲'], // 假設這些是熱銷商品
    'all' => [] // 全部商品
];

// 獲取當前選擇的分類 (從URL參數)
$currentCategory = $_GET['category'] ?? 'all';

// 構建SQL查詢
if ($currentCategory === 'all') {
    $sql = "SELECT MIN(no) as no, name FROM goods GROUP BY name";
    $stmt = $conn->prepare($sql);
} else {
    // 只查詢屬於當前分類的商品
    $placeholders = implode(',', array_fill(0, count($categories[$currentCategory]), '?'));
    $sql = "SELECT MIN(no) as no, name FROM goods WHERE name IN ($placeholders) GROUP BY name";
    $stmt = $conn->prepare($sql);
    // 綁定參數
    $types = str_repeat('s', count($categories[$currentCategory]));
    $stmt->bind_param($types, ...$categories[$currentCategory]);
}

$stmt->execute();
$result = $stmt->get_result();
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
    '高雄大學紀念長褲' => 'https://s.yimg.com/zp/MerchandiseImages/D792F653CB-SP-12248789.jpg',
    '高雄大學紀念後背包' => 'https://www.costco.com.tw/medias/sys_master/images/hcc/h27/257772281954334.jpg',
    '高雄大學紀念棒球外套' => 'https://diz36nn4q02zr.cloudfront.net/webapi/imagesV3/Cropped/SalePage/10175889/4/638820261409530000?v=1',
    '高雄大學紀念帽T' => 'https://shoplineimg.com/57a8189d617069559a8e0400/63490d84e2d9bf002bf520de/800x.jpg'
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
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .category-btn {
            width: 100%;
            padding: 14px 16px;
            background-color: #d7a86e;
            border: none;
            border-bottom: 1px solid #c28e5c;
            color: white;
            font-size: 15px;
            cursor: pointer;
            text-align: left;
            text-decoration: none;
        }
        
        .category-btn:hover {
            background-color: #c48a53;
        }
        
        .category-btn.active {
            background-color: #b08968;
            font-weight: bold;
        }

        .content {
            flex-grow: 1;
            padding: 20px;
            background-color: #fff8f0;
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 20px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .gallery a {
            display: block;
            border: 2px solid #e2c7ab;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            text-decoration: none;
            color: #4b3a2d;
            background: white;
            height: 100%;
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
        <a href="?category=all" class="category-btn <?= $currentCategory === 'all' ? 'active' : '' ?>">🏠 全部商品</a>
        <a href="?category=hot" class="category-btn <?= $currentCategory === 'hot' ? 'active' : '' ?>">🔥 熱銷</a>
        <a href="?category=tops" class="category-btn <?= $currentCategory === 'tops' ? 'active' : '' ?>">👕 上衣</a>
        <a href="?category=pants" class="category-btn <?= $currentCategory === 'pants' ? 'active' : '' ?>">👖 褲子</a>
        <a href="?category=bags" class="category-btn <?= $currentCategory === 'bags' ? 'active' : '' ?>">👜 包包</a>
        <a href="?category=stationery" class="category-btn <?= $currentCategory === 'stationery' ? 'active' : '' ?>">✏️ 文具</a>
    </div>

    <!-- 商品顯示區 -->
    <div class="content">
        <h2 id="category-title">
            <?php 
                switch($currentCategory) {
                    case 'hot': echo "🔥 熱銷商品"; break;
                    case 'tops': echo "👕 上衣類"; break;
                    case 'pants': echo "👖 褲子類"; break;
                    case 'bags': echo "👜 包包類"; break;
                    case 'stationery': echo "✏️ 文具類"; break;
                    default: echo "全部商品";
                }
            ?>
        </h2>
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
    // 高亮當前選擇的分類 (備用，PHP已經處理)
    document.addEventListener('DOMContentLoaded', function() {
        const currentCategory = "<?= $currentCategory ?>";
        const buttons = document.querySelectorAll('.category-btn');
        
        buttons.forEach(button => {
            const category = button.getAttribute('href').split('=')[1];
            if (category === currentCategory || 
               (currentCategory === 'all' && category === undefined)) {
                button.classList.add('active');
            }
        });
    });
</script>

</body>
</html>