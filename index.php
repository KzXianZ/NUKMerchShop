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
            background-color: #fdf6ec; /* 溫暖奶油底色 */
            color: #4e342e; /* 暖棕色文字 */
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
            background-color: #e6c3a5; /* 奶茶色 header */
        }

        .search-box input {
            padding: 6px;
            width: 200px;
            border: 1px solid #b08968;
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
            min-height: calc(100vh - 60px); /* 佔滿整體高度 */
        }

        .sidebar {
            width: 200px;
            background-color: #f0d5b6; /* 更柔和的大地橘 */
            padding: 0;
        }

        .sidebar button {
            width: 100%;
            padding: 14px 16px;
            background-color: #d7a86e;
            border: none;
            border-bottom: 1px solid #c28e5c;
            color: white;
            font-size: 16px;
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

        .products {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .products img {
            width: 150px;
            border-radius: 8px;
            border: 1px solid #e0c1a1;
        }

        #category-title {
            font-size: 20px;
            margin-bottom: 10px;
            color: #5d4037;
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
        <button onclick="showCategory('hot')">🔥 熱銷</button>
        <button onclick="showCategory('tops')">👕 上衣</button>
        <button onclick="showCategory('pants')">👖 褲子</button>
        <button onclick="showCategory('bags')">👜 包包</button>
        <button onclick="showCategory('accessories')">💍 飾品</button>
        <button onclick="showCategory('stationery')">✏️ 文具</button>
        <button onclick="showCategory('others')">📦 其他</button>
    </div>

    <!-- 商品顯示區 -->
    <div class="content">
        <h2 id="category-title">請選擇分類</h2>
        <div class="products" id="product-list"></div>
    </div>
</div>

<script>
    function showCategory(category) {
        const productList = document.getElementById("product-list");
        const title = document.getElementById("category-title");

        const products = {
            hot: ["hot1.jpg", "hot2.jpg"],
            tops: ["tops1.jpg", "tops2.jpg"],
            pants: ["pants1.jpg", "pants2.jpg"],
            bags: ["bags1.jpg", "bags2.jpg"],
            accessories: ["accessories1.jpg", "accessories2.jpg"],
            stationery: ["stationery1.jpg", "stationery2.jpg"],
            others: ["others1.jpg", "others2.jpg"]
        };

        title.innerText = "選擇的分類：" + category;
        productList.innerHTML = products[category].map(img => `<img src="${img}" alt="${category}">`).join('');
    }
</script>

</body>
</html>
