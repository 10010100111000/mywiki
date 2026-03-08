<?php
require 'db.php';

if (!isset($_GET['id'])) {
header('Location: index.php');
exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM articles WHERE id = :id");
$stmt->execute(['id' => $id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
header('Location: index.php');
exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title><?= htmlspecialchars($article['title']) ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
<a href="index.php">首页</a>
<a href="login.php">登录</a>
<a href="register.php">注册</a>
<a href="post.php">发表文章</a>
</nav>

<h1><?= htmlspecialchars($article['title']) ?></h1>
<p>作者：<?= htmlspecialchars($article['author']) ?></p>
<p>发布时间：<?= $article['created_at'] ?></p>
<div>
<?= nl2br(htmlspecialchars($article['content'])) ?>
</div>
<hr>
<div>
<strong>隐藏字段值：</strong>
<?= htmlspecialchars($article['hidden_field']) ?>
</div>
</body>
</html>