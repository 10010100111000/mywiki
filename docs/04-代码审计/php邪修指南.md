# 一. PHP 核心基础

## 1.基础结构

```php
<?php 
      echo 'hello;
 ?>
```

* 所有 PHP 代码，必须写在 <?php 和 ?> 中间,**不区分大小写可以是PHP 或pHP等**
* 每一句 PHP 代码，结尾必须加英文的分号 ` ;`
* echo 类似printf()函数,但他不是函数

### 必知必会

| 标签类型     | 写法                                      | 配置要求                       | PHP 版本支持                    |
| ------------ | ----------------------------------------- | ------------------------------ | ------------------------------- |
| 标准标签     | `<?php...?>`                            | 无需配置                       | 所有版本通用                    |
| 短标签       | `<?...?>`                               | 需开启 `short_open_tag = On` | 所有版本（需配置）              |
| 短输出标签   | `<?=变量或内容?>`                       | PHP 5.4+ 版本默认开启          | PHP 5.4+                        |
| ASP 风格标签 | `<% ... %>` 或 `<%= ... %>`           | 需开启 `asp_tags = On`       | PHP 5.x 及以下；7.0+ 已彻底移除 |
| Script 标签  | `<script language="php"> ... </script>` | 无需配置但已废弃               | 仅极老版本支持                  |

## 2.变量和数据类型

PHP 是弱类型语言：不用提前声明类型，赋值是什么，变量就是什么。

### 2.1 变量命名规则:

* 必须以 $ 开头
* **区分大小写 ,`$a` 和 `$A`不是同一个变量**

### 2.2 数据类型

#### 2.2.1 基本类型

|       类型       |           定义           |     写法     |       例子       |
| :--------------: | :-----------------------: | :-----------: | :---------------: |
|   整型（int）   |    纯整数（正、负、0）    |   不加引号   |   `$a = 123;`   |
| 字符串（string） |      文字 / 混合内容      | 加单 / 双引号 | `$b = 'admin';` |
|  布尔型（bool）  | 只有两个值true或者 flase |   不加引号   |  `$c = true;`  |

#### 2.2.2 数组

// 老派写法
`$fruits = array("apple", "orange");`

| 数组类型 | 键名类型          | 例子                                            | 取值方式          |
| -------- | ----------------- | ----------------------------------------------- | ----------------- |
| 索引数组 | 数字（从 0 开始） | `$arr = ["a", "b"];`                          | `$arr[0]`       |
| 关联数组 | 字符串            | `$user = ["name" => "a","pass" => "123456"];` | `$user['name']` |

#### 2.2.3 null

NULL 是一个特殊的值，表示「变量为空」,
写法：null（不区分大小写，NULL/Null/null 都一样）,不加引号（加了引号就是字符串 "null" 了）

为NULL的情况

| 情况                          | 例子                                           |
| ----------------------------- | ---------------------------------------------- |
| 直接赋值为 NULL               | `$a = null;`                                 |
| 变量未定义                    | 直接用 `$b`，但之前没定义过                  |
| 用 `unset()` 销毁变量       | `$c = 123; unset($c);`                       |
| current($arr)取数组当前元素   | 数组为空，或指针超出范围                       |
| end($arr)取数组最后一个元素,  | 数组为空                                       |
| reset($arr)重置数组指针到开头 | 数组为空                                       |
| preg_match()正则匹配          | 正则表达式错误（不是匹配失败，匹配失败返回 0） |
| mb_substr()截取多字节字符串   | 起始位置超出字符串长度                         |
| json_decode()解析 JSON        | JSON 格式错误                                  |
| unserialize()反序列化         | 数据格式错误                                   |

### 2.3 超全局变量

这些变量是 PHP 内置的数组，在任何地方都能访问，它们承载了用户输入的所有数据,(如果你不知道GET POST 这方面的知识,请先学习html或在HTTP的相关知识)

| 超全局变量                | 含义                      |
| ------------------------- | ------------------------- |
| `$_GET['id']`           | 获取 URL 参数 (`?id=1`) |
| `$_POST['user']`        | 获取 POST 表单数据        |
| `$_REQUEST['x']`        | 同时包含 GET/POST/Cookie  |
| `$_FILES['file']`       | 获取上传的文件            |
| `$_SERVER['HTTP_HOST']` | 获取 HTTP 头信息          |

#### 2.3.1 传递关联数组

```http
?参数名[键名1]=值1&参数名[键名2]=值2&参数名[键名3]=值3
```

例如: 访问以下地址

`test.php?fruit[]=apple&fruit[]=banana&fruit[]=orange`

php代码:

```php
<?php
// test.php
print_r($_GET['fruit']);
var_dump($_GET['fruit']);
?>

//输出
/*
array
(
    [0] => apple
    [1] => banana
    [2] => orange
)
array(3) {
  [0]=>
  string(5) "apple"
  [1]=>
  string(6) "banana"
  [2]=>
  string(6) "orange"
}*/
```

2.3.2 传索引数组传递

```htttp
?参数名[]=值1&参数名[]=值2&参数名[]=值3
```

### 必知必会

1. 访问网址：http://test.com/?id=1&name=admin

```
<?php
$id = $_GET['id'];       // 取到 什么,是什么类型
$name = $_GET['name'];   // 取到 什么,是什么类型
echo $id;
echo $name;
?>
```

2. 提交表单

   ```html
   <form method="post" action="login.php">
       <input name="user" type="text">
       <input name="pass" type="password">
       <input type="submit">
   </form>
   ```

   ```
   <?php
   $user = $_POST['user'];   // 取到表单输入的用户名
   $pass = $_POST['pass'];   // 取到表单输入的密码
   ?>
   ```
3. $_REQUEST   不管用户用 GET 还是 POST 传参，都能接收到

   ```http
   http
   POST /index.php?id=1
   host xxxxxx

   page=10&name=hacker
   ```

   ```php
   <?php
   $id = $_REQUEST['id'];   
   $page = $_REQUEST['page'];  
   $name=$_REQUEST['name']; 
   ?>
   ```
4. $_FILES（文件上传）

   ```
   <!-- upload.html -->
   <form method="post" action="upload.php" enctype="multipart/form-data">
       <!-- 这里的 name="my_file" 是关键！PHP里要用这个名字 -->
       选择文件：<input type="file" name="my_file">
       <br><br>
       <input type="submit" value="上传">
   </form>
   ```

   ```php
   $_FILES = [
   // 第一层键名：就是表单里的 name="my_file"
   "my_file" => [
   // 第二层键名：固定的5个，代表文件的不同信息
   "name"     => "shell.jpg",   // 原始文件名
   "type"     => "image/jpeg",  // MIME类型（浏览器给的，不可信）
   "tmp_name" => "/tmp/php123.tmp", // 服务器上的临时文件路径（最重要！）
   "error"    => 0,              // 错误代码（0=成功）
   "size"     => 102400          // 文件大小（字节）
   ]
   ];
   ```

   ```php
   <?php
   // upload.php

   // 1. 取原始文件名：$_FILES['表单name']['name']
   $file_name = $_FILES['my_file']['name'];
   echo "原始文件名：" . $file_name . "<br>";

   // 2. 取临时文件路径（最重要！后面要用来移动文件）：$_FILES['表单name']['tmp_name']
   $tmp_path = $_FILES['my_file']['tmp_name'];
   echo "临时文件路径：" . $tmp_path . "<br>";

   // 3. 取文件大小：$_FILES['表单name']['size']
   $file_size = $_FILES['my_file']['size'];
   echo "文件大小：" . $file_size . " 字节<br>";

   // 4. 取错误代码：$_FILES['表单name']['error']
   $error = $_FILES['my_file']['error'];
   echo "错误代码：" . $error;
   ?>
   ```
5. $_SERVER（服务器 / 请求头信息）

| 键名                            | 含义           | 审计考点                           |
| ------------------------------- | -------------- | ---------------------------------- |
| `$_SERVER['HTTP_HOST']`       | 请求的主机名   | 可能被篡改，导致 SSRF / 重定向漏洞 |
| `$_SERVER['HTTP_REFERER']`    | 来源页面 URL   | 不可信，容易伪造                   |
| `$_SERVER['HTTP_USER_AGENT']` | 浏览器信息     | 可能被篡改，导致 XSS / 逻辑漏洞    |
| `$_SERVER['REMOTE_ADDR']]`    | 客户端 IP 地址 | 相对可信，但如果有代理可能不准确   |

## 4.条件判断

### 4.1 基本格式

```php
if (条件) {
    // 条件成立执行这里
} 
代码继续执行....
//-------------------
if (条件) {
    // 条件成立执行这里
} else {
    // 条件不成立执行这里
}
代码继续执行....
//-------------------
if (条件1) {
    // 条件1成立执行
} elseif (条件2) {
    // 条件2成立执行
} else {
    // 都不成立执行
}
代码继续执行....


```

### 4.2 比较运算符

| 运算符          | 含义                                   | 例子          | 结果      |
| --------------- | -------------------------------------- | ------------- | --------- |
| `==`          | 等于（弱类型比较，只看值，不看类型）   | `1 == "1"`  | `true`  |
| `===`         | 全等于（强类型比较，既看值，又看类型） | `1 === "1"` | `false` |
| `!=` / `<>` | 不等于（弱类型）                       | `1 != "2"`  | `true`  |
| `!==`         | 不全等于（强类型）                     | `1 !== "1"` | `true`  |
| `<`           | 小于                                   | `1 < 2`     | `true`  |
| `>`           | 大于                                   | `2 > 1`     | `true`  |
| `<=`          | 小于等于                               | `1 <= 1`    | `true`  |
| `>=`          | 大于等于                               | `2 >= 1`    | `true`  |

### 4.3 必知必会

#### 4.3.1 布尔值自动转换

不要和下面的记混了

| 值的类型 | 具体值                                | 备注                         |
| -------- | ------------------------------------- | ---------------------------- |
| 布尔型   | `false`                             | 假                           |
| 整型     | `0`                                 | 假                           |
| 字符串   | `""`（空字符串）                    | 假                           |
| 数组     | `array()`（空数组）                 | 假                           |
| NULL     | `null`                              | NULL 假                      |
| 字符串   | `"0"`（字符串 0）                   | **唯一的字符串假值！** |
| 整型     | `1` / `-1` / `123`              | 非 0 整数都是真              |
| 字符串   | `"1"` / `"abc"` / `" "`（空格） | 非空且非 "0" 的字符串真      |
| 数组     | `array(1)` / `array("a")`         | 非空数组 真                  |

#### 4.3.2 弱类型比较 ==

先把两边转成相同类型（通常是数字），再比较值

| 弱比较              | 结果     | 原因                           |
| ------------------- | -------- | ------------------------------ |
| `0 == "abc"`      | `true` | 字符串转数字是 0               |
| `0 == "0e123456"` | `true` | 科学计数法转数字是 0           |
| `1 == "1abc"`     | `true` | 字符串开头是数字，只取数字部分 |

| 弱比较              | 结果      | 原因                |
| :------------------ | --------- | ------------------- |
| `null == 0`       | `true`  | NULL 弱等于 0       |
| `null == ""`      | `true`  | NULL 弱等于空字符串 |
| `null == false`   | `true`  | NULL 弱等于 false   |
| `null == array()` | `true`  | NULL 弱等于空数组   |
| `null == "0"`     | `false` | 注意！这个不相等    |

**NULL 遇空都相等，唯有 字符0 是例外**

请看下面这段代码：

```php
password=_GET['pw'];
if ($password == "0e123456") {
echo "登录成功！";
}
```

小挑战：
如果我知道数据库里存的 MD5 值（或其他哈希）是 "0e987654"，根据 PHP 的弱类型特性，我提交一个什么样的字符串给 $password，可能会导致 == 判断为真？（提示：0e 在 PHP 比较时会被当做科学计数法 0 * 10^n）。

## 5.循环

#### foreach

只遍历「值」

```php
foreach (数组 as $值变量) {
    // 每次循环，$值变量 就是数组里的一个值
}
```

```php
<?php
$fruit = ["apple", "banana", "orange"];
foreach ($fruit as $f) {
    echo $f . "<br>"; // 输出每个水果名
}
?>
```

同时遍历「键 + 值」

```php
foreach (数组 as $键变量 => $值变量) {
    // 每次循环，$键变量=键名，$值变量=值
}
```

```php
<?php
foreach ($_GET as $key => $value) {
    echo "参数名：" . $key . "，参数值：" . $value . "<br>";
}
?>
```

#### while

```
while (条件) {
    // 条件成立就重复执行这里
    // 注意：必须有让条件不成立的代码，否则会无限循环！
}
```

## 6.字符串

字符串的两种定义方式

### 6.1 单引号 ' '

纯文本，不解析变量，不解析转义字符（除了 \' \转单引号、\\ \转反斜杠）

```php
<?php
$name = "admin";
echo 'Hello $name'; // 输出：Hello $name（不解析变量）
echo 'Hello \'admin\''; // 输出：Hello 'admin'（只解析 \'）
?>
```

### 6.2 双引号 " "

解析变量，解析转义字符（\n 换行、\t 制表符、\\" 转双引号等）

```php
<?php
$name = "admin";
echo "Hello $name"; // 输出：Hello admin（解析变量）
echo "Hello \"admin\""; // 输出：Hello "admin"（解析 \"）
?>
```

### 6.3 字符串连接符：点 .

把两个或多个字符串拼接在一起。

```php
<?php
$a = "Hello";
$b = "World";
echo $a . " " . $b; // 输出：Hello World

// 拼接超全局变量（SQL注入场景！）
$id = $_GET['id'];
$sql = "SELECT * FROM users WHERE id = " . $id;
// 攻击者输入 ?id=1 OR 1=1，拼接后就是：
// SELECT * FROM users WHERE id = 1 OR 1=1 → SQL注入！
?>
```

## 7.函数

### 7.2 基本格式

```php
function 函数名(参数1, 参数2, ...) {
    // 函数体：要执行的代码
    return 返回值; // 可选，没有return就返回 NULL
}
```

### 7.1 字符串函数

#### strlen()

- **用法**：`strlen($str)`
- **作用**：获取字符串的字节长度
- **返回值**：成功返回整数（字节数），失败/传数组返回 NULL
- **例子**：

  ```php
  <?php
  echo strlen("admin"); // 输出：5
  ?>
  ```

#### strpos()

- **用法**：`strpos($haystack, $needle, $offset)`
- **作用**：查找子字符串第一次出现的位置（默认 从0开始，大小写敏感）
- **返回值**：成功返回整数（位置），没找到返回 false，传数组返回 NULL
- **例子**：

  ```php
  <?php
  echo strpos("Hello admin", "admin"); // 输出：6（"admin"从第6位开始）
  ?>
  ```

绕过

如果开发者用 == 而不是 === 来判断「没找到」，当子字符串在位置 0 时，0 == false 为 true，会被误判为「没找到」，从而绕过！

```php
<?php
$id = $_GET['id'];
// 错误写法：用 == 判断
if (strpos($id, "OR") == false) {
    echo "通过（没找到 OR）";
} else {
    die("被过滤！");
}
?>
```

#### strstr()

- **用法**：`strstr($haystack, $needle)`
- **作用**：查找子字符串并返回该子字符串及之后的部分（大小写敏感）
- **返回值**：成功返回字符串片段，没找到返回 false，传数组返回 NULL
- **例子**：

  ```php
  <?php
  echo strstr("Hello admin", "admin"); // 输出：admin
  ?>
  ```

#### stristr()

- **用法**：`stristr($haystack, $needle)`
- **作用**：同 strstr()，但**大小写不敏感**
- **返回值**：同 strstr()
- **例子**：

  ```php
  <?php
  echo stristr("Hello Admin", "admin"); // 输出：Admin（不区分大小写）
  ?>
  ```

#### substr()

- **用法**：`substr($str, $start, $length)`（$length 可选）
- **作用**：从 $start 位置截取 $length 个字符（不写 $length 截到末尾）
- **返回值**：成功返回截取的字符串，失败返回 false
- **例子**：

  ```php
  <?php
  echo substr("Hello admin", 6, 5); // 输出：admin（从第6位开始截5个）
  ?>
  ```

---

#### str_replace()

- **用法**：`str_replace($search, $replace, $str)`
- **作用**：把 $str 中的 $search 替换成 $replace
- **返回值**：返回替换后的字符串
- **例子**：

  ```php
  <?php
  echo str_replace("OR", "", "1 OR 1=1"); // 输出：1  1=1（把"OR"换成空）
  ?>
  ```

---

#### preg_match()

- **用法**：`preg_match($pattern, $subject, $matches, $flags, $offset)`
- **作用**：用pattern(正则表达式) 匹配subject(目标字符)
- **返回值**：匹配成功返回 1，失败返回 0，错误/传数组返回 false
- **例子**：

  ```php
  <?php
  echo preg_match("/OR/", "1 OR 1=1"); // 输出：1（匹配到"OR"）
  ?>
  ```

| 参数         | 是否必选 | 作用                                                         |
| ------------ | -------- | ------------------------------------------------------------ |
| `$pattern` | ✅ 必选  | 正则表达式（必须用分隔符包裹，比如 `/正则/`）              |
| `$subject` | ✅ 必选  | 要匹配的目标字符串（通常是用户可控的 `$_GET`/`$_POST`）  |
| `$matches` | ❌ 可选  | 数组，用来存储匹配到的内容（`$matches[0]` 是完整匹配结果） |
| `$flags`   | ❌ 可选  | 匹配标志（CTF 里很少用）                                     |
| `$offset`  | ❌ 可选  | 从目标字符串的第几个字符开始匹配（默认 0）                   |

##### 1. 分隔符

正则表达式必须用**非字母数字、非反斜杠**的字符包裹，常用的有：

- `/正则/`（最常用）
- `#正则#`（当正则里有 `/` 时用，避免转义）
- `~正则~`（同上）

##### 2. 常用元字符（匹配特定字符）

| 元字符 |                            作用                            |                        例子                        |
| :----: | :--------------------------------------------------------: | :-------------------------------------------------: |
| `.` |    匹配**任意单个字符**（默认不匹配换行 `\n`）    |      `/a.c/` 匹配 `abc`、`a1c`、`a!c`      |
| `^` |                  匹配**字符串开头**                  | `/^admin/` 匹配 `admin123`，不匹配 `123admin` |
| `$` |                  匹配**字符串结尾**                  | `/admin$/` 匹配 `123admin`，不匹配 `admin123` |
| `\d` |                 匹配**数字**（0-9）                 |           `/\d+/` 匹配 `123`、`456`           |
| `\w` |    匹配**字母、数字、下划线**（a-z、A-Z、0-9、_）    |            `/\w+/` 匹配 `admin_123`            |
| `\s` | 匹配**空白字符**（空格、制表符 `\t`、换行 `\n`） |    `/a\s+b/` 匹配 `a b`、`a  b`、`a\nb`    |
| `\` |              转义字符（让元字符变成普通字符）              |      `/a\.c/` 匹配 `a.c`（不匹配 `abc`）      |

##### 3. 常用量词（匹配字符出现的次数）

|   量词   |                        作用                        |                        例子                        |
| :-------: | :------------------------------------------------: | :-------------------------------------------------: |
|   `*`   | 匹配**0 次或多次**（贪婪匹配，尽可能多匹配） |    `/a*/` 匹配 `""`、`a`、`aa`、`aaa`    |
|   `+`   |              匹配**1 次或多次**              | `/a+/` 匹配 `a`、`aa`、`aaa`，不匹配 `""` |
|   `?`   |             匹配**0 次或 1 次**             |             `/a?/` 匹配 `""`、`a`             |
|  `{n}`  |              匹配**恰好 n 次**              |               `/a{3}/` 匹配 `aaa`               |
| `{n,}` |              匹配**至少 n 次**              |          `/a{2,}/` 匹配 `aa`、`aaa`          |
| `{n,m}` |         匹配**至少 n 次，最多 m 次**         |     `/a{2,4}/` 匹配 `aa`、`aaa`、`aaaa`     |

##### 4. 常用修饰符

修饰符加在正则分隔符**后面**，用来改变匹配规则，比如 `/正则/i`。

| 修饰符 |                      作用                      |               CTF 考点               |                          例子                          |
| :----: | :--------------------------------------------: | :----------------------------------: | :----------------------------------------------------: |
| `/i` |                  大小写不敏感                  |      没加 `/i` 时用大小写绕过      |     `/OR/i` 匹配 `OR`、`Or`、`oR`、`or`     |
| `/s` |            让 `.` 匹配换行 `\n`            | 没加 `/s` 时用 `%0a`（换行）绕过 |        `/a.c/s` 匹配 `a\nb`（换行也能匹配）        |
| `/m` | 多行匹配（`^` 匹配行开头，`$` 匹配行结尾） |             结合换行绕过             |           `/^admin$/m` 匹配 `admin\n123`           |
| `/U` |           非贪婪匹配（尽可能少匹配）           |          改变贪婪匹配的行为          | `/a.*?b/` 匹配 `aab` 中的 `ab`（而不是 `aab`） |

例子 1：过滤 SQL 注入的 OR

```php
<?php
$id = $_GET['id'];
// 正则：匹配 OR（大小写不敏感）
if (preg_match("/OR/i", $id)) {
    die("SQL注入被过滤！");
}
echo "通过";
?>
```

例子 2：过滤文件后缀 php

```php
<?php
$file = $_GET['file'];
// 正则：匹配以 .php 结尾的字符串
if (preg_match("/\.php$/", $file)) {
    die("php文件被禁止！");
}
echo "通过";
?>
```

例子 3：过滤 XSS 的 script 标签

```php
<?php
$xss = $_GET['xss'];
// 正则：匹配 <script>（大小写不敏感）
if (preg_match("/<script>/i", $xss)) {
    die("XSS被过滤！");
}
echo "通过";
?>
```

##### 1. 数组绕过

`preg_match()` 处理数组时会返回 `false`，从而绕过检查。

- **攻击方法**：传数组 `?id[]=1`
- 代码验证

  ```php
  <?php
  $id = $_GET['id'];
  if (!preg_match("/OR/i", $id)) {
      echo "通过";
  }
  // 传 ?id[]=1，preg_match() 返回 false，!false 为 true，绕过！
  ?>
  ```

##### 2. 换行绕过（针对没加 `/s` 的正则）

如果正则没加 `/s`，`.` 不匹配换行 `%0a`，可以在 payload 里加换行绕过。

- **攻击方法**：传 `?id=1%0aOR 1=1`
- 代码验证

  ```php
  <?php
  $id = $_GET['id'];
  // 正则：匹配 1.OR（没加 /s，. 不匹配换行）
  if (preg_match("/1.OR/i", $id)) {
      die("被过滤！");
  }
  echo "通过";
  // 传 ?id=1%0aOR 1=1，. 不匹配换行，绕过！
  ?>
  ```

##### 3. 大小写绕过（针对没加 `/i` 的正则）

如果正则没加 `/i`，是大小写敏感的，用大小写混合绕过。

- **攻击方法**：传 `?id=1 Or 1=1`（把 OR 改成 Or）
- 代码验证

  ```php
  <?php
  $id = $_GET['id'];
  // 正则：匹配 OR（没加 /i，大小写敏感）
  if (preg_match("/OR/", $id)) {
      die("被过滤！");
  }
  echo "通过";
  // 传 ?id=1 Or 1=1，Or 不匹配 OR，绕过！
  ?>
  ```

##### 4. PCRE 回溯绕过（针对复杂正则）

如果正则很复杂（比如有很多 `*`、`+`），传超长 payload 会导致正则回溯次数超过 PHP 限制（默认 1000000），`preg_match()` 返回 `false`，从而绕过。

- **攻击方法**：传超长的重复字符（比如 `?id=str_repeat("a", 1000000)`）

---

#### addslashes()

- **用法**：`addslashes($str)`
- **作用**：在 '、"、\、NULL 前添加反斜杠 \
- **返回值**：返回转义后的字符串
- **例子**：

  ```php
  <?php
  echo addslashes("admin' OR 1=1"); // 输出：admin\' OR 1=1（在'前加\）
  ?>
  ```

---

#### htmlspecialchars()

- **用法**：`htmlspecialchars($str)`
- **作用**：把 &、<、>、" 转换成 HTML 实体（防 XSS）
- **返回值**：返回编码后的字符串
- **例子**：

  ```php
  <?php
  echo htmlspecialchars("<script>alert(1)</script>");
  // 输出：<script>alert(1)</script>
  ?>
  ```

---

#### base64_encode()

- **用法**：`base64_encode($str)`
- **作用**：把字符串编码成 Base64
- **返回值**：返回 Base64 编码字符串
- **例子**：

  ```php
  <?php
  echo base64_encode("admin"); // 输出：YWRtaW4=
  ?>
  ```

---

#### base64_decode()

- **用法**：`base64_decode($str)`
- **作用**：把 Base64 字符串解码
- **返回值**：返回解码后的原始字符串，失败返回 false
- **例子**：

  ```php
  <?php
  echo base64_decode("YWRtaW4="); // 输出：admin
  ?>
  ```

---

#### md5()

- **用法**：`md5($str)`
- **作用**：计算字符串的 MD5 哈希值
- **返回值**：返回 32 位十六进制字符串，传数组返回 NULL
- **例子**：

  ```php
  <?php
  echo md5("admin"); // 输出：21232f297a57a5a743894a0e4a801fc3
  ?>
  ```

# 8. 序列化和反序列化

## 8.1 什么是序列化和反序列化?

序列化：把 PHP 的变量（数组、对象等）打包成一个字符串（方便存储、传输）
反序列化：把序列化后的字符串拆包还原成原来的 PHP 变量

## 8.2 函数

**serialize ()**：序列化（打包） 把序列化字符串还原成原来的变量 成功返回还原后的变量，失败返回 false

* `$var`：必选，要序列化的变量
* `$options`：可选，PHP 7.0+ 支持，比如 SERIALIZE_PREFER_IGNORE（忽略不可序列化的属性）

  ```php
  <?php
  // 序列化数组
  $arr = ["apple", "banana"];
  echo serialize($arr);
  // 输出：a:2:{i:0;s:5:"apple";i:1;s:6:"banana";}
  ?>
  ```

**unserialize ()**：反序列化（拆包）

* `$str`：必选，要反序列化的字符串
* `$options`：可选，PHP 7.0+ 支持，比如 allowed_classes（允许反序列化的类，默认允许所有）

```php
<?php
// 反序列化数组
$str = 'a:2:{i:0;s:5:"apple";i:1;s:6:"banana";}';
$arr = unserialize($str);
print_r($arr);
// 输出：Array ( [0] => apple [1] => banana )
?>
```

## 8.3 序列化后的格式

| 数据类型 | 格式                                                                     | 例子                                                                 |
| -------- | ------------------------------------------------------------------------ | :------------------------------------------------------------------- |
| 字符串   | `s:长度:"内容";`                                                       | `s:5:"apple";`（字符串 "apple"，长度 5）                           |
| 整数     | `i:数字;`                                                              | `i:123;`（整数 123）                                               |
| 索引数组 | `a:元素个数:{键1;值1;键2;值2;...}`                                     | `a:2:{i:0;s:3:"app";i:1;s:2:"ba";}`（2 个元素的数组）              |
| 关联数组 | `a:元素个数:{键1;值1;键2;值2;...}`                                     | `a:2:{s:4:"name";s:2:"ad";s:4:"pass";s:4:"1234";}`2 个元素的数组） |
| 对象     | `O:类名长度:"类名":属性个数:{属性名类型;属性名;属性值类型;属性值;...}` | 见下面例子                                                           |

```php
<?php
// 定义一个类
class User {
    // 三种属性
    public $name = "admin";      // public（公开）
    private $pass = "123456";    // private（私有）
    protected $email = "test@test.com"; // protected（保护）
}

// 序列化对象
$user = new User();
echo serialize($user);


?>
```

输出为:

```php
O:4:"User":3:{
    s:4:"name";s:5:"admin";
    s:10:"\0User\0pass";s:6:"123456";
    s:8:"\0*\0email";s:13:"test@test.com";
}
```

| 属性类型                    | 序列化时的属性名格式                               | 长度计算规则                            |
| --------------------------- | -------------------------------------------------- | --------------------------------------- |
| **public（公开）**    | 直接写属性名：`s:长度:"属性名";`                 | 只算属性名本身的长度                    |
| **private（私有）**   | 加 `\0类名\0` 前缀：`s:长度:"\0类名\0属性名";` | 1（\0）+ 类名长度 + 1（\0）+ 属性名长度 |
| **protected（保护）** | 加 `\0*\0` 前缀：`s:长度:"\0*\0属性名";`       | 1（\0）+ 1（*）+ 1（\0）+ 属性名长度    |

\0 是 NULL 字节，在 **URL 里传输时**必须编码成 %00，否则会失效！
比如 private 属性的 \0User\0pass → URL 里写成 %00User%00pass

## 8.4 魔术方法

### __construct()

构造函数（创建对象时自动触发）

作用:当你**创建一个新对象**时，自动执行，用来**初始化对象**（比如给属性赋值）。

触发时机:`$obj = new ClassName();` 时

例子:

```php
<?php
class User {
    public $name;
  
    // 构造函数：创建对象时自动把名字设为 "admin"
    public function __construct() {
        $this->name = "admin";
        echo "对象创建成功！<br>";
    }
}

// 创建对象（自动触发 __construct()）
$user = new User();
echo $user->name; // 输出：对象创建成功！ admin
?>
```

---

### __destruct()

析构函数（对象销毁时自动触发）

作用:当对象**被销毁**时（比如代码执行完、手动删除对象），自动执行，用来**清理资源**（比如关闭文件、删除临时文件）。

触发时机

- 代码执行完，对象自动销毁时
- 用 `unset($obj)` 手动删除对象时

例子

```php
<?php
class User {
    public $name;
  
    // 构造函数：创建对象时自动执行
    public function __construct() {
        echo "对象创建了！<br>";
    }
  
    // 析构函数：对象销毁时自动执行
    public function __destruct() {
        echo "对象销毁了！<br>";
    }
}

// 1. 创建对象（自动触发 __construct()）
$user = new User();
echo "中间代码执行中...<br>";

// 2. 代码执行完，对象自动销毁（自动触发 __destruct()）
?>
```

输出结果

```txt
对象创建了！
中间代码执行中...
对象销毁了！
```

---

### __sleep()

序列化前自动触发

作用:当你**调用 `serialize()` 序列化对象**前，自动执行，用来**选择要序列化的属性**（比如只序列化重要的属性，不序列化密码）。

触发时机:`serialize($obj)` 时

要求: 必须返回一个**数组**，包含要序列化的属性名

例子:

```php
<?php
class User {
    public $name = "admin";
    public $pass = "123456"; // 密码，不想序列化
  
    // 只序列化 $name，不序列化 $pass
    public function __sleep() {
        return ["name"]; // 返回要序列化的属性名数组
    }
}

$user = new User();
echo serialize($user);
// 输出：O:4:"User":1:{s:4:"name";s:5:"admin";}（只有 $name，没有 $pass）
?>
```

---

### __wakeup()

反序列化后自动触发

作用:当你**调用 `unserialize()` 反序列化对象**后，自动执行，用来**重新初始化对象**（比如重新连接数据库）。

触发时机:`unserialize($str)` 时

例子:

```php
<?php
class User {
    public $name;
  
    // 反序列化后自动把名字改成 "admin"
    public function __wakeup() {
        $this->name = "admin";
        echo "反序列化成功！<br>";
    }
}

// 序列化字符串（原来的名字是 "test"）
$str = 'O:4:"User":1:{s:4:"name";s:4:"test";}';

// 反序列化（自动触发 __wakeup()）
$user = unserialize($str);
echo $user->name; // 输出：反序列化成功！ admin（名字被改成了 admin）
?>
```

---

### __toString()

把对象当成字符串时自动触发

作用: 当你**把对象当成字符串使用**时（比如 `echo $obj`），自动执行，用来**返回对象的字符串表示**。

触发时机:`echo $obj`、`print $obj` 时

要求:必须返回一个**字符串**

例子

```php
<?php
class User {
    public $name = "admin";
  
    // 把对象当成字符串时，返回名字
    public function __toString() {
        return "用户名字：" . $this->name;
    }
}

$user = new User();
echo $user; // 输出：用户名字：admin（自动触发 __toString()）
?>
```

# 8.5 反序列化漏洞基本原理

核心逻辑

如果：

1. 开发者**把用户可控的变量传给 `unserialize()`**
2. 且**类的魔术方法里有危险操作**（比如文件读写、命令执行）

攻击者就可以**构造恶意序列化字符串**，触发魔术方法里的危险操作！

### 漏洞例子

```php
<?php
// 漏洞类：__destruct() 里有删除文件的危险操作
class DeleteFile {
    public $filename = "test.txt"; // 要删除的文件名
  
    // 析构函数：对象销毁时自动删除文件
    public function __destruct() {
        echo "正在删除文件：" . $this->filename . "<br>";
        unlink($this->filename); // 删除文件（危险操作！）
    }
}

// 漏洞代码：用户可控的 unserialize()
$str = $_GET['str']; // 用户通过 URL 传参
unserialize($str); // 反序列化用户传的字符串
?>
```

攻击步骤

步骤1：构造恶意序列化字符串

我们写一段代码，把 `$filename` 改成要删除的文件（比如 `index.php`）：

```php
<?php
class DeleteFile {
    public $filename = "index.php"; // 改成要删除的文件
}

$obj = new DeleteFile();
echo serialize($obj);
// 输出：O:10:"DeleteFile":1:{s:8:"filename";s:9:"index.php";}
?>
```

步骤2：访问漏洞 URL

把构造好的序列化字符串通过 URL 传给漏洞代码：

```
你的网址?str=O:10:"DeleteFile":1:{s:8:"filename";s:9:"index.php";}
```

步骤3：自动触发漏洞

1. `unserialize($str)` → 反序列化，创建 `DeleteFile` 对象
2. 代码执行完 → 对象销毁，**自动触发 `__destruct()`**
3. `__destruct()` 执行 → 删除 `index.php`

### 实际挑战

TODO

# 9 json

JSON 是一种文本格式，用来表示 对象（对象/字典）和数组 的数据结构。它源于 JavaScript，但已广泛应用于多种编程语言的数据交换。JSON 的结构简单、易读、易解析，常用于 Web API 数据传输、配置文件以及序列化数据存储。

## 语法规则

**JSON 顶层可以是任意 JSON 类型. 键值对之间用 `: `分割,不同的数据之间用逗号`,`分开**

JSON 支持的基本类型包括 字符串（String）、数字（Number）、布尔值（Boolean）、数组（Array）、对象（Object）、空值（null）。字符串必须使用双引号 "。

基本数据类型 (Simple Types)

- **字符串 (String)**：必须使用**双引号**括起来的 Unicode 字符序列，支持反斜杠转义。
- **数字 (Number)**：包括整数和浮点数（如 `42`, `3.14`, `1.0e+2`），不支持八进制和十六进制。
- **布尔值 (Boolean)**：仅包含 `true` 或 `false` 两个字面值。
- **空值 (Null)**：表示空或无值，写作 `null`。

结构化数据类型 (Structured Types)

- **对象 (Object)**：无序的“键/值对”集合，以 `{` 开始，以 `}` 结束。**键（Key）必须是双引号括起来的字符串**，值（Value）可以是任意合法的 JSON 类型。
- **数组 (Array)**：值的有序集合，以 `[` 开始，以 `]` 结束。其中的元素可以是不同类型的 JSON 数据，甚至嵌套其他数组或对象。

对象（Object）：由 {} 包含，内部包含一组键值对（key-value），键必须是字符串，值可以是字符串、数字、布尔值、数组、对象或 null。例如：

```json
{
"name": "张三",
"age": 28,
"isStudent": false
}
```

数组（Array）：由 [] 包含，内部是有序的值列表，每个值可以是任意 JSON 数据类型。例子：

```json
[
"苹果",
"香蕉",
"橙子"
]
```

# 10 json函数

## json_encode()

将 PHP 变量转换为 JSON 字符串,该函数用于将数组或对象编码成符合 JSON 格式的字符串。

语法: `json_encode(mixed $value, int $flags = 0, int $depth = 512) `
返回值: 格式化的json字符串 或false

## json_decode()

将 JSON 字符串转换为 PHP 变量,该函数用于解析 JSON 字符串，默认转换为 PHP 的 stdClass 对象。

语法：`json_decode(string $json, ?bool $associative = null, int $depth = 512, int $flags = 0)`

返回值: 成功时,根据传入的数据返回相应的数据类型的变量 ,失败返回null

关键参数 (`$associative`)：
设置为 true 时，JSON 对象将被转换为 关联数组 而非对象。
通常在需要遍历数据或习惯数组操作时，建议设为 true。

## 例子

```php
<?php
// ======================
// 一、json_encode：PHP变量 → JSON字符串
// ======================

// 1. 字符串
$str = "CISP";
json_encode($str);       // 结果："CISP"

// 2. 数字
$num = 666;
json_encode($num);       // 结果：666

// 3. 布尔值
$bool = true;
json_encode($bool);      // 结果：true

// 4. 空值 null
$null = null;
json_encode($null);      // 结果：null

// 5. 索引数组
$arr = [10, 20, 30];
json_encode($arr);
// 格式化结果：
// [
//   10,
//   20,
//   30
// ]

// 6. 关联数组 (JSON对象)
$user = ["name" => "admin", "age" => 18];
json_encode($user);
// 格式化结果：
// {
//   "name": "admin",
//   "age": 18
// }

// ======================
// 二、json_decode：JSON字符串 → PHP变量
// ======================

// 1. 解码字符串
json_decode('"CISP"');   // 结果：string(4) "CISP"

// 2. 解码数字
json_decode('666');      // 结果：int(666)

// 3. 解码布尔
json_decode('true');     // 结果：bool(true)

// 4. 解码 null
json_decode('null');     // 结果：NULL

// 5. 解码数组
json_decode('[10,20,30]');
// 结果：array(3) { [0]=> 10, [1]=> 20, [2]=> 30 }

// 6. 解码对象 (加true转数组)
json_decode('{"name":"admin","age":18}', true);
// 结果：array(2) { ["name"]=> "admin", ["age"]=> 18 }
?>
```
