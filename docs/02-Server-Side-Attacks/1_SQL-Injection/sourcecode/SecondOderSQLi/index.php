<?php
require 'db.php';

// 创建articles表（如果不存在）
$sql = "CREATE TABLE IF NOT EXISTS articles (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
title VARCHAR(255) NOT NULL,
content TEXT NOT NULL,
author VARCHAR(100),
hidden_field VARCHAR(255),
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->exec($sql);

// 获取所有文章列表
$stmt = $conn->prepare("SELECT id, title, content, author, hidden_field, created_at FROM articles ORDER BY created_at DESC");
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
<title>文章系统</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
<a href="index.php">首页</a>
<a href="login.php">登录</a>
<a href="register.php">注册</a>
<a href="post.php">发表文章</a>
<a href="view.php">查看文章</a>
</nav>

<h1>最新文章</h1>
<?php foreach ($articles as $article): ?>
<div class="article">
<h2><a href="view.php?id=<?= $article['id'] ?>" style="text-decoration: underline; color: blue; cursor: pointer;"><?= htmlspecialchars($article['title']) ?></a></h2>
<p><?= nl2br(htmlspecialchars($article['content'])) ?></p>
<p class="meta">作者: <?= htmlspecialchars($article['author']) ?> | 时间: <?= $article['created_at'] ?></p>
<?php if (isset($_SESSION['user']) && $_SESSION['user']['username'] === $article['author']): ?>
<form method="post" action="delete_article.php" onsubmit="return confirm('确定要删除这篇文章吗？');">
<input type="hidden" name="article_id" value="<?= $article['id'] ?>">
<button type="submit" class="btn btn-danger btn-sm">删除</button>
</form>
<?php endif; ?>
</div>
<?php endforeach; ?>
</body>
</html>