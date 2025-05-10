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
    <title>使用者管理</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans TC', sans-serif;
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
        h1 {
            color: #5d4037;
        }
        .button-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
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
    </style>
</head>
<body>
<div class="container">
    <h1>使用者管理</h1>

    <div class="button-group">
        <div class="card">
            <a href="view_users.php">查看所有使用者</a>
        </div>
        <div class="card">
            <a href="view_user_cart.php">查看購物車內容</a>
        </div>
    </div>
</div>
</body>
</html>
