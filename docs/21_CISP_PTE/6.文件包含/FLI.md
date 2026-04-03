# 第一题

请在系统根目录下找到flag.txt文件并获取flag值

部分源码：

```php
if (isset($_GET['page'])) {

    $page = $_GET['page'];

    include($page);

}
```

## write up

直接在路径后,添加page参数,如`?page=/flag.txt`即可

# 第二题

所谓的文件包含就是指在一个文件中包含另一个文件，小明的网站是存在包含漏洞的，声称目录下就存在flag.php文件，看你怎么拿？

源码提示：

```php
include($_GET['cx'].".php");
```

## write up

该后台拼接了`.php`后缀, 所以我们不能使用完整的文件名,他会在后台拼接为 `flag.php.php` 导致找不到文件![image-20260402110916969](./image/FLI/image-20260402110916969.png)

所以使用 `?x=flag` 即可

# 第三题

管理员设置了访问限制，但flag就在根目录下，你能找到正确的姿势来读取它吗

## write up



![image-20260402111609115](./image/FLI/image-20260402111609115.png)

403 绕过的几种方式：

- 先查看有没有传cookie，cookie里面有没有关键的字段
- 修改请求的方法，如修改为post，put
- 修改referer 
- 添加 x-forwarded-for: 127.0.0.1

最后尝试添加x-forwarded-for 字段后，可以绕过403

![image-20260402112315880](./image/FLI/image-20260402112315880.png)

# 第四题

题目的flag值放在系统根目录下，请利用文件上传和文件包含相关的知识，获取到指定的flag内容。

## write up

给了一个文件上传的界面

![image-20260402113240267](./image/FLI/image-20260402113240267.png)

选择 攻击机中的`C:\Software\网站木马\php\1.php` 通过查看他的内容可以知道他是一个图片马

![image-20260402113621512](./image/FLI/image-20260402113621512.png)

提示不允许上传php文档，除了内容中有php代码之后，我们先修改filename 后缀名，常见的有

- phtml pht phtm
- php3 php4  php5 php6 php7 php8
- phar

![image-20260402114633690](./image/FLI/image-20260402114633690.png)

可以看到都上传成功了，可是不知道上传的路径，查看源代码也没有提示。那么使用burp 自带的字典进行爆破

![image-20260402114815530](./image/FLI/image-20260402114815530.png)

可以看到存在uoploads文件夹，尝试访问之前上传的文件

![image-20260402114915928](./image/FLI/image-20260402114915928.png)

尝试了很多webshell连接器 去连接，都无法执行，

我忽略了很大的一个问题，这个题目还考了文件包含，而我当前没碰到文件包含，所以我还是执着的尝试文件上传webshell

后来我发现我并没有尝试以下的最佳实践：

1. 大小写php绕过
2. 双后缀绕过，比如apache2.x版本中的双后缀解析，遇到不认识的后缀，会从右到左解析后缀

之后尝试`6.php.xxx` 后，尝试连接webshell ，可以通过

#### 回顾

来看以下服务器端的源码，

```php
if(isset($_FILES['file'])){
    $target_dir = "uploads/";  
    $filename = basename($_FILES["file"]["name"]);
//得到扩展名
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
//比较扩展命
    if($extension === "php"){
        echo "错误：不允许上传 PHP 文件！";
        exit;
    }
    $target_file = $target_dir . $filename;

    if(move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)){
        echo "上传成功";
    } else {
        echo "上传失败!";
    }
}
?>
```

连接webshell 后，发现uplaods中原来有一个index.php ![image-20260402125346365](./image/FLI/image-20260402125346365.png)

这个才是文件包含漏洞的php文件，源代码如下：

```php
<?php
if(isset($_GET['page'])){
    // 直接包含用户传入的文件，存在严重漏洞
    include($_GET['page']);
} else {
    echo "请在URL中添加参数，例如：?page=xxx.jpg";
    
}
?>
```

# 第五题

flag.php在根目录下，试着找出来

## write up

本题有点傻逼，提示和题目完全没有关系

![image-20260402132445032](./image/FLI/image-20260402132445032.png)

这题主要考察 php warper 的使用

使用php://filter 来查看源文件，如：

`?cx=php://filter/convert.base64-encode/resource=/flag.php`

![image-20260402141746552](./image/FLI/image-20260402141746552.png)

### 回顾：

看一下源代码：

```php
<?php
    error_reporting(0); // 关闭错误提示，防止暴露路径信息

    // 获取用户传入的参数
    $category = isset($_GET['cx']) ? $_GET['cx'] : '';

    if ($category) {
        // 如果路径中包含 .. 或 /，拒绝请求
        if (strpos($category, '..') !== false) {
            die("非法路径！");
        }

        // 允许通过 php://filter 读取文件内容
        //这里还必须以php://filter开头，所以应该也没有其他的方式了
        if (strpos($category, 'php://filter') === 0) {
            include($category);
        } else {
            die("不允许的文件类型！");
        }
    }
?>
```

strpos（） 函数的作用： 函数查找字符串在另一字符串中第一次出现的位置。对大小写敏感。如果没有找到字符串则返回 FALSE。**注释：**字符串位置从 0 开始，不是从 1 开始。

# 第六题

flag.php在根目录下，去找到它

和上一题很相似的界面

## write up

```php
<?php
    error_reporting(0); // 关闭错误信息，增加难度

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 获取用户 POST 提交的 cx 参数
        $category_encoded = isset($_POST['cx']) ? $_POST['cx'] : '';
        
        // 解码 base64（增加挑战性）
        $category = base64_decode($category_encoded);

        // 直接包含用户传入的路径
        if (!empty($category)) {
            include($category); // 这里存在文件包含漏洞
        }
    }
?>
```

这种题目完全就是傻逼题目，还增加base64 增加挑战性，一点提示都没有，做你妈个逼