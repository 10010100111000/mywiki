# 第一题

小明做了一个文件上传的页面，他将危险文件做了个黑名单，以此来杜绝危险文件的上传，但是他忘记了很严重的问题。想办法上传文件，拿到flag。

源码提示：

```php
$is_upload = false;

$msg = null;

if (isset($_POST['submit'])) {

if (file_exists(UPLOAD_PATH)) {

$deny_ext = array(".php",".php5",".php4",".php3",".php2","php1",".html",".htm",".phtml",".pHp",".pHp5",".pHp4",".pHp3",".pHp2","pHp1",".Html",".Htm",".pHtml",".jsp",".jspa",".jspx",".jsw",".jsv",".jspf",".jtml",".jSp",".jSpx",".jSpa",".jSw",".jSv",".jSpf",".jHtml",".asp",".aspx",".asa",".asax",".ascx",".ashx",".asmx",".cer",".aSp",".aSpx",".aSa",".aSax",".aScx",".aShx",".aSmx",".cEr",".sWf",".swf");

$file_name = trim($_FILES['upload_file']['name']);

$file_name = deldot($file_name);//删除文件名末尾的点

$file_ext = strrchr($file_name, '.');

$file_ext = strtolower($file_ext); //转换为小写

$file_ext = trim($file_ext); //收尾去空



if (!in_array($file_ext, $deny_ext)) {

if (move_uploaded_file($_FILES['upload_file']['tmp_name'], UPLOAD_PATH . '/' . $_FILES['upload_file']['name'])) {

$img_path = UPLOAD_PATH . $_FILES['upload_file']['name'];

$is_upload = true;

}

} else {

$msg = '此文件不允许上传!';

}

} else {

$msg = UPLOAD_PATH . '文件夹不存在,请手工创建！';

}

}
```

## write up

本题考察 `.htaccess` 文件上传，只要文件名是这个就通过，垃圾题目



# 第二题

flag.php在根目录下，试着找出来

学会好好利用http://ip:port/include.php界面

## write up

include,php 源码：

```php
<?php
/*
本页面存在文件包含漏洞，用于测试图片马是否能正常运行！
*/
header("Content-Type:text/html;charset=utf-8");
$file = $_GET['file'];
if(isset($file)){
    include $file;
}else{
    show_source(__file__);
}
?>
```



# 第三题

flag.php在根目录下，试试看，怎么找出来

## write up

active mq 的 put方法 可以任意文件上传漏洞

1. 访问 `/admin/test/systemProperties.jsp` 文件 获取`activemq.base 	/opt/activemq`路径
2. 生成jsp木马
3. 使用put 方法，上传木马到 /fileserver/ 目录下，如下图所示：
   ![image-20260402163132405](./image/FileUpload/image-20260402163132405.png)

4. 使用mov 方法，将shell..jsp 移动到admin目录下,并添加请求头 Destination:file:///opt/activemq/webapps/admin/shell.jsp

   ![image-20260402163957501](./image/FileUpload/image-20260402163957501.png)

5. l连接木马，![image-20260402164050473](./image/FileUpload/image-20260402164050473.png)
   ![image-20260402164110924](./image/FileUpload/image-20260402164110924.png)

