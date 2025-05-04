<!-- index.php -->
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>會員登入 / 註冊</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fdf6ec;
            color: #4e342e;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 60px auto;
            background-color: #fff8f0;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #5d4037;
            border-bottom: 2px solid #e6c3a5;
            padding-bottom: 8px;
        }

        form {
            margin-top: 20px;
        }

        input[type="email"],
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0 16px;
            border: 1px solid #b08968;
            border-radius: 6px;
            box-sizing: border-box;
            background-color: #fffaf4;
        }

        input[type="submit"] {
            background-color: #b08968;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #a17256;
        }

        .form-section {
            margin-bottom: 40px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-section">
        <h2>登入</h2>
        <form method="POST" action="login.php">
            電子郵件: <input type="email" name="email" required><br>
            密碼: <input type="password" name="password" required><br>
            <input type="submit" value="登入">
        </form>
    </div>

    <div class="form-section">
        <h2>註冊</h2>
        <form method="POST" action="register.php">
            電子郵件: <input type="email" name="email" required><br>
            電話號碼: <input type="text" name="phone" required><br>
            密碼: <input type="password" name="password" required><br>
            <input type="submit" value="註冊">
        </form>
    </div>
</div>

</body>
</html>
