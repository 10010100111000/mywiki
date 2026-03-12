# 理论

Web 模糊测试是 Web 应用程序安全领域的一项关键技术，它通过测试各种输入来识别漏洞。该技术通过向 Web 应用程序提供意外或随机数据来进行自动化测试，从而检测攻击者可能利用的潜在缺陷。

在 Web 应用程序安全领域，“ fuzzing ”和“ brute-forcing ”这两个术语经常被混用，对于初学者来说，将它们视为类似的技术完全没问题。然而，两者之间存在一些细微的差别：

## 模糊测试 vs. 暴力破解

模糊测试覆盖范围更广。它通过向 Web 应用程序输入意料之外的数据（包括格式错误的数据、无效字符和无意义的组合）来进行测试。其目的是观察应用程序如何应对这些异常输入，并发现其在处理意外数据方面存在的潜在漏洞。模糊测试工具通常会利用包含常见模式、现有参数变体甚至随机字符序列的词表来生成各种各样的有效载荷。

暴力破解则是一种更有针对性的方法。它专注于系统地尝试各种可能的结果，例如密码或身份证号码。暴力破解工具通常依赖于预定义的列表或字典（例如密码字典）来通过反复试验来猜测正确的值。

这里举个例子来说明区别：想象一下，你正试图打开一扇锁着的门。模糊测试就像是把你能找到的所有东西都扔向门——钥匙、螺丝刀，甚至是一只橡皮鸭——看看有没有什么能打开它。而暴力破解就像是把钥匙串上的每一种组合都试一遍，直到找到能开门的那一对。

## 为什么需要模糊测试 Web 应用程序？

Web 应用程序已成为现代商业和通信的支柱，处理着海量的敏感数据，并支持着关键的在线交互。然而，它们的复杂性和相互关联性也使它们成为网络攻击的主要目标。手动测试虽然必不可少，但在识别漏洞方面却存在局限性。而这正是 Web 模糊测试的优势所在：

* **揭露隐藏的漏洞**: 模糊测试可以发现传统安全测试方法可能遗漏的漏洞。通过向网络应用程序注入意外和无效的输入，模糊测试可以触发意外行为，从而揭示代码中隐藏的缺陷。
* 自动化安全测试: 模糊测试可自动生成和发送测试输入，从而节省宝贵的时间和资源。这使得安全团队能够专注于分析结果和解决发现的漏洞。
* 模拟真实世界攻击: 模糊测试工具可以模拟攻击者的技术，帮助您在恶意攻击者利用漏洞之前识别它们。这种主动出击的方法可以显著降低攻击成功的风险。
* 加强输入验证: 模糊测试有助于识别输入验证机制中的弱点，这对于防止 SQL injection 和 cross-site scripting ( XSS ) 等常见漏洞至关重要。
* 提高代码质量: 模糊测试通过发现代码漏洞和错误来提升整体代码质量。开发人员可以利用模糊测试的反馈来编写更健壮、更安全的代码。
* 持续安全: ：模糊测试可以集成到 software development lifecycle ( SDLC ) 中，作为 continuous integration and continuous deployment ( CI/CD 持续集成和持续部署) 管道的一部分，从而确保定期执行安全测试，并在开发过程早期发现漏洞。

## 基本概念

在我们深入探讨网络模糊测试的实际方面之前，了解一些关键概念是很重要的：

| 概念                                    | 说明                                                                              | 示例                                                                                 |
| --------------------------------------- | --------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------ |
| **字典（Wordlist）**              | 字典或单词、短语、文件名、目录名、参数值的列表，用于模糊测试时作为输入。          | 通用：admin、login、password、backup、config应用专属：productID、addToCart、checkout |
| **载荷（Payload）**               | 模糊测试过程中发送给 Web 应用的实际数据，可以是简单字符串、数值或复杂数据结构。   | ' OR 1=1 -- （用于 SQL 注入）                                                        |
| **响应分析（Response Analysis）** | 检查 Web 应用对模糊测试载荷的响应（如状态码、错误信息），识别可能代表漏洞的异常。 | 正常：200 OK错误（疑似 SQL 注入）：500 服务器内部错误 附带数据库报错                 |
| **模糊测试工具（Fuzzer）**        | 自动生成并发送载荷到 Web 应用、并分析响应的软件工具。                             | ffuf、wfuzz、Burp Suite Intruder                                                     |
| **误报（False Positive）**        | 被工具错误识别为漏洞的结果。                                                      | 对不存在的目录返回 404 Not Found                                                     |
| **漏报（False Negative）**        | 应用中实际存在但未被工具检测到的漏洞。                                            | 支付流程中隐蔽的逻辑漏洞                                                             |
| **测试范围（Fuzzing Scope）**     | 模糊测试所针对的 Web 应用具体部分。                                               | 仅对登录页面测试，或专注于某个 API 接口                                              |

## 工具

我们将使用四款功能强大的工具，它们专为 Web 应用程序侦察和漏洞评估而设计。为了简化设置，我们将预先安装所有工具。

### 安装 Go、Python 和 PIPX

使用这些工具需要安装 Go 和 Python。如果尚未安装，请按以下步骤安装。

pipx 是一个命令行工具，旨在简化 Python 应用程序的安装和管理。它通过为每个应用程序创建独立的虚拟环境来简化流程，确保依赖项之间不会发生冲突。这意味着您可以安装和运行多个 Python 应用程序 pipx 而无需担心兼容性问题。pipx 还简化了应用程序的升级或卸载过程，使您的系统保持整洁有序。

如果您使用的是基于 Debian 的系统（例如 Ubuntu），则可以使用 APT 软件包管理器安装 Go、Python 和 PIPX。

1. 打开终端并更新软件包列表，以确保您拥有有关软件包及其依赖项最新版本的最新信息。

   ```shell
   $ sudo apt update
   ```
2. 使用以下命令安装 Go：

   ```shell
   $ sudo apt install -y golang
   ```
3. 使用以下命令安装 Python：

   ```shell
   $ sudo apt install -y python3 python3-pip
   ```
4. 使用以下命令安装和配置 pipx：

   ```shell
   $ sudo apt install pipx
   $ pipx ensurepath
   $ sudo pipx ensurepath --global
   ```
5. 为确保 Go 和 Python 已正确安装，您可以检查它们的版本：

   ```powershell
   $ go version
   $ python3 --version
   ```

如果安装成功，您应该可以看到 Go 和 Python 的版本信息。

### FFUF

FFUF （ Fuzz Faster U Fool ）是一款用 Go 语言编写的快速 Web 模糊测试工具。它擅长快速枚举 Web 应用程序中的目录、文件和参数。其灵活性、速度和易用性使其成为安全专家和爱好者的首选。

您可以使用以下命令安装 FFUF ：

```shell
$ go install github.com/ffuf/ffuf/v2@latest

```

应用场景

| 应用场景       | 说明                                         |
| -------------- | -------------------------------------------- |
| 目录与文件枚举 | 快速发现 Web 服务器上的隐藏目录和文件。      |
| 参数发现       | 查找并测试 Web 应用中的参数。                |
| 暴力破解攻击   | 执行暴力破解，以获取登录凭证或其他敏感信息。 |

### Gobuster

Gobuster 是另一款流行的网页目录和文件模糊测试工具。它以速度快、操作简单著称，因此对于初学者和经验丰富的用户来说都是不错的选择。

您可以使用以下命令安装 GoBuster ：

```shell
$ go install github.com/OJ/gobuster/v3@latest
```

应用场景

| 应用场景           | 说明                                                    |
| ------------------ | ------------------------------------------------------- |
| 内容发现           | 快速扫描并发现隐藏的 Web 内容，如目录、文件和虚拟主机。 |
| DNS 子域名枚举     | 识别目标域名的子域名。                                  |
| WordPress 内容检测 | 使用专用字典查找与 WordPress 相关的内容。               |

### FeroxBuster

FeroxBuster 是一个用 Rust 编写的快速递归内容发现工具。它专为暴力搜索 Web 应用程序中未链接的内容而设计，因此特别适用于识别隐藏的目录和文件。它更像是一个“强制浏览”工具，而不是像 ffuf 那样的模糊测试工具。

要安装 FeroxBuster ，您可以使用以下命令：

```shell
$ curl -sL https://raw.githubusercontent.com/epi052/feroxbuster/main/install-nix.sh | sudo bash -s $HOME/.local/bin
```

应用场景

| 应用场景       | 说明                                               |
| -------------- | -------------------------------------------------- |
| 递归扫描       | 执行递归扫描，以发现嵌套的目录与文件。             |
| 未关联内容发现 | 识别 Web 应用中**未被内部链接指向**的内容。  |
| 高性能扫描     | 借助 Rust 语言的性能优势，实现高速的内容发现扫描。 |

### wfuzz/wenum

wenum 是 wfuzz 的一个活跃维护的分支，wfuzz 是一款功能强大且用途广泛的命令行模糊测试工具，以其灵活性和自定义选项而闻名。它尤其适用于参数模糊测试，允许您针对 Web 应用程序测试各种输入值，并发现它们在处理这些参数时可能存在的漏洞。

如果您使用的是 PwnBox 或 Kali 等渗透测试 Linux 发行版， wfuzz 可能已经预装，您可以根据需要立即使用它。但是，目前安装 wfuzz 存在一些问题，因此您可以将其替换为 wenum 。这两个命令可以互换，语法也相同，因此如有必要，您可以直接将 wenum 命令替换为 wfuzz 命令。

以下命令将使用 pipx （一个用于在隔离环境中安装和管理 Python 应用程序的工具）来安装 wenum 。这可以确保 wenum 拥有一个干净且一致的环境，防止任何可能的软件包冲突：

```
$ pipx install git+https://github.com/WebFuzzForge/wenum
$ pipx runpip wenum install setuptools
```

应用场景

| 应用场景       | 说明                                             |
| -------------- | ------------------------------------------------ |
| 目录与文件枚举 | 快速识别 Web 服务器上的隐藏目录和文件。          |
| 参数发现       | 查找并测试 Web 应用程序中的参数。                |
| 暴力破解攻击   | 执行暴力破解攻击，以获取登录凭证或其他敏感信息。 |

# 目录和文件模糊测试

Web 应用通常会存在一些**不直接链接或对用户不可见的目录和文件**。这些隐藏资源可能包含敏感信息、备份文件、配置文件，甚至是存在漏洞的旧版应用程序。**目录和文件模糊测试（Fuzzing）** 旨在发现这些隐藏资产，为攻击者提供潜在的入侵入口或有价值信息，用于进一步的漏洞利用。

## 发现隐藏资产

Web 应用中往往藏有大量隐藏资源 —— 目录、文件和接口端点，这些内容无法通过主界面直接访问。对攻击者而言，这些隐蔽区域可能包含极具价值的信息，例如：

- **敏感数据**：备份文件、配置文件或包含用户凭证及其他机密信息的日志。
- **过时内容**：可能存在已知漏洞的旧版文件或脚本。
- **开发相关资源**：测试环境、预发布站点或管理后台，可被用于发起进一步攻击。
- **隐藏功能**：未公开的功能或接口，可能暴露出意料之外的漏洞。

对安全研究员和渗透测试人员来说，发现这些隐藏资产至关重要。这能帮助他们更深入地理解 Web 应用的**攻击面**与潜在漏洞。

## 发现隐藏资产的重要性

挖掘这些 “隐藏宝藏” 绝非小事。每一项发现都能帮助完善对 Web 应用结构与功能的整体认知，这是开展全面安全评估的基础。

这些隐藏区域通常缺乏对外公开组件那样严格的安全防护，因此极易成为被攻击的重点目标。通过主动发现这些漏洞，你可以领先恶意攻击者一步。

即便某个隐藏资产不会直接暴露出漏洞，从中获取的信息在渗透测试的后续阶段也可能极具价值，包括了解目标使用的技术栈、发现可用于后续攻击的敏感数据等。

**目录与文件模糊测试**是发现这些隐藏资产最有效的方法之一。其核心思路是：使用一系列潜在的目录与文件名对目标 Web 应用进行系统性探测，并分析服务器响应，从而识别出真实存在的资源。

## 字典（Wordlist）

字典是目录和文件模糊测试的**核心命脉**。它提供了工具用于探测目标的潜在目录名与文件名。高质量的字典能大幅提升发现隐藏资产的成功率。

字典通常来源于多种渠道：

- 从网络上收集常见目录与文件名
- 分析公开的数据泄露事件
- 从已知漏洞中提取目录信息

这些字典会经过精心整理，去重并剔除无效条目，以保证在测试过程中高效可用。其目标是构建一份全面、高覆盖率的潜在目录与文件名列表，从而对目标应用进行全面探测。

我们前面提到的工具（如 ffuf、wfuzz 等）**本身不内置字典**，但可以很好地兼容外部字典文件。这种灵活性让你可以使用现成字典，或根据特定目标与场景自行构建字典。

目前最全面、使用最广泛的字典集合之一是 **[SecLists](https://github.com/danielmiessler/SecLists)**。这是 GitHub 上的一个开源项目，提供了大量适用于各类安全测试场景的字典，包括目录与文件模糊测试专用字典。

SecLists 包含适用于以下场景的字典：

- 常见目录与文件名
- 备份文件
- 配置文件
- 存在漏洞的脚本
- 以及更多其他内容

在 SecLists 中，最常用于 Web 目录与文件模糊测试的字典有：

- **Discovery/Web-Content/common.txt**：这是通用字典，包含 Web 服务器上大量常见的目录与文件名。它是进行模糊测试的绝佳起点，通常能得到有价值的结果。
- **Discovery/Web-Content/directory-list-2.3-medium.txt**：这是一个更全面、专门针对目录名的字典。当你需要更深入地探测潜在目录时，这是一个很好的选择。
- **Discovery/Web-Content/raft-large-directories.txt**：该字典汇集了来自多个来源的海量目录名，对于全面的模糊测试任务非常有用。
- **Discovery/Web-Content/big.txt**：顾名思义，这是一个超大型字典，同时包含目录名与文件名。当你希望大范围扫描、探索所有可能性时非常实用。

## ffuf

我们将使用 ffuf 完成本次模糊测试任务。ffuf 的基本工作流程如下：

- **字典**：为 ffuf 提供包含潜在目录或文件名的字典。
- **带 FUZZ 关键字的 URL**：构造一个包含 `FUZZ` 关键字的 URL 作为占位符，字典中的内容会被填入此处。
- **发送请求**：ffuf 遍历字典，将 URL 中的 `FUZZ` 替换为每一条内容，并向目标 Web 服务器发送 HTTP 请求。
- **响应分析**：ffuf 分析服务器的响应（状态码、响应包长度等），并根据你的规则过滤结果。

例如，如果你想对目录进行模糊测试，可以使用类似这样的 URL：

```http
http://localhost/FUZZ
```

ffuf 会将 `FUZZ` 替换为你所选字典中的单词，如 `admin`、`backup`、`uploads` 等，然后向 `http://localhost/admin`、`http://localhost/backup` 等地址发送请求。

## 目录模糊测试

第一步是执行**目录模糊测试**，这能帮助我们发现 Web 服务器上的隐藏目录。我们将使用以下 ffuf 命令：

```shell
$ ffuf -w /usr/share/seclists/Discovery/Web-Content/directory-list-2.3-medium.txt -u http://IP:PORT/FUZZ

```

* -w（字典）：指定我们要使用的字典文件路径。本示例中，我们使用的是 SecLists 库中一个中等规模的目录字典。
* -u（统一资源定位符 / 网址）：指定要进行模糊测试的基础 URL。`FUZZ` 关键字作为占位符，模糊测试工具会将字典中的内容插入到该位置。

上述输出结果显示，ffuf 在目标 Web 服务器上发现了一个名为 `w2ksvrus` 的目录（可通过 301 状态码「永久重定向」确认）。这一发现可能成为后续深入排查的潜在切入点。

## 文件模糊测试

目录模糊测试的核心是查找文件夹，而文件模糊测试则更进一步，旨在发现这些目录内甚至 Web 应用根目录下的特定文件。Web 应用会借助各类文件类型提供内容、实现不同功能，常见的文件扩展名包括：

- `.php`：包含 PHP 代码的文件（PHP 是主流的服务器端脚本语言）；
- `.html`：定义网页结构与内容的文件；
- `.txt`：纯文本文件，常用来存储简单信息或日志；
- `.bak`：备份文件，用于在文件出错或被修改时保留其历史版本；
- `.js`：包含 JavaScript 代码的文件，为网页增加交互性和动态功能。

通过结合通用文件名字典，针对这些常见扩展名开展模糊测试，我们能更大概率发现那些被意外暴露或配置不当的文件 —— 这类文件可能导致信息泄露，或引发其他安全漏洞。

例如，若目标网站使用 PHP 开发，一旦发现 `config.php.bak` 这类备份文件，可能会泄露数据库凭证、API 密钥等敏感信息；同理，找到 `test.php` 这类废弃 / 未使用的脚本文件，也可能暴露可被攻击者利用的漏洞。

请使用 ffuf 工具，并搭配通用文件名字典，搜索带有特定扩展名的隐藏文件：

```shell
$ ffuf -w /usr/share/seclists/Discovery/Web-Content/common.txt -u http://IP:PORT/w2ksvrus/FUZZ -e .php,.html,.txt,.bak,.js -v 

        /'___\  /'___\           /'___\   
       /\ \__/ /\ \__/  __  __  /\ \__/   
       \ \ ,__\\ \ ,__\/\ \/\ \ \ \ ,__\  
        \ \ \_/ \ \ \_/\ \ \_\ \ \ \ \_/  
         \ \_\   \ \_\  \ \____/  \ \_\   
          \/_/    \/_/   \/___/    \/_/   

       v2.1.0-dev
________________________________________________

 :: Method           : GET
 :: URL              : http://IP:PORT/w2ksvrus/FUZZ.html
 :: Wordlist         : FUZZ: /usr/share/seclists/Discovery/Web-Content/common.txt
 :: Extensions       : .php .html .txt .bak .js 
 :: Follow redirects : false
 :: Calibration      : false
 :: Timeout          : 10
 :: Threads          : 40
 :: Matcher          : Response status: 200-299,301,302,307,401,403,405,500
________________________________________________

[Status: 200, Size: 111, Words: 2, Lines: 2, Duration: 0ms]
| URL | http://IP:PORT/w2ksvrus/dblclk.html
    * FUZZ: dblclk

[Status: 200, Size: 112, Words: 6, Lines: 2, Duration: 0ms]
| URL | http://IP:PORT/w2ksvrus/index.html
    * FUZZ: index

:: Progress: [28362/28362] :: Job [1/1] :: 0 req/sec :: Duration: [0:00:00] :: Errors: 0 ::
```

* -e 指定要扫描的文件扩展名，一次可以指定多个，例如：-e .php,.html,.bak
* -v 开启详细输出模式（verbose），会显示每个请求的详细信息，方便调试。

## 递归模糊测试

到目前为止，我们的测试重点集中在 Web 根目录下的一级目录，以及单个目录内的文件。但如果目标应用存在包含多层嵌套目录的复杂结构该怎么办？手动逐层测试不仅繁琐，还极其耗时 —— 这正是递归模糊测试的用武之地。

### 递归模糊测试的工作原理

递归模糊测试是一种自动化探索 Web 应用目录结构深层内容的方法，核心流程分为三个简单步骤：

#### 1. 初始测试阶段

- 模糊测试从顶层目录（通常是 Web 根目录 `/`）开始；
- 测试工具根据提供的字典（包含潜在目录名和文件名）发起请求；
- 分析服务器响应，筛选出表明目录存在的有效结果（例如 HTTP 200 OK 状态码）。

#### 2. 目录发现与扩展阶段

- 当发现有效目录时，工具不会仅记录该目录，而是为其创建新的测试分支 —— 本质上是将该目录名拼接至基础 URL 后；
- 例如，若在根目录下发现 `admin` 目录，工具会生成新的测试分支：`http://localhost/admin/`；
- 这个新分支将作为新一轮模糊测试的起点，工具会再次遍历字典，将每个条目拼接至新分支 URL 后发起请求（如 `http://localhost/admin/FUZZ`）。

#### 3. 迭代深入阶段

- 上述过程会针对每个新发现的目录重复执行，不断创建新分支，将测试范围向 Web 应用目录结构的更深处拓展；
- 该过程会持续到达到指定的深度限制（例如最多深入三层），或不再发现新的有效目录为止。

可以将整个过程想象成一棵树：Web 根目录是树干，每个发现的目录是分支，递归模糊测试会系统性地探索每一条分支，不断向深处延伸，直到触及 “树叶”（文件）或预设的终止条件。

## 为什么要使用递归模糊测试？

面对结构复杂的 Web 应用，递归模糊测试是必不可少的实用手段：

- **高效性**：自动化发现嵌套目录，相比手动探索节省大量时间；
- **全面性**：系统性遍历目录结构的每一条分支，降低遗漏隐藏资产的风险；
- **减少人工操作**：无需手动输入每个新目录进行测试，工具会自动完成全流程；
- **可扩展性**：对于大型 Web 应用而言尤为重要 —— 这类场景下手动探索几乎不具备可行性。

本质上，递归模糊测试的核心是 “巧干而非蛮干”。它能帮助你高效、全面地探测 Web 应用的深层结构，发现那些潜藏在隐蔽角落的潜在漏洞。

## 使用 ffuf 执行递归模糊测试

请通过页面底部的题目区域启动目标系统，并将文中的 `IP:PORT` 替换为你所启动实例的地址和端口。本次测试将使用字典文件 `/usr/share/seclists/Discovery/Web-Content/directory-list-2.3-medium.txt`。

接下来，我们用 ffuf 演示递归模糊测试的具体操作：

```shell
$ ffuf -w word.txt -ic -v -u http://IP:PORT/FUZZ -e .html -recursion 

        /'___\  /'___\           /'___\   
       /\ \__/ /\ \__/  __  __  /\ \__/   
       \ \ ,__\\ \ ,__\/\ \/\ \ \ \ ,__\  
        \ \ \_/ \ \ \_/\ \ \_\ \ \ \ \_/  
         \ \_\   \ \_\  \ \____/  \ \_\   
          \/_/    \/_/   \/___/    \/_/   

       v2.1.0-dev
________________________________________________

 :: Method           : GET
 :: URL              : http://IP:PORT/FUZZ
 :: Wordlist         : FUZZ: /usr/share/seclists/Discovery/Web-Content/directory-list-2.3-medium.txt
 :: Extensions       : .html 
 :: Follow redirects : false
 :: Calibration      : false
 :: Timeout          : 10
 :: Threads          : 40
 :: Matcher          : Response status: 200-299,301,302,307,401,403,405,500
________________________________________________

[Status: 301, Size: 0, Words: 1, Lines: 1, Duration: 0ms]
| URL | http://IP:PORT/level1
| --> | /level1/
    * FUZZ: level1

[INFO] Adding a new job to the queue: http://IP:PORT/level1/FUZZ

[INFO] Starting queued job on target: http://IP:PORT/level1/FUZZ

[Status: 200, Size: 96, Words: 6, Lines: 2, Duration: 0ms]
| URL | http://IP:PORT/level1/index.html
    * FUZZ: index.html

[Status: 301, Size: 0, Words: 1, Lines: 1, Duration: 0ms]
| URL | http://IP:PORT/level1/level2
| --> | /level1/level2/
    * FUZZ: level2

[INFO] Adding a new job to the queue: http://IP:PORT/level1/level2/FUZZ

[Status: 301, Size: 0, Words: 1, Lines: 1, Duration: 0ms]
| URL | http://IP:PORT/level1/level3
| --> | /level1/level3/
    * FUZZ: level3

[INFO] Adding a new job to the queue: http://IP:PORT/level1/level3/FUZZ

[INFO] Starting queued job on target: http://IP:PORT/level1/level2/FUZZ

[Status: 200, Size: 96, Words: 6, Lines: 2, Duration: 0ms]
| URL | http://IP:PORT/level1/level2/index.html
    * FUZZ: index.html

[INFO] Starting queued job on target: http://IP:PORT/level1/level3/FUZZ

[Status: 200, Size: 126, Words: 8, Lines: 2, Duration: 0ms]
| URL | http://IP:PORT/level1/level3/index.html
    * FUZZ: index.html

:: Progress: [441088/441088] :: Job [4/4] :: 100000 req/sec :: Duration: [0:00:06] :: Errors: 0 ::
```

|           参数 | 说明                                                                                                                                                                                                   |
| -------------: | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `-recursion` | 开启递归扫描，让 ffuf 自动对新发现的目录继续扫描<br />例如，当工具在扫描 http://localhost/FUZZ 时发现了一个名为 admin 的目录，它会自动将扫描目标切换至 http://localhost/admin/FUZZ，开启新一轮的爆破。 |
|        `-ic` | 忽略字典里以 `#` 开头的注释行，不发送无效请求参数                                                                                                                                                    |

模糊测试从 Web 根目录（http://IP:PORT/FUZZ）开始。

起初，ffuf 收到一个 301（永久重定向）响应，识别出 level1 目录。重定向的发生促使工具立即在该目录内启动下一层级的模糊测试，从而有效地扩展了搜索分支。

当 ffuf 递归扫描 level1 时，进一步发现了 level2 和 level3 两个子目录（同时在 level1 下发现了一个 index.html 文件）。这些新目录被自动加入测试队列，扫描深度持续扩展。

工具按队列系统性地向后扫描，在 level2 和 level3 中均成功识别出 index.html 文件。

### 注意合规使用

递归模糊测试虽是一项强大的技术，但也会大量消耗资源，尤其在大型 Web 应用中表现明显。过量的请求可能压垮目标服务器，导致性能异常，或触发安全防护机制。

为降低此类风险，ffuf 提供了可精细调节递归模糊测试过程的参数：

| 参数                 | 作用                                                                                                     |
| -------------------- | -------------------------------------------------------------------------------------------------------- |
| `-recursion-depth` | 设置递归扫描的最大深度。例如 `-recursion-depth 2` 会将扫描限制在两层深度内（起始目录及其直接子目录）。 |
| `-rate`            | 控制 ffuf 每秒发送的请求数，避免服务器过载。                                                             |
| `-timeout`         | 设定单个请求的超时时间，防止工具在无响应的目标上卡顿。                                                   |

```shell
$ ffuf -w word.txt -ic -u http://IP:PORT/FUZZ -e .html -recursion -recursion-depth 2 -rate 500
```


# 参数与值模糊测试

在发现隐藏目录和文件的基础上，我们接下来深入讲解**参数与值模糊测试**。该技术聚焦于篡改 Web 请求中的参数及其值，以此发现应用程序在处理输入时存在的漏洞。

参数是 Web 应用的“信使”，负责在浏览器与承载 Web 应用的服务器之间传递关键信息。它们类似于编程中的变量，存储的特定值会影响应用程序的行为逻辑。

## GET 参数：公开传递信息

你经常能在 URL 中直接看到 GET 参数，它们紧跟在问号（`?`）之后，多个参数之间用和号（`&`）连接。例如：

```http
https://example.com/search?query=fuzzing&category=security
```

在这个 URL 中：

- `query` 是参数，对应值为 "fuzzing"
- `category` 是另一个参数，对应值为 "security"

GET 参数就像明信片——其包含的信息任何人只要查看 URL 就能看到。它们主要用于不会改变服务器状态的操作，比如搜索或筛选。

## POST 参数：后台私密通信

如果说 GET 参数是公开的明信片，POST 参数则更像密封的信封，其携带的信息会隐秘地放在 HTTP 请求的请求体中。它们不会直接显示在 URL 里，因此是传输登录凭证、个人信息或财务明细等敏感数据的首选方式。

当你提交表单，或与使用 POST 请求的网页交互时，会发生以下过程：

1. **数据收集**：收集表单字段中输入的信息，并准备传输；
2. **编码处理**：将数据编码为特定格式，通常是 `application/x-www-form-urlencoded` 或 `multipart/form-data`：
   - `application/x-www-form-urlencoded`：该格式将数据编码为用和号（`&`）分隔的键值对，与 GET 参数格式类似，但会放在请求体而非 URL 中；
   - `multipart/form-data`：该格式用于提交文件及其他数据，会将请求体拆分为多个部分，每个部分包含一段特定数据或一个文件；
3. **HTTP 请求发送**：将编码后的数据放入 HTTP POST 请求的请求体中，发送至 Web 服务器；
4. **服务端处理**：服务器接收 POST 请求，解码数据，并根据应用程序逻辑进行处理。

以下是提交登录表单时，POST 请求的简化示例：

```http
POST /login HTTP/1.1
Host: example.com
Content-Type: application/x-www-form-urlencoded

username=your_username&password=your_password
```

- `POST`：表示 HTTP 请求方法为 POST；
- `/login`：指定表单数据发送到的 URL 路径；
- `Content-Type`：指定请求体中数据的编码方式（本例中为 `application/x-www-form-urlencoded`）；
- `Request Body`：包含编码后的表单数据，以键值对形式呈现（`username` 和 `password`）。

## 为什么参数对模糊测试至关重要

参数是与 Web 应用交互的“入口”。通过篡改参数值，你可以测试应用对不同输入的响应，进而可能发现漏洞。例如：

- 修改购物车 URL 中的商品 ID，可能暴露价格错误或未授权访问其他用户订单的问题；
- 修改请求中的隐藏参数，可能解锁隐藏功能或管理权限；
- 向搜索查询中注入恶意代码，可能暴露跨站脚本（XSS）或 SQL 注入（SQLi）等漏洞。

## wenum 工具实操

本节中，我们将使用 wenum 工具探测目标 Web 应用中的 GET 和 POST 参数，最终目标是找到能触发独特响应的隐藏值，进而可能发现漏洞。

请通过页面底部的题目区域启动目标系统，并将文中的 `IP:PORT` 替换为你启动实例的地址和端口。本次测试将使用字典文件 `/usr/share/seclists/Discovery/Web-Content/common.txt`。

首先，在攻击主机上安装 wenum 工具：

```shellsession
$ pipx install git+https://github.com/WebFuzzForge/wenum
$ pipx runpip wenum install setuptools
```

接下来，先用 curl 手动与目标接口交互，了解其行为：

```shellsession
$ curl http://IP:PORT/get.php

Invalid parameter value
x:
```

响应结果表明参数 `x` 缺失。我们尝试添加一个值：

```shellsession
$ curl http://IP:PORT/get.php?x=1

Invalid parameter value
x: 1
```

此次服务器识别到了参数 `x`，但提示提供的值（1）无效。这说明应用程序确实会检查该参数的值，并根据其有效性返回不同响应。我们需要找到能触发不同（且更有价值）响应的特定值。

手动猜测参数值既繁琐又耗时，而 wenum 可以解决这个问题。它能自动化测试大量潜在值，大幅提升找到正确值的概率。

使用 wenum 对参数 `x` 的值进行模糊测试，以 SecLists 中的 `common.txt` 作为字典：

```shellsession
$ wenum -w /usr/share/seclists/Discovery/Web-Content/common.txt --hc 404 -u "http://IP:PORT/get.php?x=FUZZ"

...
 Code    Lines     Words        Size  Method   URL 
...
 200       1 L       1 W        25 B  GET      http://IP:PORT/get.php?x=OA... 

Total time: 0:00:02
Processed Requests: 4731
Filtered Requests: 4730
Requests/s: 1681
```

参数说明：

- `-w`：指定字典文件路径；
- `--hc 404`：隐藏返回 404 状态码（未找到）的响应（wenum 默认会记录所有发起的请求）；
- `http://IP:PORT/get.php?x=FUZZ`：目标 URL，wenum 会将参数值占位符 `FUZZ` 替换为字典中的内容。

分析结果会发现，大多数请求都会返回“Invalid parameter value”（无效参数值）及你尝试的错误值，但有一行结果格外突出：

```bash
 200       1 L       1 W        25 B  GET      http://IP:PORT/get.php?x=OA...
```

这表明当参数 `x` 的值设为 "OA..." 时，服务器返回了 200 OK 状态码，说明该输入有效。

## POST 参数模糊测试

对 POST 参数进行模糊测试的方法与 GET 参数略有不同。我们不会将值直接追加到 URL 后，而是使用 ffuf 将载荷发送到请求体中。这能测试应用程序如何处理通过表单或其他 POST 机制提交的数据。

目标应用的 `post.php` 脚本中也有一个名为 `y` 的 POST 参数。先用 curl 探测其默认行为：

```shellsession
$ curl -d "" http://IP:PORT/post.php

Invalid parameter value
y:
```

`-d` 参数指示 curl 发送一个请求体为空的 POST 请求，响应结果表明应用期望获取参数 `y`，但未接收到。

与 GET 参数一样，手动测试 POST 参数值效率极低，我们将使用 ffuf 自动化完成这一过程：

```shell
$ ffuf -u http://IP:PORT/post.php -X POST -H "Content-Type: application/x-www-form-urlencoded" -d "y=FUZZ" -w common.txt -mc 200 -v

        /'___\  /'___\           /'___\   
       /\ \__/ /\ \__/  __  __  /\ \__/   
       \ \ ,__\\ \ ,__\/\ \/\ \ \ \ ,__\  
        \ \ \_/ \ \ \_/\ \ \_\ \ \ \ \_/  
         \ \_\   \ \_\  \ \____/  \ \_\   
          \/_/    \/_/   \/___/    \/_/   

       v2.1.0-dev
________________________________________________

 :: Method           : POST
 :: URL              : http://IP:PORT/post.php
 :: Wordlist         : FUZZ: /usr/share/seclists/Discovery/Web-Content/common.txt
 :: Header           : Content-Type: application/x-www-form-urlencoded
 :: Data             : y=FUZZ
 :: Follow redirects : false
 :: Calibration      : false
 :: Timeout          : 10
 :: Threads          : 40
 :: Matcher          : Response status: 200
________________________________________________

[Status: 200, Size: 26, Words: 1, Lines: 2, Duration: 7ms]
| URL | http://IP:PORT/post.php
    * FUZZ: SU...

:: Progress: [4730/4730] :: Job [1/1] :: 5555 req/sec :: Duration: [0:00:01] :: Errors: 0 ::
```

|  参数  | 作用                                         |
| :----: | :------------------------------------------- |
| `-d` | 指定 POST 请求体数据，将载荷放在请求体中发送 |

这里的**主要区别**在于使用了 **`-d`** 参数，它告诉 ffuf 将载荷（`y=FUZZ`）作为 POST 数据放在**请求体**中发送。

同样，你会看到大部分响应都是「无效参数」。而正确的值（`SU...`）会以 **200 OK** 状态码返回

在现实场景中，这些标志不会存在，而识别有效的参数值可能需要对响应进行更细致的分析。然而，这个练习提供了一个简化的演示，说明如何利用ffuf来自动化测试许多潜在的参数值。

# Virtual Host & Subdomain模糊测试

虚拟主机（vhost）和子域名(Subdomain)在组织与管理 Web 内容方面都起着**关键作用**。

**虚拟主机**允许在**单一服务器或 IP 地址**上托管多个网站或域名。每个虚拟主机都对应一个唯一的域名或主机名。当客户端发送 HTTP 请求时，Web 服务器会检查 **Host 请求头**，以决定返回哪个虚拟主机的内容。这能高效利用资源、降低成本，因为多个网站可以共享同一套服务器基础设施。

**子域名**则是主域名的扩展，用于在域名内形成层级结构，通常用来划分网站不同板块或服务。
例如：`blog.example.com` 和 `shop.example.com` 就是主域名 `example.com` 的子域名。
与虚拟主机不同，子域名通过 **DNS 记录**解析到具体 IP 地址。

| 特点     | 虚拟主机（Virtual Hosts）                | 子域名（Subdomains）                             |
| -------- | ---------------------------------------- | ------------------------------------------------ |
| 识别方式 | 通过 HTTP 请求中的**Host 头** 识别 | 通过**DNS 记录** 识别，指向具体 IP         |
| 主要作用 | 在**一台服务器上托管多个网站**     | 用于在同一个网站内**划分不同板块或服务**   |
| 安全风险 | 配置错误可能暴露内部应用或敏感数据       | DNS 记录管理不当可能出现**子域名接管**漏洞 |

## Gobuster 工具

Gobuster 是一款多功能命令行工具，以**目录/文件扫描**和 **DNS 爆破** 能力著称。它可以系统性地探测目标服务器或域名，发现隐藏目录、文件和子域名，是安全评估与渗透测试中的常用工具。

Gobuster 可用于模糊测试多种内容：

- **目录**：发现服务器上的隐藏目录
- **文件**：识别指定后缀的文件（如 `.php`、`.txt`、`.bak`）
- **子域名**：枚举目标域名的子域名
- **虚拟主机（vhost）**：通过修改 Host 头发现隐藏的虚拟主机

## Gobuster 虚拟主机模糊测试

虽然 Gobuster 常被用于目录和文件枚举，但它同样支持**虚拟主机发现**，是评估服务器安全状况的实用工具。

使用下面的命令将指定虚拟主机添加到你的 `hosts` 文件（把 `IP` 换成你实例的地址）。
本次使用字典：`/usr/share/seclists/Discovery/Web-Content/common.txt`

## Gobuster vhost 命令详解

```shellsession
$ gobuster vhost -u http://inlanefreight.htb:81 -w /usr/share/seclists/Discovery/Web-Content/common.txt --append-domain
```

| 参数                | 作用                                                                                                                    |
| ------------------- | ----------------------------------------------------------------------------------------------------------------------- |
| `gobuster vhost`  | 启用**虚拟主机扫描模式**，让工具专注发现虚拟主机，而不是目录或文件。                                              |
| `-u <URL>`        | 指定目标基础 URL                                                                                                        |
| `-w <字典路径>`   | 指定用于爆破虚拟主机名的字典                                                                                            |
| `--append-domain` | 自动把主域名 `inlanefreight.htb` 拼接到字典里的每个单词后面，保证 Host 头是完整域名（如 `admin.inlanefreight.htb`） |

Gobuster 会遍历字典，拼接主域名，然后用不同的 Host 头发起请求，通过服务器响应（状态码、响应长度等）识别**有效但未公开的虚拟主机**。

运行该命令即可对目标执行虚拟主机扫描。

```shell
$ gobuster vhost -u http://inlanefreight.htb:81 -w /usr/share/seclists/Discovery/Web-Content/common.txt --append-domain

===============================================================
Gobuster v3.6
by OJ Reeves (@TheColonial) & Christian Mehlmauer (@firefart)
===============================================================
[+] Url:             http://inlanefreight.htb:81
[+] Method:          GET
[+] Threads:         10
[+] Wordlist:        /usr/share/SecLists/Discovery/Web-Content/common.txt
[+] User Agent:      gobuster/3.6
[+] Timeout:         10s
[+] Append Domain:   true
===============================================================
Starting gobuster in VHOST enumeration mode
===============================================================
Found: .git/logs/.inlanefreight.htb:81 Status: 400 [Size: 157]
...
Found: admin.inlanefreight.htb:81 Status: 200 [Size: 100]
Found: android/config.inlanefreight.htb:81 Status: 400 [Size: 157]
...
Progress: 4730 / 4730 (100.00%)
===============================================================
Finished
===============================================================
```


扫描完成后，我们会看到一份结果列表。**状态码为 200 的虚拟主机尤其值得关注**。在 HTTP 中，200 状态码表示响应成功，说明该虚拟主机有效且可以访问。例如，这一行：
Found: admin.inlanefreight.htb:81  Status: 200 [Size: 100]
表示已找到虚拟主机 admin.inlanefreight.htb，并且访问成功。

## Gobuster 子域名模糊测试

虽然 Gobuster 常被用于虚拟主机和目录发现，但它在**子域名枚举**方面同样出色，这是绘制目标域名攻击面的关键步骤。通过系统性地测试潜在子域名的各种组合，Gobuster 可以发现隐藏或被遗忘的子域名，这些子域名可能存放着有价值的信息或存在漏洞。

我们来分解这条 Gobuster 子域名扫描命令：

```shellsession
$ gobuster dns -d inlanefreight.com -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt
```

| 参数              | 作用                                                                  |
| ----------------- | --------------------------------------------------------------------- |
| `gobuster dns`  | 启用 Gobuster 的**DNS 模糊测试模式**，专注发现子域名            |
| `-d <域名>`     | 指定要枚举子域名的目标域名                                            |
| `-w <字典路径>` | 指定用于生成子域名的字典,本例使用的是包含最常见 5000 个子域名的字典。 |

Gobuster 的工作原理：
根据字典生成子域名，拼接到目标域名后，通过 DNS 查询尝试解析这些子域名。如果某个子域名能解析到 IP 地址，就判定为有效，并输出到结果中。

运行这条命令后，Gobuster 会输出类似如下内容：

```shell
$ gobuster dns -d inlanefreight.com -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt 

===============================================================
Gobuster v3.6
by OJ Reeves (@TheColonial) & Christian Mehlmauer (@firefart)
===============================================================
[+] Domain:     inlanefreight.com
[+] Threads:    10
[+] Timeout:    1s
[+] Wordlist:   /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt
===============================================================
Starting gobuster in DNS enumeration mode
===============================================================
Found: www.inlanefreight.com

Found: blog.inlanefreight.com

...

Progress: 4989 / 4990 (99.98%)
===============================================================
Finished
===============================================================
```

在输出结果中，**以 Found: 开头的每一行**，都表示 Gobuster 发现了一个有效子域名。

> 注意：在最新版 Gobuster 中，`-d` 现在表示**请求之间的延迟**，而不是域名。如果你使用最新版本，请用 `--do` 或 `--domain` 来指定目标域名。
>


# 模糊测试输出过滤

像 gobuster、ffuf、wfuzz 这类 Web 模糊测试工具都会进行全面扫描，通常会产生**海量数据**。在这些输出里筛选有效信息非常困难。不过这些工具都提供了强大的过滤功能，帮你简化分析、只关注关键结果。

## Gobuster

Gobuster 根据不同模块提供多种过滤选项，帮你聚焦特定响应、简化分析。
注意：**-s 和 -b 仅在目录扫描模式下可用**。

| 参数                 | 说明                                     | 示例场景                                               |
| -------------------- | ---------------------------------------- | ------------------------------------------------------ |
| `-s`（包含）       | 只保留指定状态码的响应（逗号分隔）       | 只想看重定向，过滤 301,302,307                         |
| `-b`（排除）       | 排除指定状态码的响应（逗号分隔）         | 服务器返回大量 404，用 `-b 404` 排除                 |
| `--exclude-length` | 排除指定长度的响应（逗号分隔，支持范围） | 排除 0 字节或 404 字节响应：`--exclude-length 0,404` |

示例命令：

```shellsession
#查找状态码为 200 或 301 的目录，但排除大小为 0 的响应（空响应）
gobuster dir -u http://example.com/ -w wordlist.txt -s 200,301 --exclude-length 0
```

## FFUF

FFUF 提供高度可定制的过滤系统，可以精确控制显示的输出。这让你能够高效地从海量数据中筛选内容，专注于最相关的发现。

FFUF 的过滤选项分为多种类型，每种都用于优化你的结果。

| 参数                  | 说明                                                                                                                                           | 示例场景                                                                          |
| --------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------- |
| `-mc` (match code)  | 只保留匹配指定状态码的响应。可单个、逗号分隔、或短横线表示范围（如 200,204,301,400-499）。默认匹配：200-299、301、302、307、401、403、405、500 | 模糊测试后你看到很多 302 重定向，但主要关心 200 响应。使用 `-mc 200` 只保留这些 |
| `-fc` (filter code) | 排除匹配指定状态码的响应，格式同 `-mc`。常用于剔除 404 这类常见错误码                                                                        | 扫描返回大量 404，使用 `-fc 404` 移除它们                                       |
| `-fs` (filter size) | 排除指定大小或范围的响应。可单个或短横线范围（如 `-fs 0` 表示空响应，`-fs 100-200` 表示 100–200 字节）                                    | 你认为有效响应会大于 1KB，使用 `-fs 0-1023` 过滤掉小响应                        |
| `-ms` (match size)  | 只保留匹配指定大小或范围的响应，格式同 `-fs`                                                                                                 | 你在找一个确切 3456 字节的备份文件，使用 `-ms 3456`                             |
| `-fw` (filter word) | 排除响应中包含指定单词数量的结果                                                                                                               | 过滤掉单词数为 219 的响应，使用 `-fw 219`                                       |
| `-mw` (match word)  | 只保留响应体中包含指定单词数量的结果                                                                                                           | 你在寻找简短的特定错误信息，使用 `-mw 5-10`                                     |
| `-fl` (filter line) | 排除指定行数或行数范围的响应。例如 `-fl 5` 会过滤掉 5 行的响应                                                                               | 你发现一种固定 10 行的错误信息，使用 `-fl 10` 过滤                              |
| `-ml` (match line)  | 只保留响应体中包含指定行数的结果                                                                                                               | 你在寻找特定格式（如 20 行）的响应，使用 `-ml 20`                               |
| `-mt` (match time)  | 只保留满足首字节响应时间（TTFB）条件的响应。用于识别异常慢 / 快、可能有价值的响应                                                              | 应用处理某些输入时响应很慢，使用 `-mt >500` 找出超过 500ms 的响应               |

示例命令：

你可以组合多个过滤器。例如：

```
# 查找状态码 200、按单词数过滤、且响应大小大于 500 字节的目录
ffuf -u http://example.com/FUZZ -w wordlist.txt -mc 200 -fw 427 -ms >500

# 过滤掉状态码 404、401、302 的响应
ffuf -u http://example.com/FUZZ -w wordlist.txt -fc 404,401,302

# 查找扩展名为 .bak、大小在 10KB~100KB 之间的备份文件
ffuf -u http://example.com/FUZZ.bak -w wordlist.txt -fs 0-10239 -ms 10240-102400

# 发现响应时间超过 500ms 的接口
ffuf -u http://example.com/FUZZ -w wordlist.txt -mt >500
```

## wenum

wenum 支持按状态码、大小、行数、单词数、正则过滤。

| 参数                         | 说明                                      | 示例场景                                                                               |
| ---------------------------- | ----------------------------------------- | -------------------------------------------------------------------------------------- |
| `--hc` (hide code)         | 排除匹配指定状态码的响应                  | 服务器返回大量 400 错误，使用 `--hc 400` 隐藏它们                                    |
| `--sc` (show code)         | 只保留匹配指定状态码的响应                | 你只关心成功请求（200 OK），使用 `--sc 200`                                          |
| `--hl` (hide length)       | 排除指定内容长度（按行）的响应            | 服务器返回冗长错误信息，使用高值 `--hl` 隐藏                                         |
| `--sl` (show length)       | 只保留指定内容长度（按行）的响应          | 你怀疑某已知行数的响应和漏洞有关，使用 `--sl` 定位                                   |
| `--hw` (hide word)         | 排除指定单词数的响应                      | 服务器很多响应包含相同短语，使用 `--hw` 过滤                                         |
| `--sw` (show word)         | 只保留指定单词数的响应                    | 你在寻找简短错误信息，使用低值 `--sw`                                                |
| `--hs` (hide size)         | 排除指定响应大小（字节 / 字符）的响应     | 服务器有效请求会返回大文件，使用 `--hs` 过滤大响应                                   |
| `--ss` (show size)         | 只保留指定响应大小（字节 / 字符）的响应   | 你在寻找特定大小的文件，使用 `--ss`                                                  |
| `--hr` (hide regex)        | 排除响应体匹配指定正则的结果              | 过滤包含 "Internal Server Error" 的响应：`--hr "Internal Server Error"`              |
| `--sr` (show regex)        | 只保留响应体匹配指定正则的结果            | 过滤包含 "admin" 的响应：`--sr "admin"`                                              |
| `--filter / --hard-filter` | 通用过滤器，用于显示 / 隐藏或阻止后续处理 | `--filter "Login"` 只显示含 Login 的响应；`--hard-filter "Login"` 隐藏且不插件处理 |

示例命令：

```shellsession
# 只显示成功请求和重定向
wenum -w wordlist.txt --sc 200,301,302 -u https://example.com/FUZZ

# 隐藏常见错误码
wenum -w wordlist.txt --hc 404,400,500 -u https://example.com/FUZZ

# 只显示简短错误信息（5~10 个单词）
wenum -w wordlist.txt --sw 5-10 -u https://example.com/FUZZ

# 隐藏大文件，专注小响应
wenum -w wordlist.txt --hs 10000 -u https://example.com/FUZZ

# 过滤包含特定内容的响应
wenum -w wordlist.txt --sr "admin\|password" -u https://example.com/FUZZ
```

## Feroxbuster

| 参数                      | 说明                                           | 示例场景                                                     |
| ------------------------- | ---------------------------------------------- | ------------------------------------------------------------ |
| `--dont-scan` (Request) | 排除特定 URL 或模式（即使递归时从链接中发现）  | 你知道 /uploads 只有图片，使用 `--dont-scan /uploads` 排除 |
| `-S, --filter-size`     | 按大小（字节）排除响应，支持单个或逗号分隔范围 | 你发现很多 1KB 错误页，使用 `-S 1024` 排除                 |
| `-X, --filter-regex`    | 排除响应体 / 响应头匹配指定正则的结果          | 使用 `-X "Access Denied"` 过滤含该错误信息的页面           |
| `-W, --filter-words`    | 排除指定单词数或范围的响应                     | 使用 `-W 0-10` 剔除单词数极少的响应（如错误信息）          |
| `-N, --filter-lines`    | 排除指定行数或范围的响应                       | 使用 `-N 50-` 过滤冗长页面                                 |
| `-C, --filter-status`   | 排除指定 HTTP 状态码（黑名单）                 | 使用 `-C 404,500` 屏蔽常见错误                             |
| `--filter-similar-to`   | 排除与给定页面相似的响应                       | 使用 `--filter-similar-to error.html` 移除重复 / 近似页面  |
| `-s, --status-codes`    | 只保留指定状态码（白名单，默认全部）           | 使用 `-s 200,204,301,302` 专注成功响应                     |

示例命令：

```shell
# 查找状态码 200 的目录，排除大于 10KB 或包含 error 单词的响应
feroxbuster --url http://example.com -w wordlist.txt -s 200 -S 10240 -X "error"
```

## 快速演示

本次模糊测试使用字典：`/usr/share/seclists/Discovery/Web-Content/common.txt`

到目前为止的模块中，你可能已经注意到有些命令已经使用了某种结果过滤，或者工具自身默认应用了过滤。例如，使用 ffuf 进行 POST 模糊测试时，如果我们去掉匹配状态码的过滤器，ffuf 会使用一系列默认过滤规则。

```shell
ffuf -u http://IP:PORT/post.php -X POST -H "Content-Type: application/x-www-form-urlencoded" -d "y=FUZZ" -w /usr/share/seclists/Discovery/Web-Content/common.txt -v
```

在上面的输出中，这一行：

```
:: Matcher : Response status: 200-299,301,302,307,401,403,405,500
```

表明 ffuf **默认只匹配这些状态码**。这种有意的过滤可以最大限度减少 404 带来的干扰，确保你关注的结果保持突出。

为了说明不使用过滤可能带来的问题，我们使用 `-mc all` 参数匹配**所有状态码**，再运行一次扫描：

```shell
ffuf -u http://IP:PORT/post.php -X POST -H "Content-Type: application/x-www-form-urlencoded" -d "y=FUZZ" -w /usr/share/seclists/Discovery/Web-Content/common.txt -v -mc all
```

输出结果会被大量 **404 NOT FOUND** 淹没，让你很难识别出任何有潜在价值的发现。这充分说明了**使用合适的过滤技术对优化模糊测试流程、优先关注有效结果至关重要**。


# 验证发现结果

模糊测试擅长大范围探测并生成潜在线索，但并非所有发现都是真实漏洞。该过程常会产生**误报**——即触发了工具的检测机制，但本身无害、不构成实际威胁的异常现象。这就是验证成为模糊测试工作流程中关键一步的原因。

## 为什么要验证

验证发现结果有以下重要作用：

- **确认漏洞真实性**：确保发现的问题是真实漏洞，而非误报。
- **评估影响范围**：帮助判断漏洞的严重程度及其对 Web 应用可能造成的潜在影响。
- **复现问题**：提供稳定复现漏洞的方法，辅助制定修复或缓解方案。
- **收集证据**：整理漏洞证明，用于向开发人员或相关负责人展示。

## 手动验证

验证潜在漏洞最可靠的方式是**手动验证**，通常包括：

1. **复现请求**：使用 curl 或浏览器等工具，手动发送模糊测试中触发异常响应的相同请求。
2. **分析响应**：仔细检查返回结果，确认是否存在漏洞特征。留意错误信息、意外内容或偏离预期的行为。
3. **尝试利用**：若结果有价值，可在受控环境中尝试利用该漏洞，评估其影响与严重程度。此步骤必须谨慎操作，且仅在获得正式授权后进行。

为了负责任地验证和利用漏洞，避免任何可能损害生产系统或泄露敏感数据的行为至关重要。应专注于构建**概念验证（PoC）**，在不造成破坏的前提下证明漏洞存在。例如，若怀疑存在 SQL 注入漏洞，可以构造一条无害的 SQL 查询语句返回数据库版本信息，而不是尝试提取或修改敏感数据。

验证的目标是在遵守道德与法律准则的前提下，收集足够证据，让相关负责人认可漏洞的存在及其潜在影响。

## 示例

假设你的测试工具在 Web 服务器上发现了一个名为 `/backup/` 的目录。访问该目录返回 200 OK 状态码，说明这个目录存在且可访问。虽然乍一看这似乎无关紧要，但必须谨记：备份目录往往包含敏感信息。

备份文件的设计用途是保存数据，这意味着它们可能包含：

- **数据库导出文件**：这类文件可能包含完整的数据库内容，包括用户凭证、个人信息和其他机密数据。
- **配置文件**：这类文件可能存储 API 密钥、加密密钥或其他可被攻击者利用的敏感配置项。
- **源代码**：源代码的备份副本可能泄露漏洞或实现细节，被攻击者利用。

如果攻击者获取了这些文件，就有可能攻陷整个 Web 应用、窃取敏感数据或造成重大破坏。但作为安全专业人员，你在验证该问题时需采取合规方式——既要证明漏洞存在，又不能破坏目标系统的完整性，也避免自身承担潜在风险。

### 使用 curl 进行验证

首先，我们需要确认这个目录是否真的可以被浏览。可以通过 curl 工具来验证：

```shell
$ curl http://IP:PORT/backup/
```

查看终端中的输出结果。如果服务器返回 `/backup` 目录下包含的文件和子目录列表，就说明你已成功确认存在**目录遍历漏洞**。典型的返回结果如下所示：

```html
<!DOCTYPE html>
<html>
<head>
<title>Index of /backup/</title>
<style type="text/css">
[...]
</style>
</head>
<body>
<h2>Index of /backup/</h2>
<div class="list">
<table summary="Directory Listing" cellpadding="0" cellspacing="0">
<thead><tr><th class="n">Name</th><th class="m">Last Modified</th><th class="s">Size</th><th class="t">Type</th></tr></thead>
<tbody>
<tr class="d"><td class="n"><a href="../">..</a>/</td><td class="m"> </td><td class="s">-  </td><td class="t">Directory</td></tr>
<tr><td class="n"><a href="backup.sql">backup.sql</a></td><td class="m">2024-Jun-12 14:00:46</td><td class="s">0.2K</td><td class="t">application/octet-stream</td></tr>
</tbody>
</table>
</div>
<div class="foot">lighttpd/1.4.76</div>

<script type="text/javascript">
[...]
</script>

</body>
</html>
```

为了在不泄露敏感数据的前提下负责任地确认漏洞，最优方案是通过分析**响应头**来获取目录内文件的线索。具体来说，`Content-Type` 响应头通常能指示文件类型（例如，`application/sql` 表示数据库导出文件，`application/zip` 表示压缩备份文件）。

此外，需仔细检查 `Content-Length` 响应头：数值大于 0 说明文件包含实际内容；而长度为 0 的文件虽然可能存在异常，但不一定构成直接的安全漏洞。例如，若发现一个 `dump.sql` 文件的 `Content-Length` 为 0，说明它大概率是空文件——尽管出现在备份目录中存在可疑性，但并不直接等同于安全风险。

以下是使用 curl 仅获取 `password.txt` 文件响应头的示例：

```shell
$ curl -I http://IP:PORT/backup/password.txt
```

返回结果：

```
HTTP/1.1 200 OK
Content-Type: text/plain;charset=utf-8
ETag: "3406387762"
Last-Modified: Wed, 12 Jun 2024 14:08:46 GMT
Content-Length: 171
Accept-Ranges: bytes
Date: Wed, 12 Jun 2024 14:08:59 GMT
Server: lighttpd/1.4.76
```

对响应头的解读：

- `Content-Type: text/plain;charset=utf-8`：说明 `password.txt` 是一个纯文本文件，符合文件名的预期特征。
- `Content-Length: 171`：文件大小为 171 字节。虽然无法直接确定文件内容，但说明该文件非空，且大概率包含数据。结合文件名和其所处的备份目录位置，这一情况值得高度警惕。

这些响应头信息，再加上目录列表可访问的事实，构成了潜在安全风险的有力证据。我们已确认备份目录可被访问，且包含一个名为 `password.txt` 的非空文件——该文件极有可能包含敏感信息。

通过聚焦于响应头分析，你可以在不直接访问文件内容的前提下收集有价值的信息，既验证了漏洞的存在，又符合负责任的披露原则。


# Web API（网络应用程序编程接口）

**Web API（Web Application Programming Interface，网络应用程序编程接口）**是一套规则和规范，允许不同的软件应用程序在网络上进行通信。它充当一种通用语言，让各式各样的软件组件能够无缝交换数据与服务，而不受其底层技术或编程语言的限制。

本质上，**Web API** 是**服务器**（存放数据与功能）与**客户端**（如浏览器、移动端应用或另一台服务器）之间的桥梁，客户端希望通过它访问或使用服务器上的数据或功能。
Web API 有多种类型，每种都有各自的优势与适用场景。

## 前置知识

### REST（表述性状态传递）

**REST（Representational State Transfer，表述性状态传递）** API 是目前非常流行的 Web 服务架构风格。
它采用**无状态、客户端-服务器**通信模型，客户端向服务器发送请求以访问或操作资源。

**REST API** 使用标准 **HTTP 方法（GET、POST、PUT、DELETE）** 对由唯一 URL 标识的资源执行 **CRUD（增、查、改、删）** 操作。
它们通常以轻量级格式交换数据，如 **JSON** 或 **XML**，便于与各类应用和平台集成。

示例请求：

```http
GET /users/123
```

### SOAP（简单对象访问协议）

**SOAP（Simple Object Access Protocol，简单对象访问协议）** API 遵循更为正式、标准化的协议来交换结构化信息。
它使用 **XML** 定义消息，再将消息封装在 **SOAP 信封**中，通过 HTTP、SMTP 等网络协议传输。

**SOAP API** 通常内置安全、可靠性与事务管理功能，适合需要严格数据完整性与错误处理的企业级应用。

示例请求：

```xml
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
   <soapenv:Header/>
   <soapenv:Body>
      <tem:GetStockPrice>
         <tem:StockName>AAPL</tem:StockName>
      </tem:GetStockPrice>
   </soapenv:Body>
</soapenv:Envelope>
```

### GraphQL

**GraphQL** 是较新的 API 查询语言与运行时。
与 **REST API** 为不同资源开放多个接口不同，**GraphQL** 只提供**单一接口**，客户端可以使用灵活的查询语法精确请求所需数据。

这解决了 REST API 中常见的**数据过度获取（over-fetching）**或**数据获取不足（under-fetching）**问题。
GraphQL 的强类型与自省能力让 API 可以持续迭代，而不破坏现有客户端，是现代 Web 与移动应用的热门选择。

示例查询：

```graphql
query {
  user(id: 123) {
    name
    email
  }
}
```

### Web API 的优势

Web API 通过为客户端提供标准化方式访问和操作服务器存储的数据，彻底改变了应用开发与交互模式。
它们让开发者可以将应用的特定功能或服务开放给外部用户或其他应用，提升**代码复用性**，并促进**聚合应用（mashup）**与复合应用的开发。

此外，Web API 在集成第三方服务（如社交登录、安全支付、地图功能）方面至关重要，让开发者无需重复造轮子即可快速引入外部能力。

API 也是**微服务架构（microservices architecture）**的基石：大型单体应用被拆分为多个小型、独立的服务，通过定义良好的 API 通信。
这种架构提升了**可扩展性、灵活性与稳定性**，非常适合现代 Web 应用。

### API 与 Web 服务器的区别

传统网页与 Web API 在网络生态中都很重要，但在结构、通信方式与功能上有明显区别。理解这些差异对高效进行模糊测试至关重要。

| 特点     | Web 服务器（Web Server）                            | API（应用程序编程接口）                            |
| -------- | --------------------------------------------------- | -------------------------------------------------- |
| 用途     | 主要用于提供静态内容（HTML、CSS、图片）与动态网页   | 主要用于让不同软件之间相互通信、交换数据、触发操作 |
| 通信     | 与浏览器使用 HTTP 协议通信                          | 可使用 HTTP、HTTPS、SOAP 等多种协议                |
| 数据格式 | 以 HTML、CSS、JS 等网页格式为主                     | 可使用 JSON、XML 等多种数据格式                    |
| 用户交互 | 用户通过浏览器直接交互，查看页面与内容              | 用户一般不直接交互，由应用代表用户调用 API         |
| 访问权限 | 通常公网可访问                                      | 可公开、私有（仅内部）或合作方专用                 |
| 示例     | 访问网站时，服务器返回 HTML/CSS/JS 供浏览器渲染页面 | 手机天气 App 调用天气 API 获取数据，再展示给用户   |

理解这些区别后，你就可以针对 Web API 的特点调整模糊测试策略：
不再扫描隐藏目录或文件，而是专注于 **API 接口（endpoint）** 与**参数**，并重点关注请求与响应中的数据格式。

## 识别接口（Identifying Endpoints）

在开始对 Web API 进行模糊测试之前，你必须知道要从哪里入手。**识别 API 暴露的接口**是整个流程中至关重要的第一步。这需要一些排查技巧，但有多种方法可以帮你发现这些通往应用数据与功能的隐藏入口。

### REST

REST API 围绕**资源（resource）**概念构建，资源由被称为**接口（endpoint）**的唯一 URL 标识。这些接口是客户端请求的目标，通常还会包含参数，用于提供额外上下文或控制所请求的操作。

REST API 中的接口以 URL 结构呈现，代表你想要访问或操作的资源。例如：

- `/users` —— 代表用户资源集合
- `/users/123` —— 代表 ID 为 123 的特定用户
- `/products` —— 代表商品资源集合
- `/products/456` —— 代表 ID 为 456 的特定商品

这些接口的结构遵循**层级模式**，更具体的资源嵌套在更宽泛的分类之下。

**参数**用于修改 API 请求的行为或提供额外信息。在 REST API 中，有多种参数类型：

| 参数类型                              | 说明                                                       | 示例                                      |
| ------------------------------------- | ---------------------------------------------------------- | ----------------------------------------- |
| 查询参数（Query Parameters）          | 在接口 URL 中问号（?）后追加，用于过滤、排序或分页         | /users?limit=10&sort=name                 |
| 路径参数（Path Parameters）           | 直接嵌入接口 URL 内，用于标识特定资源                      | /products/{id}                            |
| 请求体参数（Request Body Parameters） | 在 POST、PUT、PATCH 请求的请求体中发送，用于创建或更新资源 | { "name": "New Product", "price": 99.99 } |

#### 发现 REST 接口与参数

可以通过以下几种方法发现 REST API 的可用接口与参数：

- **API 文档**：理解 API 最可靠的方式是查阅官方文档。文档通常会列出可用接口、参数、预期的请求/响应格式以及使用示例。留意 Swagger（OpenAPI）或 RAML 等规范，它们提供机器可读的 API 描述。
- **网络流量分析**：如果没有文档或文档不完整，可以分析网络流量来观察 API 的使用方式。Burp Suite 或浏览器开发者工具等工具可以拦截并查看 API 请求与响应，从而暴露出接口、参数和数据格式。
- **参数名模糊测试**：与目录、文件模糊测试类似，你可以使用相同的工具和技术对 API 请求中的参数名进行模糊测试。ffuf、wfuzz 等工具配合合适的字典，可以发现隐藏或未文档化的参数。这在处理缺乏完整文档的 API 时尤其有用。

### SOAP

SOAP（Simple Object Access Protocol，简单对象访问协议）API 的结构与 REST API 不同。它们依赖基于 XML 的消息和 **WSDL（Web Services Description Language，Web 服务描述语言）** 文件来定义接口与操作。

与 REST API 为每个资源使用不同 URL 不同，SOAP API 通常只暴露**一个接口**。这个接口是 SOAP 服务端监听请求的 URL，具体要执行的操作由 SOAP 消息内容本身决定。

SOAP 参数定义在 SOAP 消息体（XML 文档）中。这些参数被组织为元素和属性，形成层级结构。参数的具体结构取决于被调用的操作。所有参数都在 WSDL 文件中定义——WSDL 是一种基于 XML 的文档，用于描述 Web 服务的接口、操作和消息格式。

假设有一个图书馆的 SOAP API，提供图书搜索服务。WSDL 文件可能会定义一个名为 `SearchBooks` 的操作，包含以下输入参数：

- `keywords`（字符串）：搜索关键词
- `author`（字符串）：作者名（可选）
- `genre`（字符串）：图书类型（可选）

这个API的一个示例SOAP请求可能如下所示：

```xml
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:lib="http://example.com/library">
   <soapenv:Header/>
   <soapenv:Body>
      <lib:SearchBooks>
         <lib:keywords>cybersecurity</lib:keywords>
         <lib:author>Dan Kaminsky</lib:author>
      </lib:SearchBooks>
   </soapenv:Body>
</soapenv:Envelope>

```

在这个请求中：

- `keywords` 参数设为 `cybersecurity`，用于搜索相关主题的图书
- `author` 参数设为 `Dan Kaminsky`，用于进一步筛选
- `genre` 参数未包含，表示不按类型过滤

SOAP 响应通常会返回符合搜索条件的图书列表，格式遵循 WSDL 定义。

#### 发现 SOAP 接口与参数

可以使用以下方法识别 SOAP API 的可用接口（操作）与参数：

- **WSDL 分析**：WSDL 文件是理解 SOAP API 最有价值的资源。它描述了：
  - 可用操作（接口）
  - 每个操作的输入参数（消息类型、元素、属性）
  - 每个操作的输出参数（响应消息类型）
  - 参数使用的数据类型（字符串、整数、复杂类型等）
  - SOAP 接口的地址（URL）
    你可以手动分析 WSDL，或使用专门解析、可视化 WSDL 结构的工具。
- **网络流量分析**：与 REST API 类似，你可以拦截并分析 SOAP 流量，观察客户端与服务端之间的请求与响应。Wireshark、tcpdump 等工具可以捕获 SOAP 流量，让你分析消息结构并提取接口与参数信息。
- **参数名与参数值模糊测试**：虽然 SOAP API 通常结构明确，但模糊测试仍有助于发现隐藏或未文档化的操作或参数。你可以使用模糊测试工具发送构造异常或不符合预期的值，观察服务端如何响应。

### 识别 GraphQL API 接口与参数

GraphQL API 相比 REST 和 SOAP 更加灵活高效，允许客户端在**单次请求**中精确获取所需数据。

与 REST、SOAP 通常为不同资源开放多个接口不同，GraphQL API 一般**只有一个接口**，通常是类似 `/graphql` 的 URL，作为所有查询（query）和变更（mutation）的入口。

GraphQL 使用专用查询语言来描述数据需求。在这门语言中，**查询（query）**和**变更（mutation）**是定义参数、组织请求数据的载体。

#### GraphQL 查询（Queries）

查询用于从 GraphQL 服务端**获取数据**。它们精准指定客户端需要的字段、关联关系和嵌套对象，解决了 REST API 中常见的数据过度获取（over-fetching）或数据获取不足（under-fetching）问题。查询中的**参数（argument）**可用于进一步筛选、分页等。

| 组件                      | 说明                                     | 示例                  |
| ------------------------- | ---------------------------------------- | --------------------- |
| 字段（Field）             | 表示要获取的具体数据项                   | name, email           |
| 关联（Relationship）      | 表示不同数据类型之间的联系               | posts                 |
| 嵌套对象（Nested Object） | 返回另一个对象的字段，允许深入遍历数据图 | posts { title, body } |
| 参数（Argument）          | 修改查询或字段的行为（过滤、排序、分页） | posts(limit: 5)       |

```graphql
query {
  user(id: 123) {
    name
    email
    posts(limit: 5) {
      title
      body
    }
  }
}
```

在这个示例中：

- 我们查询 ID 为 123 的用户信息
- 请求返回姓名和邮箱
- 同时获取该用户的前 5 条帖子，包含标题和内容

#### GraphQL 变更（Mutations）

变更与查询相对，用于**修改服务端数据**，包括创建、更新、删除操作。与查询一样，变更也可以接收参数，作为操作的输入值。

| 组件                  | 说明                             | 示例                                  |
| --------------------- | -------------------------------- | ------------------------------------- |
| 操作（Operation）     | 要执行的动作                     | createPost, updateUser, deleteComment |
| 参数（Argument）      | 操作所需的输入数据               | title: "New Post", body: "..."        |
| 选取字段（Selection） | 变更完成后希望在响应中获取的字段 | id, title                             |

```graphql
mutation {
  createPost(title: "New Post", body: "This is the content of the new post") {
    id
    title
  }
}
```

该变更创建一篇新帖子，并在响应中返回新帖子的 ID 和标题。

#### 发现 GraphQL 查询与变更

有以下几种方法可以发现 GraphQL 的查询与变更：

- **内省（Introspection）**：GraphQL 的内省机制是发现能力的强大工具。向内省接口发送查询，可以获取完整的架构（schema），包括可用类型、字段、查询、变更和参数。工具与 IDE 可利用这些信息提供自动补全、验证和文档提示。
- **API 文档**：文档完善的 GraphQL API 会在内省机制之外提供完整指南与参考，通常会说明各类查询与变更的用途、有效结构示例、输入参数与响应格式。GraphiQL、GraphQL Playground 等工具常与 GraphQL 服务端集成，提供交互式环境来浏览架构、测试查询。
- **网络流量分析**：与 REST、SOAP 一样，分析网络流量可以了解 GraphQL API 的结构与使用方式。通过捕获并查看发往 `/graphql` 接口的请求与响应，你可以观察真实的查询与变更，理解预期的请求格式与返回数据类型，为针对性模糊测试提供帮助。

注意：GraphQL 设计追求灵活，因此不一定存在固定的查询与变更集合。你应重点理解底层架构，以及客户端如何构造合法请求来获取或修改数据。

## API Fuzzing

API 模糊测试是专门针对 Web API 设计的一种模糊测试形式。虽然模糊测试的核心原理保持不变——向目标发送意外或非法输入——但 API 模糊测试聚焦于 Web API 所特有的结构与协议。

API 模糊测试会向 API 发送一系列自动化测试请求，每条测试都会对 API 接口的请求做轻微修改，例如：

- 改变参数值
- 修改请求头
- 调整参数顺序
- 引入意外的数据类型或格式

其目标是触发 API 报错、崩溃或异常行为，从而暴露出潜在漏洞，如输入校验缺陷、注入攻击或身份认证问题。

### 为什么要对 API 进行模糊测试

API 模糊测试非常重要，原因如下：

- **发现隐藏漏洞**：API 通常存在隐藏或未文档化的接口与参数，这些都可能成为攻击点。模糊测试能帮助发现这些隐蔽的攻击面。
- **测试健壮性**：模糊测试可以评估 API 在处理意外或畸形输入时的表现，确保其不会崩溃或泄露敏感数据。
- **自动化安全测试**：手动测试所有可能的输入组合是不现实的。模糊测试可自动化完成，节省时间与精力。
- **模拟真实攻击**：模糊测试可以模仿攻击者行为，让你在漏洞被利用前提前发现。

### API 模糊测试的类型

主要有三种 API 模糊测试类型：

1. 参数模糊测试API 模糊测试中最核心的技术之一，专注于系统性测试 API 参数的各种取值。包括：查询参数（拼接在 URL 中）请求头（携带请求元数据）请求体（携带数据载荷）通过向这些参数注入意外或非法值，可以发现注入漏洞（SQL 注入、命令注入）、XSS、参数篡改等漏洞。
2. 数据格式模糊测试
   Web API 常使用 JSON、XML 等结构化格式交换数据。
   数据格式模糊测试专门针对这些格式，修改其结构、内容或编码，可暴露出解析错误、缓冲区溢出、特殊字符处理不当等问题。
3. 序列模糊测试
   API 通常包含多个互相关联的接口，请求的顺序与时机至关重要。
   序列模糊测试测试 API 对请求序列的响应，可暴露出**竞态条件、不安全直接对象引用（IDOR）、权限绕过**等逻辑漏洞。

### 浏览 API

该 API 通过 `/docs` 接口提供自动生成的文档：
`http://IP:PORT/docs`

![FastAPI interface showing endpoints: GET /, GET /items/{item_id}, DELETE /items/{item_id}, PUT /items/{item_id}, POST /items/.](https://cdn.services-k8s.prod.aws.htb.systems/content/modules/280/apispec.png)

文档中列出了 5 个公开接口：

- `GET /`：获取根资源，通常返回欢迎信息或 API 说明
- `GET /items/{item_id}`：根据 ID 获取指定条目
- `DELETE /items/{item_id}`：根据 ID 删除指定条目
- `PUT /items/{item_id}`：更新指定条目
- `POST /items/`：创建或更新条目

尽管 Swagger 文档明确列出了 5 个接口，但必须注意：**API 可能包含未公开、未文档化的隐藏接口**。

这些隐藏接口可能用于内部功能、出于错误的“隐蔽即安全”思想，或仍在开发中。

### 对 API 进行模糊测试

我们将使用一款模糊测试工具，配合字典来发现这些未公开接口。
执行以下命令拉取、安装并运行工具：

```shell
git clone https://github.com/PandaSt0rm/webfuzz_api.git
cd webfuzz_api
pip3 install -r requirements.txt
```

然后使用目标 IP 和端口运行模糊测试工具：

```shell
$ python3 api_fuzzer.py http://IP:PORT

[-] Invalid endpoint: http://localhost:8000/~webmaster (Status code: 404)
[-] Invalid endpoint: http://localhost:8000/~www (Status code: 404)

Fuzzing completed.
Total requests: 4730
Failed requests: 0
Retries: 0
Status code counts:
404: 4727
200: 2
405: 1
Found valid endpoints:
- http://localhost:8000/cz...
- http://localhost:8000/docs
Unusual status codes:
405: http://localhost:8000/items
```

模糊器识别出许多无效端点（返回 `404 Not Found`错误）。

发现两个有效接口：

- `/cz...`：未文档化的隐藏接口
- `/docs`：公开的 Swagger 文档接口

`/items` 返回 **405 Method Not Allowed**，说明使用了错误的 HTTP 方法访问该接口。

除了发现接口，模糊测试还可用于测试接口接收的参数。
通过系统性注入异常值，可触发错误、崩溃或异常行为，暴露出大量漏洞，例如：

- **对象级别权限损坏**：修改参数值即可越权访问其他对象/资源
- **功能级别权限损坏**：通过修改参数实现未授权功能调用
- **服务端请求伪造（SSRF）**：向参数注入恶意值，诱使服务器发起意外内网/外请求，泄露敏感信息或扩大攻击面
