<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    die("你沒有權限進入此頁面");
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>管理員後台</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2e2d2;
            color: #4e342e;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 700px;
            margin: 80px auto;
            padding: 40px;
            background-color: #f5e0c8;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            color: #5d4037;
        }
        .button-group {
            display: flex;
            flex-direction: column;
            gap: 30px;
            margin-top: 50px;
            align-items: center;
        }
        .card {
            background-color: #f3d5b5;
            padding: 20px;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card a {
            display: block;
            padding: 15px 0;
            background-color: #b08968;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 18px;
        }
        .card a:hover {
            background-color: #a17256;
        }
        .description {
            margin-top: 12px;
            font-size: 15px;
            color: #6e4b3a;
        }
        .logout {
            margin-top: 50px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>歡迎管理員 <?php echo htmlspecialchars($_SESSION['email']); ?></h2>

    <div class="button-group">
        <div class="card">
            <a href="product_manage.php">商品管理</a>
            <div class="description">新增、修改、刪除商品資訊</div>
        </div>
        <div class="card">
            <a href="view_orders.php">訂單管理</a>
            <div class="description">查看訂單與更新配送狀態</div>
        </div>
        <div class="card">
            <a href="view_users.php">使用者管理</a>
            <div class="description">瀏覽會員資料</div>
        </div>
        <div class="card">
            <a href="sales_report.php">報表分析</a>
            <div class="description">檢視銷售數據</div>
        </div>
    </div>

    <div class="logout">
        <a href="logout.php">登出</a>
    </div>

    <div class="logout" style="margin-top: 20px;">
    <a href="index.php">回到主頁</a>
</div>
</div>
</body>
</html>