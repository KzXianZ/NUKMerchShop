<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    die("你沒有權限進入此頁面");
}
echo "<h2>歡迎管理員 {$_SESSION['email']}</h2>";
?>
<p><a href="logout.php">登出</a></p>