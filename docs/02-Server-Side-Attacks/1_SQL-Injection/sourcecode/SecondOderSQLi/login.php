<?php
session_start();
require 'db.php';

// 创建users表（如果不存在）
$sql = "CREATE TABLE IF NOT EXISTS users (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(50) NOT NULL,
password VARCHAR(255) NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->exec($sql);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$username = $_POST['username'];
$password = $_POST['password'];

// 简单的登录验证（存在SQL注入漏洞）
$stmt = $conn->query("SELECT * FROM users WHERE username = '$username' AND password = '$password'");
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
$_SESSION['user'] = $user;
header('Location: index.php');
exit;
} else {
$error = '用户名或密码错误';
}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>登录</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
<a href="index.php">首页</a>
<a href="login.php">登录</a>
<a href="register.php">注册</a>
<a href="post.php">发表文章</a>
</nav>

<h1>登录</h1>
<?php if ($error): ?>
<p style="color: red;"><?= $error ?></p>
<?php endif; ?>

<form method="post">
<div>
<label for="username">用户名:</label>
<input type="text" id="username" name="username" required>
</div>
<div>
<label for="password">密码:</label>
<input type="password" id="password" name="password" required>
</div>
<button type="submit">登录</button>
</form>
</body>
</html>
