<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    die("你沒有權限進入此頁面");
}

// 資料庫連線
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'nukmerchshop';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("資料庫連線失敗：" . $conn->connect_error);
}

// 時間區間查詢
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$sql = "
    SELECT od.product_name, SUM(od.amount) AS total_amount
    FROM `orders` o
    JOIN order_items od ON o.id = od.order_id
    WHERE o.status = '已送達'
";
if (!empty($start_date) && !empty($end_date)) {
    $sql .= " AND o.created_at BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'";
}
$sql .= " GROUP BY od.product_name";

$result = $conn->query($sql);
$labels = [];
$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['product_name'];
        $data[] = $row['total_amount'];
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>銷售報表</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Noto Sans TC', sans-serif;
            background-color: #f2e2d2;
            color: #4e342e;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
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
        form {
            margin: 20px 0;
        }
        input[type="date"], button {
            padding: 10px 15px;
            font-size: 16px;
            border: 1px solid #b08968;
            border-radius: 6px;
            background-color: #fff;
            margin: 5px;
        }
        button {
            background-color: #b08968;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #a17256;
        }
        .chart-container {
            max-width: 500px;
            margin: 40px auto 0;
        }
        .switch-buttons {
            margin: 20px 0;
        }
        .switch-buttons button {
            margin: 0 10px;
        }
        .back-link {
            margin-top: 40px;
        }
        .back-link a {
            color: #5d4037;
            text-decoration: none;
            font-size: 16px;
            padding: 10px 20px;
            border: 1px solid #b08968;
            border-radius: 5px;
            background-color: #f3d5b5;
            transition: background-color 0.3s, color 0.3s;
        }
        .back-link a:hover {
            background-color: #e4bfa3;
            color: #3e2723;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>銷售報表</h1>

    <!-- 篩選表單 -->
    <form method="get">
        起始日期：
        <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
        結束日期：
        <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
        <button type="submit">查詢</button>
    </form>

    <!-- 切換圖表按鈕 -->
    <div class="switch-buttons">
        <button onclick="switchChart('pie')">圓餅圖</button>
        <button onclick="switchChart('bar')">長條圖</button>
    </div>

    <!-- 圖表畫布 -->
    <div class="chart-container">
        <canvas id="salesChart"></canvas>
    </div>

    <!-- 返回連結 -->
    <div class="back-link">
        <a href="admin.php">← 回到報表選單</a>
    </div>
</div>

<script>
    const chartLabels = <?= json_encode($labels) ?>;
    const chartData = <?= json_encode($data) ?>;
    let chartType = 'pie';

    const ctx = document.getElementById('salesChart').getContext('2d');
    let chart = new Chart(ctx, {
        type: chartType,
        data: {
            labels: chartLabels,
            datasets: [{
                label: '銷售數量',
                data: chartData,
                backgroundColor: [
                    '#ff6384', '#36a2eb', '#ffce56',
                    '#8e44ad', '#2ecc71', '#e67e22', '#1abc9c'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: chartType === 'pie' ? 'bottom' : 'top' },
                title: {
                    display: true,
                    text: '已送達訂單的商品銷售統計'
                }
            },
            scales: chartType === 'bar' ? {
                y: { beginAtZero: true }
            } : {}
        }
    });

    function switchChart(type) {
        chart.destroy();
        chart = new Chart(ctx, {
            type: type,
            data: {
                labels: chartLabels,
                datasets: [{
                    label: '銷售數量',
                    data: chartData,
                    backgroundColor: [
                        '#ff6384', '#36a2eb', '#ffce56',
                        '#8e44ad', '#2ecc71', '#e67e22', '#1abc9c'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: type === 'pie' ? 'bottom' : 'top' },
                    title: {
                        display: true,
                        text: '已送達訂單的商品銷售統計'
                    }
                },
                scales: type === 'bar' ? {
                    y: { beginAtZero: true }
                } : {}
            }
        });
    }
</script>
</body>
</html>
