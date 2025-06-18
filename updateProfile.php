<?php
session_start();
require 'db.php';  // 已建立 $conn

// 1. 權限檢查
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 2. 取得表單資料
$user_id  = $_SESSION['user_id'];
$name     = $_POST['name'];
$gender   = $_POST['gender'];
$phone    = $_POST['phone'];
$email    = $_POST['email'];
$address  = $_POST['address'];
$password = $_POST['password'];

// 3. 檢查是否有上傳頭像
$avatar = null;
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
    // 確保是圖片檔案
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($_FILES['avatar']['type'], $allowedTypes)) {
        // 生成新的檔案名稱（避免檔名重複）
        $avatar = uniqid('avatar_') . '.' . pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $uploadDir = 'userAvatar/';
        $uploadFile = $uploadDir . $avatar;
        
        // 移動上傳的檔案
        if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile)) {
            echo "頭像上傳失敗，請稍後再試。";
            exit;
        }
    } else {
        echo "請上傳有效的圖片檔案（JPG, PNG, GIF）。";
        exit;
    }
}

// 4. 根據是否有輸入新密碼，決定 SQL
if (!empty($password)) {
    $hashedPw = password_hash($password, PASSWORD_DEFAULT);
    if ($avatar) {
        $sql = "
          UPDATE users 
          SET name=?, gender=?, phone=?, email=?, address=?, password=?, avatar=? 
          WHERE id=?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssssi",
            $name,
            $gender,
            $phone,
            $email,
            $address,
            $hashedPw,
            $avatar,
            $user_id
        );
    } else {
        $sql = "
          UPDATE users 
          SET name=?, gender=?, phone=?, email=?, address=?, password=? 
          WHERE id=?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssi",
            $name,
            $gender,
            $phone,
            $email,
            $address,
            $hashedPw,
            $user_id
        );
    }
} else {
    if ($avatar) {
        $sql = "
          UPDATE users 
          SET name=?, gender=?, phone=?, email=?, address=?, avatar=? 
          WHERE id=?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssi",
            $name,
            $gender,
            $phone,
            $email,
            $address,
            $avatar,
            $user_id
        );
    } else {
        $sql = "
          UPDATE users 
          SET name=?, gender=?, phone=?, email=?, address=? 
          WHERE id=?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssi",
            $name,
            $gender,
            $phone,
            $email,
            $address,
            $user_id
        );
    }
}

// 5. 執行並檢查
if ($stmt->execute()) {
    // 更新 session 中的 email（若有變更）及可選擇性別
    $_SESSION['email']  = $email;
    header("Location: member.php");
    exit;
} else {
    echo "更新失敗：" . htmlspecialchars($stmt->error);
    exit;
}
?>
<?php
session_start();
require 'db.php';  // 已建立 $conn

// 1. 權限檢查
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 2. 取得表單資料
$user_id  = $_SESSION['user_id'];
$name     = $_POST['name'];
$gender   = $_POST['gender'];
$phone    = $_POST['phone'];
$email    = $_POST['email'];
$address  = $_POST['address'];
$password = $_POST['password'];

// 3. 檢查是否有上傳頭像
$avatar = null;
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
    // 確保是圖片檔案
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($_FILES['avatar']['type'], $allowedTypes)) {
        // 生成新的檔案名稱（避免檔名重複）
        $avatar = uniqid('avatar_') . '.' . pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $uploadDir = 'userAvatar/';
        $uploadFile = $uploadDir . $avatar;
        
        // 移動上傳的檔案
        if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile)) {
            echo "頭像上傳失敗，請稍後再試。";
            exit;
        }
    } else {
        echo "請上傳有效的圖片檔案（JPG, PNG, GIF）。";
        exit;
    }
}

// 4. 根據是否有輸入新密碼，決定 SQL
if (!empty($password)) {
    $hashedPw = password_hash($password, PASSWORD_DEFAULT);
    if ($avatar) {
        $sql = "
          UPDATE users 
          SET name=?, gender=?, phone=?, email=?, address=?, password=?, avatar=? 
          WHERE id=?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssssi",
            $name,
            $gender,
            $phone,
            $email,
            $address,
            $hashedPw,
            $avatar,
            $user_id
        );
    } else {
        $sql = "
          UPDATE users 
          SET name=?, gender=?, phone=?, email=?, address=?, password=? 
          WHERE id=?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssi",
            $name,
            $gender,
            $phone,
            $email,
            $address,
            $hashedPw,
            $user_id
        );
    }
} else {
    if ($avatar) {
        $sql = "
          UPDATE users 
          SET name=?, gender=?, phone=?, email=?, address=?, avatar=? 
          WHERE id=?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssi",
            $name,
            $gender,
            $phone,
            $email,
            $address,
            $avatar,
            $user_id
        );
    } else {
        $sql = "
          UPDATE users 
          SET name=?, gender=?, phone=?, email=?, address=? 
          WHERE id=?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssi",
            $name,
            $gender,
            $phone,
            $email,
            $address,
            $user_id
        );
    }
}

// 5. 執行並檢查
if ($stmt->execute()) {
    // 更新 session 中的 email（若有變更）及可選擇性別
    $_SESSION['email']  = $email;
    header("Location: member.php");
    exit;
} else {
    echo "更新失敗：" . htmlspecialchars($stmt->error);
    exit;
}
?>
