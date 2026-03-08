<?php
require 'db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$username = $_POST['username'];
$password = $_POST['password'];

// 检查用户名是否已存在
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);

if ($stmt->rowCount() > 0) {
$error = '用户名已存在';
} else {
// 插入新用户（存在SQL注入漏洞）
$sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
$conn->exec($sql);
header('Location: login.php');
exit;
}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>注册</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
<a href="index.php">首页</a>
<a href="login.php">登录</a>
<a href="register.php">注册</a>
<a href="post.php">发表文章</a>
</nav>

<h1>注册</h1>
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
<button type="submit">注册</button>
</form>
</body>
</html>