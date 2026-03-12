下面是一份**专业化的 ffuf 使用速查与实战参考手册**。
内容按**实际渗透测试流程与使用场景**组织，并结合常用字典仓库 **SecLists**。

结构采用：

- 使用场景
- 命令
- 参数解释
- 实际示例
- 使用说明

从**基础 → 中级 → 高级 → 自动化扫描**逐步递进。

---

# 一、ffuf 基础结构

## 1. 基本命令结构

```
ffuf -u <URL> -w <WORDLIST> [options]
```

### 参数说明

| 参数     | 说明             |
| -------- | ---------------- |
| `-u`   | 目标 URL         |
| `-w`   | 字典文件         |
| `FUZZ` | 默认 fuzz 占位符 |
| `-t`   | 并发线程         |
| `-mc`  | 匹配 HTTP 状态码 |
| `-fc`  | 过滤 HTTP 状态码 |
| `-fs`  | 过滤响应大小     |
| `-fw`  | 过滤响应字数     |
| `-fl`  | 过滤响应行数     |
| `-o`   | 输出文件         |
| `-of`  | 输出格式         |

### 示例

```
ffuf -u https://example.com/FUZZ -w wordlist.txt
```

### 说明

- `FUZZ` 是 ffuf 的默认占位符
- ffuf 会用字典中的每一行替换 FUZZ
- 每次请求都会生成一个新的 URL

例如：

```
wordlist.txt

admin
login
test
```

实际请求：

```
https://example.com/admin
https://example.com/login
https://example.com/test
```

---

# 二、目录和文件发现

这是 ffuf 最常见的使用场景。

## 1. 基础目录扫描

### 命令

```
ffuf -u https://target.com/FUZZ \
-w /usr/share/seclists/Discovery/Web-Content/common.txt
```

### 参数说明

| 参数   | 说明     |
| ------ | -------- |
| `-u` | fuzz URL |
| `-w` | 目录字典 |

### 字典来源

```
SecLists/Discovery/Web-Content/common.txt
```

该字典包含常见目录：

```
admin
uploads
backup
test
```

### 使用说明

该命令用于发现：

- 隐藏目录
- 管理后台
- 调试路径
- 测试页面

---

## 2. 过滤 404 页面

很多服务器会返回统一错误页面，需要过滤。

### 命令

```
ffuf -u https://target.com/FUZZ \
-w common.txt \
-fc 404
```

### 参数说明

```
-fc
```

过滤指定 HTTP 状态码。

### 使用示例

```
ffuf -u https://example.com/FUZZ \
-w common.txt \
-fc 404
```

只显示非 404 页面。

---

## 3. 只匹配特定状态码

### 命令

```
ffuf -u https://target.com/FUZZ \
-w common.txt \
-mc 200,301,302
```

### 参数说明

```
-mc
```

Match status code。

只显示：

```
200
301
302
```

### 使用说明

常用于发现：

- 有效页面
- 重定向页面
- 认证页面

---

# 三、文件扩展名爆破

很多网站存在备份文件。

常见扩展：

```
.bak
.old
.zip
.tar
```

## 命令

```
ffuf -u https://target.com/indexFUZZ \
-w web-extensions.txt
```

### 字典

```
SecLists/Discovery/Web-Content/web-extensions.txt
```

字典内容示例：

```
.php
.bak
.old
.zip
.tar.gz
```

### 实际请求

```
index.php
index.bak
index.old
```

### 使用场景

发现：

- 源代码备份
- 旧版本文件
- 压缩备份

---

# 四、子域名发现

用于发现隐藏子域。

## 命令

```
ffuf -u https://FUZZ.example.com \
-w subdomains.txt
```

### 字典

```
SecLists/Discovery/DNS/subdomains-top1million-5000.txt
```

### 示例

```
ffuf -u https://FUZZ.example.com \
-w subdomains-top1million-5000.txt
```

### 实际请求

```
https://admin.example.com
https://dev.example.com
https://api.example.com
```

### 使用说明

用于发现：

- 测试环境
- API服务器
- 内网服务

---

# 五、虚拟主机 fuzz

有些服务器通过 Host 头识别站点。

## 命令

```
ffuf -u https://example.com \
-w subdomains.txt \
-H "Host: FUZZ.example.com"
```

### 参数说明

```
-H
```

添加 HTTP Header。

### 使用场景

当服务器配置：

```
VirtualHost
```

多个站点共享 IP。

---

# 六、参数名发现

用于发现隐藏 GET 参数。

## 命令

```
ffuf -u "https://target.com/page.php?FUZZ=test" \
-w burp-parameter-names.txt
```

### 字典

```
SecLists/Discovery/Web-Content/burp-parameter-names.txt
```

字典示例：

```
id
user
page
file
action
```

### 示例

实际请求：

```
page.php?id=test
page.php?user=test
page.php?page=test
```

### 使用说明

用于发现：

- 未公开 API 参数
- 调试参数
- 后端控制参数

---

# 七、GET 参数 fuzz

用于漏洞测试。

## 命令

```
ffuf -u "https://target.com/item?id=FUZZ" \
-w payload.txt
```

### 示例字典

来自：

```
SecLists/Fuzzing/SQLi/SQLi.txt
```

payload 示例：

```
'
" 
' OR 1=1 --
../../../../etc/passwd
<script>alert(1)</script>
```

### 使用说明

用于测试：

- SQL injection
- LFI
- XSS

---

# 八、POST 参数 fuzz

## 命令

```
ffuf -u https://target.com/login \
-X POST \
-d "username=admin&password=FUZZ" \
-H "Content-Type: application/x-www-form-urlencoded" \
-w passwords.txt
```

### 参数解释

| 参数   | 说明      |
| ------ | --------- |
| `-X` | HTTP 方法 |
| `-d` | POST 数据 |
| `-H` | Header    |

### 示例

爆破密码：

```
username=admin
password=FUZZ
```

字典：

```
SecLists/Passwords/
```

---

# 九、JSON API fuzz

现代 API 使用 JSON。

## 命令

```
ffuf -u https://target.com/api/login \
-X POST \
-d '{"user":"admin","password":"FUZZ"}' \
-H "Content-Type: application/json" \
-w passwords.txt
```

### 使用说明

用于：

- API 登录爆破
- JSON 参数 fuzz
- API 安全测试

---

# 十、多参数 fuzz

ffuf 支持多字典。

## 命令

```
ffuf -u https://target.com/login \
-X POST \
-d "username=USERFUZZ&password=PASSFUZZ" \
-w users.txt:USERFUZZ \
-w passwords.txt:PASSFUZZ \
-mode clusterbomb
```

### 模式说明

| 模式        | 说明     |
| ----------- | -------- |
| clusterbomb | 全组合   |
| pitchfork   | 一一对应 |
| sniper      | 单参数   |

### 使用示例

如果：

```
users.txt
admin
root
passwords.txt
123456
admin
```

clusterbomb：

```
admin:123456
admin:admin
root:123456
root:admin
```

---

# 十一、自动过滤

很多网站返回统一页面。

## 命令

```
ffuf -u https://target.com/FUZZ \
-w common.txt \
-ac
```

### 参数说明

```
-ac
```

Auto calibration。

ffuf 自动识别：

- 错误页面
- 404
- 通用响应

---

# 十二、递归扫描

发现目录后继续扫描。

## 命令

```
ffuf -u https://target.com/FUZZ \
-w common.txt \
-recursion
```

### 深度限制

```
-recursion-depth 2
```

### 使用说明

例如：

发现

```
admin/
```

ffuf 会自动扫描

```
admin/FUZZ
```

---

# 十三、并发和性能控制

## 高并发

```
ffuf -u https://target.com/FUZZ \
-w common.txt \
-t 200
```

### 参数

```
-t
```

线程数量。

推荐：

```
50–200
```

---

## 限速

防止被封。

```
-rate 50
```

每秒 50 请求。

---

# 十四、代理与调试

用于配合 Burp。

## 命令

```
ffuf -u https://target.com/FUZZ \
-w common.txt \
-x http://127.0.0.1:8080
```

### 参数

```
-x
```

代理地址。

---

# 十五、结果输出

## JSON 输出

```
ffuf -u https://target.com/FUZZ \
-w common.txt \
-o result.json \
-of json
```

## HTML 报告

```
-of html
```

## CSV

```
-of csv
```

---

# 十六、常用 SecLists 字典

推荐使用 **SecLists**。

## 目录扫描

```
Discovery/Web-Content/common.txt
Discovery/Web-Content/raft-medium-directories.txt
```

## 文件扫描

```
raft-medium-files.txt
```

## 子域名

```
Discovery/DNS/subdomains-top1million-5000.txt
```

## 参数名

```
burp-parameter-names.txt
```

## 漏洞 payload

```
Fuzzing/SQLi
Fuzzing/XSS
Fuzzing/LFI
```

---

# 十七、典型渗透测试流程

推荐扫描流程。

1 子域名发现

```
ffuf subdomain fuzz
```

2 目录扫描

```
common.txt
```

3 深度目录

```
raft-medium-directories.txt
```

4 文件扫描

```
raft-medium-files.txt
```

5 参数发现

```
burp-parameter-names.txt
```

6 payload fuzz

```
SQLi
XSS
LFI
```

---

下面是一份**进阶级的 ffuf 实战模板与自动化使用指南**。
内容偏向**真实渗透测试、漏洞研究和自动化信息收集流程**，并继续结合 **SecLists** 字典体系。

结构：

1. 高价值扫描模板
2. 自动化信息收集流程
3. WAF 绕过与隐蔽扫描策略
4. 高级过滤与误报控制
5. 自动化脚本示例

---

# 一、常用 ffuf 实战扫描模板

以下模板可直接用于渗透测试或漏洞挖掘。

---

# 1 目录扫描（低噪声）

### 命令

```
ffuf -u https://target.com/FUZZ \
-w /usr/share/seclists/Discovery/Web-Content/common.txt \
-fc 404 \
-ac \
-t 80
```

### 参数说明

| 参数    | 作用             |
| ------- | ---------------- |
| -fc 404 | 过滤不存在页面   |
| -ac     | 自动校准错误页面 |
| -t 80   | 并发线程         |

### 使用说明

适合：

- 初始信息收集
- 低噪声扫描
- 生产环境测试

---

# 2 深度目录扫描

### 命令

```
ffuf -u https://target.com/FUZZ \
-w /usr/share/seclists/Discovery/Web-Content/raft-medium-directories.txt \
-recursion \
-recursion-depth 2 \
-t 100
```

### 参数解释

```
-recursion
```

发现目录后继续扫描。

```
-recursion-depth
```

限制递归深度。

### 使用场景

用于发现：

- 管理后台
- API目录
- 内部工具

---

# 3 文件扫描

### 命令

```
ffuf -u https://target.com/FUZZ \
-w raft-medium-files.txt \
-mc 200,204,301,302,307,401,403
```

### 参数说明

```
-mc
```

只显示可能存在文件的状态码。

### 使用说明

发现：

- 备份文件
- API 文档
- 调试脚本

---

# 4 备份文件扫描

### 命令

```
ffuf -u https://target.com/indexFUZZ \
-w web-extensions.txt
```

### 字典

```
SecLists/Discovery/Web-Content/web-extensions.txt
```

### 发现内容

```
index.bak
index.old
index.zip
index.tar.gz
```

这些文件可能包含：

- 源代码
- 数据库配置
- API 密钥

---

# 5 子域名扫描

### 命令

```
ffuf -u https://FUZZ.target.com \
-w subdomains-top1million-5000.txt \
-t 100
```

### 字典

```
SecLists/Discovery/DNS/subdomains-top1million-5000.txt
```

### 发现示例

```
dev.target.com
admin.target.com
api.target.com
staging.target.com
```

---

# 6 虚拟主机扫描

很多服务器存在隐藏虚拟主机。

### 命令

```
ffuf -u https://target.com \
-w subdomains.txt \
-H "Host: FUZZ.target.com"
```

### 使用说明

适用于：

- CDN 背后服务器
- 内部虚拟站点

---

# 7 参数发现

### 命令

```
ffuf -u "https://target.com/page.php?FUZZ=1" \
-w burp-parameter-names.txt
```

### 字典

```
SecLists/Discovery/Web-Content/burp-parameter-names.txt
```

### 示例

发现隐藏参数：

```
page.php?debug=1
page.php?admin=1
page.php?file=1
```

---

# 8 LFI 测试

### 命令

```
ffuf -u "https://target.com/index.php?page=FUZZ" \
-w LFI-gracefulsecurity-linux.txt
```

### 字典

```
SecLists/Fuzzing/LFI/
```

### 示例 payload

```
../../../../etc/passwd
../../../../proc/self/environ
```

---

# 9 SQL 注入 fuzz

### 命令

```
ffuf -u "https://target.com/item?id=FUZZ" \
-w SQLi.txt
```

### 字典

```
SecLists/Fuzzing/SQLi/SQLi.txt
```

---

# 10 XSS fuzz

### 命令

```
ffuf -u "https://target.com/search?q=FUZZ" \
-w XSS.txt
```

### 字典

```
SecLists/Fuzzing/XSS/XSS-Jhaddix.txt
```

---

# 二、高级过滤与误报控制

误报是 fuzz 的主要问题。

---

# 1 响应大小过滤

### 命令

```
ffuf -u https://target.com/FUZZ \
-w common.txt \
-fs 4242
```

### 说明

```
-fs
```

过滤指定响应大小。

---

# 2 响应字数过滤

```
-fw
```

### 示例

```
ffuf -u https://target.com/FUZZ \
-w common.txt \
-fw 120
```

---

# 3 行数过滤

```
-fl
```

### 示例

```
ffuf -u https://target.com/FUZZ \
-w common.txt \
-fl 30
```

---

# 三、WAF 绕过技巧

一些 WAF 会检测扫描行为。

---

# 1 限速扫描

### 命令

```
ffuf -u https://target.com/FUZZ \
-w common.txt \
-rate 20
```

每秒 20 请求。

---

# 2 随机 User-Agent

### 命令

```
ffuf -u https://target.com/FUZZ \
-w user-agents.txt \
-H "User-Agent: FUZZ"
```

字典：

```
SecLists/Usernames/user-agents.txt
```

---

# 3 伪造来源 IP

### 命令

```
ffuf -u https://target.com/FUZZ \
-w common.txt \
-H "X-Forwarded-For: 127.0.0.1"
```

可组合：

```
X-Forwarded-For
X-Real-IP
X-Originating-IP
```

---

# 四、自动化信息收集流程

真实渗透测试常用流程：

```
子域名收集
↓
目录扫描
↓
文件扫描
↓
参数发现
↓
漏洞 fuzz
```

常见工具组合：

| 工具      | 作用       |
| --------- | ---------- |
| amass     | 子域名收集 |
| subfinder | 子域名发现 |
| ffuf      | fuzz       |
| httpx     | HTTP探测   |
| nuclei    | 漏洞扫描   |

---

# 五、自动化扫描脚本示例

示例 Bash 自动扫描。

```
#!/bin/bash

target=$1

echo "Scanning directories"

ffuf -u https://$target/FUZZ \
-w /usr/share/seclists/Discovery/Web-Content/common.txt \
-fc 404 \
-o dir.json

echo "Scanning files"

ffuf -u https://$target/FUZZ \
-w /usr/share/seclists/Discovery/Web-Content/raft-medium-files.txt \
-mc 200,301,302 \
-o files.json

echo "Parameter fuzzing"

ffuf -u "https://$target/index.php?FUZZ=1" \
-w /usr/share/seclists/Discovery/Web-Content/burp-parameter-names.txt \
-o params.json
```

运行：

```
bash scan.sh target.com
```

---

# 六、性能优化建议

推荐设置：

| 参数            | 推荐值  |
| --------------- | ------- |
| threads         | 50–150 |
| rate            | 20–100 |
| recursion depth | 1–2    |

扫描过快可能：

- 触发 WAF
- 被封 IP
- 导致目标宕机

---

# 七、企业级扫描建议

在真实渗透测试中：

建议分阶段扫描：

阶段一（低噪声）

```
common.txt
```

阶段二（深度）

```
raft-medium
```

阶段三（漏洞）

```
SQLi
XSS
LFI
```

阶段四（自动化）

```
ffuf + nuclei
```

---

下面是一份**高级版 ffuf 实战手册（高级技巧 + 自动化流程）**。
内容重点放在：

- 高级 fuzz 技术
- API 与现代 Web 应用测试
- 与其他工具协同
- 自动化信息收集 pipeline

同时继续结合 **SecLists** 字典体系。

---

# 一、ffuf 高级占位符与多字典技术

## 1 自定义占位符

默认占位符为：

```
FUZZ
```

但在复杂场景中需要多个变量。

### 示例

```
USER
PASS
```

### 命令

```
ffuf -u https://target.com/login \
-X POST \
-d "username=USER&password=PASS" \
-w users.txt:USER \
-w passwords.txt:PASS \
-mode clusterbomb
```

### 参数说明

| 参数        | 作用             |
| ----------- | ---------------- |
| -w file:KEY | 将字典绑定到变量 |
| -mode       | 指定组合方式     |

---

## 2 fuzz 模式说明

### clusterbomb

所有组合。

示例：

```
users
admin
root
passwords
123456
admin
```

请求组合：

```
admin:123456
admin:admin
root:123456
root:admin
```

---

### pitchfork

按顺序组合。

```
admin:123456
root:admin
```

命令：

```
-mode pitchfork
```

---

### sniper

一次只 fuzz 一个参数。

---

# 二、API fuzz 技术

现代 Web 目标往往是 API。

---

## 1 REST API 参数 fuzz

### 示例

```
GET /api/v1/users?id=1
```

### 命令

```
ffuf -u "https://target.com/api/v1/users?id=FUZZ" \
-w /usr/share/seclists/Fuzzing/SQLi/SQLi.txt
```

### 用途

测试：

- SQL 注入
- IDOR
- 参数解析漏洞

---

## 2 JSON 参数 fuzz

### 示例

```
POST /api/login
```

body

```
{
 "username":"admin",
 "password":"FUZZ"
}
```

### 命令

```
ffuf -u https://target.com/api/login \
-X POST \
-H "Content-Type: application/json" \
-d '{"username":"admin","password":"FUZZ"}' \
-w passwords.txt
```

---

## 3 JSON key fuzz

测试未知 JSON 参数。

### 命令

```
ffuf -u https://target.com/api/user \
-X POST \
-H "Content-Type: application/json" \
-d '{"FUZZ":"test"}' \
-w burp-parameter-names.txt
```

### 字典

```
SecLists/Discovery/Web-Content/burp-parameter-names.txt
```

---

# 三、JavaScript 路径挖掘

现代 Web 应用很多接口隐藏在 JS 文件中。

常见流程：

```
JS 文件
↓
提取路径
↓
ffuf fuzz
```

### 示例

JS 中发现：

```
/api/admin
/api/debug
/api/internal
```

### ffuf 扫描

```
ffuf -u https://target.com/FUZZ \
-w js-endpoints.txt
```

---

# 四、参数值 fuzz

用于发现隐藏逻辑。

### 示例

```
role=user
```

### fuzz

```
role=admin
role=root
role=debug
```

### 命令

```
ffuf -u "https://target.com/profile?role=FUZZ" \
-w roles.txt
```

---

# 五、认证绕过 fuzz

一些后台存在 header 绕过。

### 示例 header

```
X-Forwarded-For
X-Original-URL
X-Rewrite-URL
```

### 命令

```
ffuf -u https://target.com/admin \
-w headers.txt \
-H "FUZZ: 127.0.0.1"
```

字典：

```
SecLists/Fuzzing/HTTP-Request-Headers.txt
```

---

# 六、Content-Type fuzz

一些服务器解析异常。

### 命令

```
ffuf -u https://target.com/api \
-X POST \
-d "test=data" \
-H "Content-Type: FUZZ" \
-w content-types.txt
```

---

# 七、路径穿越 fuzz

### 示例

```
file=FUZZ
```

### 命令

```
ffuf -u "https://target.com/download?file=FUZZ" \
-w LFI-gracefulsecurity-linux.txt
```

字典：

```
SecLists/Fuzzing/LFI
```

---

# 八、自动化扫描 pipeline

真实渗透测试通常使用工具链。

典型流程：

```
subfinder
↓
httpx
↓
ffuf
↓
nuclei
```

---

## 示例 pipeline

### 1 子域名收集

使用 **subfinder**

```
subfinder -d target.com -o subs.txt
```

---

### 2 HTTP 探测

使用 **httpx**

```
httpx -l subs.txt -o live.txt
```

---

### 3 ffuf 扫描

```
cat live.txt | while read url
do
ffuf -u $url/FUZZ \
-w common.txt \
-fc 404
done
```

---

### 4 漏洞扫描

使用 **nuclei**

```
nuclei -l live.txt
```

---

# 九、分布式 fuzz

大型目标需要分布式扫描。

方法：

```
多个 VPS
↓
分割字典
↓
并行扫描
```

### 字典分割

```
split -l 5000 wordlist.txt part_
```

每个服务器运行：

```
ffuf -w part_a
ffuf -w part_b
```

---

# 十、结果分析技巧

重要观察指标：

| 指标        | 说明      |
| ----------- | --------- |
| status code | HTTP 状态 |
| size        | 响应大小  |
| words       | 字数      |
| lines       | 行数      |

异常响应往往表示：

- 存在隐藏页面
- 访问控制错误
- 参数解析异常

---

# 十一、企业渗透测试推荐字典

推荐 **SecLists** 目录。

## 目录扫描

```
Discovery/Web-Content/common.txt
Discovery/Web-Content/raft-medium-directories.txt
```

---

## 文件扫描

```
raft-medium-files.txt
```

---

## 子域名

```
Discovery/DNS/subdomains-top1million-5000.txt
```

---

## 参数名

```
burp-parameter-names.txt
```

---

## payload

```
Fuzzing/
SQLi
XSS
LFI
```

---

# 十二、扫描策略建议

真实环境建议分阶段。

阶段一（轻量）

```
common.txt
```

阶段二（中等）

```
raft-medium
```

阶段三（深度）

```
big.txt
```

阶段四（漏洞 fuzz）

```
SQLi
XSS
LFI
```

---

下面是一份 **SecLists 与 ffuf 的字典实战地图（Wordlist Strategy Guide）**。
重点说明：

- SecLists 各目录的用途
- 适合 ffuf 的常用字典
- 不同漏洞类型的字典选择
- 渗透测试中的字典组合策略

该指南更接近**渗透测试人员的字典选择方法论**。

---

# 一、SecLists 目录结构概览

SecLists 的主要目录结构如下：

```
SecLists
 ├─ Discovery
 ├─ Fuzzing
 ├─ Payloads
 ├─ Passwords
 ├─ Usernames
 ├─ Web-Shells
 └─ Miscellaneous
```

其中与 ffuf 关系最密切的是：

```
Discovery
Fuzzing
Passwords
Usernames
```

---

# 二、Discovery 目录（信息收集）

Discovery 用于发现：

- 子域名
- 目录
- 文件
- 参数

这是 **ffuf 使用最频繁的字典目录**。

---

# 1 子域名字典

目录：

```
Discovery/DNS/
```

常用字典：

```
subdomains-top1million-5000.txt
subdomains-top1million-20000.txt
dns-Jhaddix.txt
```

---

## 示例命令

```
ffuf -u https://FUZZ.example.com \
-w subdomains-top1million-5000.txt
```

---

## 推荐策略

小型目标：

```
5000
```

大型企业：

```
20000
```

深度扫描：

```
dns-Jhaddix
```

---

# 2 目录扫描字典

目录：

```
Discovery/Web-Content/
```

常用字典：

```
common.txt
raft-medium-directories.txt
directory-list-2.3-medium.txt
```

---

## 示例命令

```
ffuf -u https://example.com/FUZZ \
-w common.txt \
-fc 404
```

---

## 字典特点

| 字典           | 特点       |
| -------------- | ---------- |
| common         | 小型、快速 |
| raft-medium    | 更全面     |
| directory-list | 深度扫描   |

---

# 3 文件扫描字典

常用：

```
raft-medium-files.txt
```

---

## 示例

```
ffuf -u https://example.com/FUZZ \
-w raft-medium-files.txt
```

---

# 4 扩展名字典

```
web-extensions.txt
```

---

## 示例

```
ffuf -u https://example.com/indexFUZZ \
-w web-extensions.txt
```

---

# 三、参数名字典

目录：

```
Discovery/Web-Content/
```

最重要的字典：

```
burp-parameter-names.txt
```

---

## 示例

```
ffuf -u "https://example.com/page.php?FUZZ=1" \
-w burp-parameter-names.txt
```

---

## 典型发现

```
debug
admin
config
file
```

---

# 四、Fuzzing 目录（漏洞 payload）

该目录包含大量攻击 payload。

```
Fuzzing
```

主要分类：

```
SQLi
XSS
LFI
SSRF
Command-Injection
```

---

# 1 SQL 注入字典

目录：

```
Fuzzing/SQLi/
```

常用字典：

```
SQLi.txt
Generic-SQLi.txt
```

---

## 示例

```
ffuf -u "https://example.com/item?id=FUZZ" \
-w SQLi.txt
```

---

# 2 XSS 字典

目录：

```
Fuzzing/XSS/
```

推荐：

```
XSS-Jhaddix.txt
```

---

## 示例

```
ffuf -u "https://example.com/search?q=FUZZ" \
-w XSS-Jhaddix.txt
```

---

# 3 LFI 字典

目录：

```
Fuzzing/LFI/
```

常用：

```
LFI-gracefulsecurity-linux.txt
```

---

## 示例

```
ffuf -u "https://example.com/index.php?page=FUZZ" \
-w LFI-gracefulsecurity-linux.txt
```

---

# 五、密码字典

目录：

```
Passwords
```

常见：

```
rockyou.txt
top-passwords-shortlist.txt
```

---

## 登录爆破

```
ffuf -u https://example.com/login \
-X POST \
-d "user=admin&pass=FUZZ" \
-w rockyou.txt
```

---

# 六、用户名枚举

目录：

```
Usernames
```

常用：

```
top-usernames-shortlist.txt
names.txt
```

---

## 示例

```
ffuf -u https://example.com/login \
-X POST \
-d "user=FUZZ&pass=test" \
-w top-usernames-shortlist.txt
```

---

# 七、漏洞类型与字典选择

这是渗透测试中最重要的部分。

---

## 1 目录扫描

字典：

```
common.txt
raft-medium-directories.txt
```

命令：

```
ffuf -u https://target.com/FUZZ -w common.txt
```

---

## 2 API 发现

字典：

```
api-endpoints.txt
raft-medium-directories.txt
```

---

## 3 参数发现

字典：

```
burp-parameter-names.txt
```

---

## 4 SQL 注入

字典：

```
SQLi.txt
```

---

## 5 XSS

字典：

```
XSS-Jhaddix.txt
```

---

## 6 LFI

字典：

```
LFI-gracefulsecurity-linux.txt
```

---

# 八、推荐字典组合（渗透测试）

常见扫描组合：

### 第一阶段（快速扫描）

```
common.txt
subdomains-top1million-5000.txt
```

---

### 第二阶段（中等深度）

```
raft-medium-directories.txt
raft-medium-files.txt
```

---

### 第三阶段（深度扫描）

```
directory-list-2.3-medium.txt
```

---

### 第四阶段（漏洞 fuzz）

```
SQLi
XSS
LFI
```

---

# 九、字典大小选择策略

不同规模目标需要不同字典。

| 目标类型 | 推荐字典       |
| -------- | -------------- |
| 小型网站 | common         |
| 企业站点 | raft-medium    |
| 大型企业 | directory-list |
| 深度测试 | big.txt        |

---

# 十、字典优化技巧

实际渗透测试中通常需要：

### 去重

```
sort wordlist.txt | uniq
```

---

### 合并字典

```
cat list1.txt list2.txt > combined.txt
```

---

### 限制大小

```
head -n 5000 biglist.txt
```

---

# 十一、渗透测试人员常用字典组合

典型组合：

```
common.txt
raft-medium-directories.txt
raft-medium-files.txt
burp-parameter-names.txt
subdomains-top1million-5000.txt
```

这些字典可以覆盖：

- 90% 的 Web 资产发现

---

# 十二、实战扫描流程

完整扫描流程：

```
子域名发现
↓
目录扫描
↓
文件扫描
↓
参数发现
↓
漏洞 fuzz
```

示例：

```
ffuf subdomain fuzz
ffuf directory fuzz
ffuf parameter fuzz
ffuf payload fuzz
```

---

下面内容重点说明 **ffuf 在面对 WAF 时的策略，以及在真实漏洞挖掘中的使用方法**。
这些方法通常用于**渗透测试或漏洞研究环境**，目标是降低扫描被阻断概率并提高有效发现率。

---

# 一、WAF 的常见检测机制

在设计 ffuf 扫描策略前，需要理解 WAF 的典型检测方式。

常见检测机制包括：

| 检测方式            | 说明             |
| ------------------- | ---------------- |
| Rate limiting       | 请求频率检测     |
| Signature detection | payload 特征匹配 |
| Behavioral analysis | 扫描行为识别     |
| Header inspection   | HTTP 头检测      |
| IP reputation       | IP信誉/黑名单    |

常见 WAF 产品包括：

- Cloudflare
- ModSecurity
- Akamai Kona Site Defender

针对不同机制，需要采用不同的扫描策略。

---

# 二、控制扫描速率（最基础策略）

WAF 最容易检测的是**高频请求**。

### 示例

```
ffuf -u https://target.com/FUZZ \
-w common.txt \
-rate 10
```

参数说明：

```
-rate
```

限制每秒请求数。

推荐设置：

| 场景       | rate    |
| ---------- | ------- |
| Cloudflare | 5–15   |
| 企业WAF    | 10–30  |
| 无WAF      | 50–200 |

扫描速率过高通常会触发：

- IP封禁
- CAPTCHA
- 403/429

---

# 三、线程控制

线程过高会产生明显扫描特征。

### 示例

```
ffuf -u https://target.com/FUZZ \
-w common.txt \
-t 40
```

参数说明：

```
-t
```

并发线程数量。

建议：

| WAF环境 | 推荐线程 |
| ------- | -------- |
| 强WAF   | 10–30   |
| 普通WAF | 30–80   |
| 无WAF   | 100+     |

---

# 四、User-Agent 轮换

很多 WAF 会识别扫描工具的默认 UA。

### 示例

```
ffuf -u https://target.com/FUZZ \
-w common.txt \
-H "User-Agent: FUZZ" \
-w user-agents.txt:FUZZ
```

字典来源：

```
SecLists/Usernames/user-agents.txt
```

原理：

每个请求使用不同 UA，减少扫描特征。

---

# 五、Header 伪造

某些 WAF 会信任特定 header。

常见 header：

```
X-Forwarded-For
X-Real-IP
X-Originating-IP
```

### 示例

```
ffuf -u https://target.com/FUZZ \
-w common.txt \
-H "X-Forwarded-For: 127.0.0.1"
```

用途：

测试是否存在：

- 内网信任
- 反向代理漏洞

---

# 六、HTTP 方法绕过

某些 WAF 只检测 GET 请求。

### 示例

```
ffuf -u https://target.com/admin/FUZZ \
-X POST \
-w common.txt
```

或：

```
-X PUT
-X OPTIONS
```

部分服务器会出现：

- 方法处理错误
- 访问控制绕过

---

# 七、路径编码绕过

很多 WAF 对路径解析不完整。

### 示例

正常路径：

```
/admin
```

编码：

```
%2fadmin
%252fadmin
```

结合 ffuf：

```
ffuf -u https://target.com/FUZZ \
-w encoded-paths.txt
```

---

# 八、参数混淆

WAF 可能只检查特定参数。

### 示例

正常：

```
?id=1
```

混淆：

```
?id=1&id=2
?id=1;2
?id[]=1
```

ffuf：

```
ffuf -u "https://target.com/item?id=FUZZ" \
-w payload.txt
```

---

# 九、分布式扫描

对于大型目标，常用策略是**多IP扫描**。

流程：

```
多个 VPS
↓
拆分字典
↓
并行扫描
```

字典拆分：

```
split -l 5000 wordlist.txt part_
```

每台服务器运行：

```
ffuf -w part_a
```

优势：

- 降低单IP请求量
- 避免封禁

---

# 十、随机延迟

有些 WAF 会检测固定请求间隔。

可以加入延迟。

示例脚本：

```
while read url
do
ffuf -u $url/FUZZ -w common.txt -rate 5
sleep $((RANDOM % 5))
done
```

---

# 十一、真实漏洞挖掘中的 ffuf 使用方法

ffuf 在漏洞研究中主要用于 **攻击面发现**。

核心思想：

```
扩大攻击面
↓
发现隐藏功能
↓
测试漏洞
```

---

# 十二、真实漏洞挖掘流程

典型流程：

```
资产收集
↓
子域名发现
↓
目录扫描
↓
API发现
↓
参数发现
↓
漏洞 fuzz
```

常见工具链：

| 阶段     | 工具      |
| -------- | --------- |
| 子域名   | subfinder |
| HTTP探测 | httpx     |
| fuzz     | ffuf      |
| 漏洞扫描 | nuclei    |

---

# 十三、攻击面发现（最重要）

大多数漏洞来自**隐藏接口**。

ffuf 用于发现：

```
/admin
/internal
/debug
/api
/private
```

示例：

```
ffuf -u https://target.com/FUZZ \
-w raft-medium-directories.txt
```

---

# 十四、API 接口发现

现代应用很多漏洞在 API。

字典：

```
api-endpoints.txt
```

扫描：

```
ffuf -u https://target.com/api/FUZZ \
-w api-endpoints.txt
```

---

# 十五、参数发现

很多漏洞来自隐藏参数。

扫描：

```
ffuf -u "https://target.com/page.php?FUZZ=1" \
-w burp-parameter-names.txt
```

可能发现：

```
debug
admin
config
test
```

---

# 十六、权限控制漏洞

扫描后台接口：

```
ffuf -u https://target.com/admin/FUZZ \
-w common.txt
```

观察：

```
403
401
```

再测试：

```
X-Forwarded-For
```

有时可绕过。

---

# 十七、IDOR 漏洞

示例：

```
/api/user?id=100
```

使用 ffuf：

```
ffuf -u "https://target.com/api/user?id=FUZZ" \
-w ids.txt
```

可能访问其他用户数据。

---

# 十八、敏感文件发现

很多真实漏洞来自备份文件。

扫描：

```
ffuf -u https://target.com/indexFUZZ \
-w web-extensions.txt
```

可能发现：

```
index.php.bak
index.zip
backup.tar.gz
```

---

# 十九、常见漏洞来源统计（真实研究）

经验统计：

| 来源     | 占比 |
| -------- | ---- |
| 目录扫描 | 高   |
| API接口  | 很高 |
| 参数发现 | 高   |
| JS路径   | 高   |
| 备份文件 | 中   |

ffuf 在其中承担 **攻击面发现核心角色**。

---

# 二十、实践建议

在真实漏洞挖掘中：

不要盲目扫描大字典。

推荐流程：

阶段一：

```
common.txt
```

阶段二：

```
raft-medium
```

阶段三：

```
payload fuzz
```

---
