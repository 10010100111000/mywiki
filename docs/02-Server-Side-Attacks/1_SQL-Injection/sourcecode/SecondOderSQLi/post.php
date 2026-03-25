<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
header('Location: login.php');
exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$title = $_POST['title'];
$content = $_POST['content'];
$author = $_SESSION['user']['username'];
$hidden_field = 'default_value';

try {
// 保留SQL二次注入漏洞
$sql = "INSERT INTO articles (title, content, author, hidden_field, created_at)
VALUES ('$title', '$content', '$author', '$hidden_field', NOW())";
$conn->exec($sql);

header('Location: index.php');
exit;
} catch (PDOException $e) {
$error = '文章发表失败：' . $e->getMessage();
}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>发表文章</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
<a href="index.php">首页</a>
<a href="login.php">登录</a>
<a href="register.php">注册</a>
<a href="post.php">发表文章</a>
</nav>

<h1>发表文章</h1>
<?php if ($error): ?>
<p style="color: red;"><?= $error ?></p>
<?php endif; ?>

<form method="post">
<div>
<label for="title">标题:</label>
<input type="text" id="title" name="title" required>
</div>
<div>
<label for="content">内容:</label>
<textarea id="content" name="content" required></textarea>
</div>
<button type="submit">发表</button>
</form>
</body>
</html>