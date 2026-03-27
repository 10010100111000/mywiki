# 一. API攻击简介

应用程序编程接口（API）是现代软件开发的基础，其中 Web API 是最普遍的形式。它们能够实现不同系统之间通过互联网进行的无缝通信和数据交换，充当着促进不同软件应用程序之间集成与协作的关键桥梁。

API 的本质是定义规则和协议，用于规范不同系统之间的交互方式。它们规定了数据格式要求、资源访问方式以及预期响应结构。API 大致可分为公共 API（外部可访问）和私有 API（仅限特定组织或系统组使用）。

## 1. API 构建风格

Web API 可以使用各种架构风格构建，包括 REST 、 SOAP 、 GraphQL 和 gRPC ，每种风格都有其自身的优势和用例：

* [Representational State Transfer (REST)](https://roy.gbiv.com/pubs/dissertation/fielding_dissertation.pdf#:~:text=This%20chapter%20introduces%20and%20elaborates%20the%20Representational%20State%20Transfer)是目前最流行的 API 架构风格。它采用客户端 - 服务端模型，客户端通过标准 HTTP 方法（GET、POST、PUT、DELETE）向服务端的资源发起请求。RESTful API 是无状态的，即每个请求都包含服务端处理所需的全部信息，响应数据通常以 JSON 或 XML 格式进行序列化传输。
* [Simple Object Access Protocol (SOAP)](https://www.w3.org/TR/2000/NOTE-SOAP-20000508/)使用 XML 在系统间进行消息交换。SOAP API 高度标准化，并提供全面的安全、事务和错误处理功能，但与 RESTful API 相比，它们 SOAP 实现和使用通常更为复杂。
* [GraphQL](https://graphql.org/) 是一种替代型 API 架构，它提供了更灵活、高效的数据获取与更新方式。GraphQL 不会为每个资源返回固定字段，而是允许客户端精准指定所需数据，从而减少数据过度获取（over-fetching）与数据获取不足（under-fetching）的问题。GraphQL API 仅使用单一接口地址，并通过强类型查询语言来获取数据。
* [gRPC ](https://grpc.io/)是一种较新的 API 风格，它使用Protocol Buffers（protobuf） 进行消息序列化，提供高性能、高效率的系统间通信方式。gRPC API 可以用多种编程语言开发，在微服务和分布式系统中尤为实用。

本模块将重点讨论针对 [RESTful Web API 的攻击](https://cheatsheetseries.owasp.org/cheatsheets/REST_Security_Cheat_Sheet.html)。但是，文中展示的漏洞也可能存在于使用其他架构风格构建的 API 中。

## 2. API 攻击

由于 API 的通用性和普及性，它成为了一把双刃剑。尽管 API 是现代软件架构的核心组件，但同时也暴露了庞大的攻击面。API 的本质是实现不同系统间的数据交换与通信，而这一特性也带来了诸多漏洞，例如敏感数据泄露、身份认证与授权缺陷、速率限制不足、错误处理不当，以及其他各类安全配置问题。

## 3. OWASP 十大 API 安全风险

为了对 API 可能面临的安全漏洞和错误配置进行分类和标准化， OWASP 整理了 [OWASP API 安全十大风险 ](https://owasp.org/API-Security/editions/2023/en/0x11-t10/)，这是一份专门针对 API 的最关键安全风险的综合列表：

| 风险编号                                                                                                        | 风险名称                 | 说明                                                             |
| --------------------------------------------------------------------------------------------------------------- | ------------------------ | ---------------------------------------------------------------- |
| [API1:2023](https://owasp.org/API-Security/editions/2023/en/0xa1-broken-object-level-authorization/)               | 对象级别授权失效         | API 允许已认证用户访问其未被授权查看的数据。                     |
| [API2:2023](https://owasp.org/API-Security/editions/2023/en/0xa2-broken-authentication/)                           | 身份认证失效             | API 的身份认证机制可被绕过，导致未授权访问。                     |
| [API3:2023](https://owasp.org/API-Security/editions/2023/en/0xa3-broken-object-property-level-authorization/)      | 对象属性级别授权失效     | API 向已授权用户泄露其不应访问的敏感数据，或允许其修改敏感属性。 |
| [API4:2023](https://owasp.org/API-Security/editions/2023/en/0xa4-unrestricted-resource-consumption/)               | 无限制资源消耗           | API 未对用户可消耗的资源量进行限制。                             |
| [API5:2023](https://owasp.org/API-Security/editions/2023/en/0xa5-broken-function-level-authorization/)             | 功能级别授权失效         | API 允许未授权用户执行需要权限的操作。                           |
| [API6:2023](https://owasp.org/API-Security/editions/2023/en/0xa6-unrestricted-access-to-sensitive-business-flows/) | 敏感业务流程未受限制访问 | API 暴露敏感业务流程，可能造成经济损失及其他危害。               |
| [API7:2023](https://owasp.org/API-Security/editions/2023/en/0xa7-server-side-request-forgery/)                     | 服务端请求伪造（SSRF）   | API 未对请求做充分校验，攻击者可发送恶意请求并访问内部资源。     |
| [API8:2023](https://owasp.org/API-Security/editions/2023/en/0xa8-security-misconfiguration/)                       | 安全配置不当             | API 存在安全配置缺陷，包括可导致注入攻击的漏洞。                 |
| [API9:2023](https://owasp.org/API-Security/editions/2023/en/0xa9-improper-inventory-management/)                   | 接口版本清单管理不当     | API 未对接口版本进行合理、安全的管理。                           |
| [API10:2023](https://owasp.org/API-Security/editions/2023/en/0xaa-unsafe-consumption-of-apis/)                     | 不安全地调用第三方 API   | API 不安全地调用其他接口，引入潜在安全风险。                     |

本模块将专注于利用所有这些安全风险，并了解如何预防它们。

# 二. BOLA

Web API 允许用户通过各类参数请求数据或记录，这些参数包括唯一标识符，例如通用唯一识别码（UUID，也称为全局唯一标识符 / GUID）和整型 ID。然而，如果未通过对象级别授权机制对用户进行严谨、安全的校验，确认其对某一资源拥有所有权与查看权限，就会导致数据泄露，产生安全漏洞。
当一个 Web API 接口在授权校验（代码层面实现）中，无法正确确保已认证用户具备请求、查看特定数据或执行相关操作的足够权限时，该接口就存在对象级别授权失效(Broken Object Level Authorization)（BOLA） 漏洞，该漏洞也被称为不安全直接对象引用（IDOR）。

## 1. 通过用户可控资源标识符实现授权绕过

我们将要练习测试的接口存在 [CWE-639: Authorization Bypass Through User-Controlled Key](https://cwe.mitre.org/data/definitions/639.html)

场景说明
Inlanefreight 电商平台的管理员为我们提供了测试账号：htbpentester1@pentestercompany.com:HTBPentester1
希望我们测试该用户凭借分配的角色，能够利用哪些 API 漏洞。

### 1.1 测试


由于该账号属于供应商角色，我们需要调用接口 /api/v1/authentication/suppliers/sign-in 完成登录，并获取 JWT 身份凭证。
![1774554387602](images/APIAttacks/Authenitcation_Suppliers.gif)

要使用 JWT 进行身份验证，我们需要从响应结果中复制该令牌，然后点击 Authorize 按钮。注意那个锁形图标，目前处于未锁状态，表示我们还未完成认证。
接下来，在弹出的授权窗口中，将 JWT 粘贴到 Value 输入框内，然后点击 Authorize。完成后，锁形图标会变为完全锁定状态，即表示认证成功。
![jwt](images/APIAttacks/Authentication_Suppliers_2.gif)

在检查供应商组中的端点时（注意它们在最右侧有一个锁，表明需要身份验证），我们会注意到一个名为/api/v1/suppliers/current-user:![1774554871788](images/APIAttacks/1774554871788.png)

路径中包含 current-user 的接口，表明它们会使用当前已认证用户的 JWT 来执行指定操作，在本例中就是获取当前用户的数据。调用该接口后，我们会获取到当前用户所属的公司 ID：b75a7c76-e149-4ca7-9c55-d9fc4ffa87be，这是一个 GUID 格式的值,如下图所示:

![1774555002183](images/APIAttacks/1774555002183.png)

接下来我们获取当前用户的角色。调用 /api/v1/roles/current-user 接口后，响应返回的角色为：SupplierCompanies_GetYearlyReportByID:![1774555335580](images/APIAttacks/1774555335580.png)

在 Supplier-Companies 组中，我们找到了一个与角色 SupplierCompanies_GetYearlyReportByID 相关的端点，该端点接受一个 GET 参数： /api/v1/supplier-companies/yearly-reports/{ID} ：

![1774555367715](images/APIAttacks/1774555367715.png)

展开后，我们会注意到它需要 SupplierCompanies_GetYearlyReportByID 角色，并且接受 ID 参数为整数而不是 Guid ：

![1774555428273](images/APIAttacks/1774555428273.png)

### 1.2 发现漏洞

如果我们将 ID 设为 `1`，将会获取到属于另一家公司的年度报告，其公司 ID 为 `f9e58492-b594-4d82-a4de-16e4f230fce1`，
而并非我们自身所属的公司 `b75a7c76-e149-4ca7-9c55-d9fc4ffa87be`。![1774555482518](images/APIAttacks/1774555482518.png)

### 1.3 测试

尝试使用其他 ID 时，我们仍然可以访问其他供应商公司的年度报告，从而获取潜在的敏感业务数据：![1774555498772](images/APIAttacks/1774555498772.png)

此外，我们还可以大规模利用 BOLA 漏洞，获取供应商公司前 20 年的年度报告：

![BOLAMassAbuse](images/APIAttacks/BOLA_Mass_Abuse.gif)

我们只需要对从 Swagger 界面复制的 cURL 命令做几处修改：使用带变量插值的 Bash 循环，通过 -w "\n" 参数在每次响应后添加换行符，通过 -s 参数静默输出（隐藏请求进度），并将结果通过管道传递给 jq 工具解析。

```bash
$ for ((i=1; i<= 20; i++)); do
curl -s -w "\n" -X 'GET' \
  'http://94.237.49.212:43104/api/v1/supplier-companies/yearly-reports/'$i'' \
  -H 'accept: application/json' \
  -H 'Authorization: Bearer eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9uYW1laWRlbnRpZmllciI6Imh0YnBlbnRlc3RlcjFAcGVudGVzdGVyY29tcGFueS5jb20iLCJodHRwOi8vc2NoZW1hcy5taWNyb3NvZnQuY29tL3dzLzIwMDgvMDYvaWRlbnRpdHkvY2xhaW1zL3JvbGUiOiJTdXBwbGllckNvbXBhbmllc19HZXRZZWFybHlSZXBvcnRCeUlEIiwiZXhwIjoxNzIwMTg1NzAwLCJpc3MiOiJodHRwOi8vYXBpLmlubGFuZWZyZWlnaHQuaHRiIiwiYXVkIjoiaHR0cDovL2FwaS5pbmxhbmVmcmVpZ2h0Lmh0YiJ9.D6E5gJ-HzeLZLSXeIC4v5iynZetx7f-bpWu8iE_pUODlpoWdYKniY9agU2qRYyf6tAGdTcyqLFKt1tOhpOsWlw' | jq
done

{
  "supplierCompanyYearlyReport": {
    "id": 1,
    "companyID": "f9e58492-b594-4d82-a4de-16e4f230fce1",
    "year": 2020,
    "revenue": 794425112,
    "commentsFromCLevel": "Superb work! The Board is over the moon! All employees will enjoy a dream vacation!"
  }
}

<SNIP>
```

### 1.4 预防

为缓解 BOLA 漏洞，端点 /api/v1/supplier-companies/yearly-reports 应在源代码级别实施验证步骤，以确保授权用户只能访问与其关联公司相关的年度报告。此验证步骤包括将报告中的 companyID 字段与已验证供应商的 companyID 进行比较。仅当这些值匹配时才应授予访问权限；否则，应拒绝请求。这种方法有效地维护了供应商公司年度报告之间的数据隔离。


# 三.Broken Authentication 

[身份认证](https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html)是 Web API 安全的核心基石。Web API 会采用多种认证机制来保障数据的机密性。如果某个 API 的任意一项认证机制能够被绕过或规避，该 API 就存在 **认证机制失效（Broken Authentication）** 漏洞。

## 1.未正确限制过多登录尝试

我们将要练习的端点存在的漏洞为 [CWE-307: Improper Restriction of Excessive Authentication Attempts.](https://cwe.mitre.org/data/definitions/307.html)

web程序的管理向我们提供了一个客户账号,希望我们评估用户可以利用其分配的角色来利用哪些 API 漏洞。我们将使用 /api/v1/authentication/customers/sign-in 端点获取 JWT，然后使用该 JWT 进行身份验证：![1774560337790](images/APIAttacks/1774560337790.png)

调用 /api/v1/customers/current-user 端点时，我们会得到当前已认证用户的信息：![1774560371928](images/APIAttacks/1774560371928.png)

/api/v1/roles/current-user 端点显示，该用户被分配了三个角色： Customers_UpdateByCurrentUser 、 Customers_Get 和 Customers_GetAll ：

![1774560402166](images/APIAttacks/1774560402166.png)

### 1.1 测试

Customers_GetAll 允许我们使用 /api/v1/customers 端点，该端点返回所有客户的记录：![1774560432235](images/APIAttacks/1774560432235.png)

尽管该接口存在  **对象属性级授权失效（Broken Object Property Level Authorization, BOPLA）漏洞**（我们将在后续章节详细讲解）—— 因其泄露了其他客户的敏感信息，如电子邮箱、手机号码和出生日期,

但该漏洞并不能让我们直接劫持任意其他账户。

### 1.2 发现

当我们展开 /api/v1/customers/current-user PATCH 端点时，我们发现它允许我们更新信息字段，包括帐户密码：

![1774560569542](images/APIAttacks/1774560569542.png)

如果我们提供像“pass”这样的弱密码，API 会拒绝更新，并指出密码长度必须至少为六个字符：![1774560582300](images/APIAttacks/1774560582300.png)

验证消息提供了有价值的信息，表明该 API 使用了较弱的密码策略，并未强制使用加密安全的密码。如果我们尝试将密码设置为“123456”，我们会注意到 API 现在返回成功状态 true ，表明它已执行更新：![1774560608292](images/APIAttacks/1774560608292.png)

### 1.3 利用

鉴于该 API 使用了较弱的密码策略，其他客户账户在注册时可能使用了加密安全性较低的密码。因此，我们将使用 ffuf 对客户账户进行密码暴力破解。

首先，我们需要获取当提供错误凭据时 /api/v1/authentication/customers/sign-in 端点返回的（失败）消息，在本例中为“无效凭据”：![1774560739439](images/APIAttacks/1774560739439.png)

web管理员给了我们3个可用于攻击的测试账户邮箱地址,.

* OlawaleJones@yandex.com
* IsabellaRichardson@gmail.com
* WenSalazar@zoho.com

对于密码单词列表，我们将使用 [SecLists](https://github.com/danielmiessler/SecLists/tree/master) 中的[ xato-net-10-million-passwords-10000](https://github.com/danielmiessler/SecLists/blob/master/Passwords/Common-Credentials/xato-net-10-million-passwords-10000.txt) 。

由于我们需要同时对两个参数进行模糊测试（分别是邮箱和密码），因此需要使用 ffuf 的 -w 参数，并分别将关键字 EMAIL 和 PASS 绑定到邮箱字典与密码字典上。待 ffuf 扫描完成后，我们即可发现账号 IsabellaRichardson@gmail.com 的密码为 qwerasdfzxcv。

```bash
$ ffuf -w /opt/useful/seclists/Passwords/xato-net-10-million-passwords-10000.txt:PASS -w customerEmails.txt:EMAIL -u http://94.237.59.63:31874/api/v1/authentication/customers/sign-in -X POST -H "Content-Type: application/json" -d '{"Email": "EMAIL", "Password": "PASS"}' -fr "Invalid Credentials" -t 100

        /'___\  /'___\           /'___\   
       /\ \__/ /\ \__/  __  __  /\ \__/   
       \ \ ,__\\ \ ,__\/\ \/\ \ \ \ ,__\  
        \ \ \_/ \ \ \_/\ \ \_\ \ \ \ \_/  
         \ \_\   \ \_\  \ \____/  \ \_\   
          \/_/    \/_/   \/___/    \/_/   

       v2.1.0-dev
________________________________________________

 :: Method           : POST
 :: URL              : http://94.237.59.63:31874/api/v1/authentication/customers/sign-in
 :: Wordlist         : PASS: /opt/useful/seclists/Passwords/xato-net-10-million-passwords-10000.txt
 :: Wordlist         : EMAIL: /home/htb-ac-413848/customerEmails.txt
 :: Header           : Content-Type: application/json
 :: Data             : {"Email": "EMAIL", "Password": "PASS"}
 :: Follow redirects : false
 :: Calibration      : false
 :: Timeout          : 10
 :: Threads          : 100
 :: Matcher          : Response status: 200-299,301,302,307,401,403,405,500
 :: Filter           : Regexp: Invalid Credentials
________________________________________________

[Status: 200, Size: 393, Words: 1, Lines: 1, Duration: 81ms]
    * EMAIL: IsabellaRichardson@gmail.com
    * PASS: qwerasdfzxcv

:: Progress: [30000/30000] :: Job [1/1] :: 1275 req/sec :: Duration: [0:00:24] :: Errors: 0 ::
```

现在我们已经暴力破解了密码，就可以使用他的登陆凭证查看她的所有机密信息。

> 应用程序允许用户通过请求发送到其设备的 One Time Password ( OTP ) 或回答注册时选择的安全问题来重置密码。如果由于强密码策略而无法暴力破解密码，我们可以尝试暴力破解 OTP 或安全问题的答案，前提是它们的熵较低或容易被猜到（此外，还需假设未实施速率限制）。

### 1.4 预防

为了缓解 Broken Authentication 漏洞， /api/v1/authentication/customers/sign-in 端点应实施速率限制以防止暴力破解攻击。这可以通过限制单个 IP 地址或用户帐户在指定时间范围内的登录尝试次数来实现。

此外，Web API 应在注册和更新用户凭据（包括客户和供应商）时强制执行严格的密码策略，仅允许使用加密安全的密码。该策略应包括：

1. Minimum password length （例如，至少 12 个字符）
2. Complexity requirements （例如，混合使用大小写字母、数字和特殊字符）
3. 禁止使用常用或容易猜到的密码（例如在泄露的密码数据库中发现的那些）
4. 执行密码历史记录以防止重复使用最近密码
5. 定期密码过期和强制更改

此外，为了增强安全性，Web API 端点应实现多因素身份验证 ( MFA )，在完全验证用户身份之前请求 OTP 。


# 四. BOPLA

对象属性级授权失效（Broken Object Property Level Authorization, BOPLA） 是一类漏洞统称，包含两个子类别：

* 过度数据暴露(Excessive Data Exposure)
* 批量赋值(Mass achusetts)

若一个 API 接口向授权用户泄露了其无权访问的敏感数据，则该接口存在过度数据暴露(Excessive Data Exposure)漏洞。

若一个 API 接口允许授权用户越权操作敏感对象属性（包括修改、新增或删除属性值），超出其权限范围，则该接口存在批量赋值(Mass achusetts)漏洞。

## 1. 因策略不兼容导致的敏感信息泄露

我们要练习的第一个接口存在 [CWE-213 ](https://cwe.mitre.org/data/definitions/213.html)因策略不兼容导致的敏感信息泄露 漏洞。

### 1.1 场景描述

电商平台的管理员为我们提供了登录凭证,希望我们测试该用户凭借分配的角色，能够利用哪些 API 漏洞。我们调用 /api/v1/authentication/customers/sign-in 接口以客户身份登录并获取 JWT 身份令牌后，通过 /api/v1/roles/current-user 接口查询到我们拥有的角色为：Suppliers_Get 和 Suppliers_GetAll:![1774563304908](images/APIAttacks/1774563304908.png)

对于电子商务平台来说，允许客户查看供应商详细信息是常见的。然而，在调用/api/v1/suppliers GET端点后，我们注意到响应不仅包括id、companyID和name字段，还包括供应商的email和phoneNumber字段：![1774563352470](images/APIAttacks/1774563352470.png)

这些敏感字段不应暴露给客户，因为这会使他们能够完全绕过平台，直接联系供应商购买商品（并享受折扣价）。此外，这种漏洞还会使供应商获利，使他们无需支付平台费用即可获得更高的收入。

### 1.2 预防

为了缓解 Excessive Data Exposure 漏洞， /api/v1/suppliers 端点应该只返回客户视角所需的字段。实现方式可以是：返回一个专用的[响应数据传输对象（DTO）](https://en.wikipedia.org/wiki/Data_transfer_object)，只包含面向客户展示的字段，而非直接暴露用于数据库交互的完整领域模型。

## 2.对动态确定的对象属性的修改控制不当

我们将要练习的第二个 API 接口存在[ CWE-915 ](https://cwe.mitre.org/data/definitions/915.html)对动态确定的对象属性的修改控制不当 漏洞。

### 2.1场景描述

我们拥有的角色为：SupplierCompanies_Update 和 SupplierCompanies_Get:![1774563677119](images/APIAttacks/1774563677119.png)

/api/v1/supplier-companies/current-user 接口显示，当前已认证的供应商所属的供应商公司 PentesterCompany，其 isExemptedFromMarketplaceFee(是否免除平台服务费) 字段值为 0，即代表 false（否）:![1774563727669](images/APIAttacks/1774563727669.png)

因此，这意味着电商平台会对 PentesterCompany 公司售出的每件商品收取平台佣金。
当我们查看 /api/v1/supplier-companies 的 PATCH 接口时发现：该接口要求具备 SupplierCompanies_Update 角色权限，文档注明执行更新操作的供应商必须为内部员工，同时接口允许传入 isExemptedFromMarketplaceFee 字段的值。

![1774563820447](images/APIAttacks/1774563820447.png)

我们将该字段设为 1，这样 PentesterCompany 就不会被归入需要缴纳平台佣金的公司范围。调用该接口后，接口返回了操作成功的提示信息:

![1774563860474](images/APIAttacks/1774563860474.png)

随后，当我们再次通过 `/api/v1/supplier-companies/current-user` 查看公司信息时，会发现 `isExemptedFromMarketplaceFee` 字段已变为 **1:**![1774563892480](images/APIAttacks/1774563892480.png)

由于该接口错误地允许供应商修改本无权操作的字段值，此漏洞可让供应商公司在 Inlanefreight 电商平台的所有销售中获得更高收入，因为它们将无需缴纳平台佣金。不过，与上一个「因策略不兼容导致敏感信息泄露」漏洞的影响类似，该漏洞会对 Inlanefreight 电商平台相关利益方的收益造成负面影响。

### 2.2 预防

要缓解此类批量赋值漏洞，/api/v1/supplier-companies PATCH 接口应限制调用者修改敏感字段。与防范过度数据暴露的思路类似，可通过 实现**专用的请求 DTO（数据传输对象）** 来实现，只包含允许供应商修改的字段，从而杜绝越权更新敏感属性。


# 五.无限制资源消耗

文件上传与下载是所有应用的基础功能。例如在电商平台中，供应商需要能够上传商品图片，而用户则需要查看和下载这些文件。
如果一个 Web API 未能对用户发起的、会消耗网络带宽、CPU、内存与存储空间等资源的请求进行限制，那么该 API 就存在无限制资源消耗漏洞。这些资源会产生高昂成本，若缺乏完善的防护措施（尤其是有效的速率限制）来防止过度使用，攻击者就可以利用此类漏洞造成经济损失。

## 1.资源消耗未受控制

我们将要练习的接口存在[ CWE-400: Uncontrolled Resource Consumption](https://cwe.mitre.org/data/definitions/400.html).CWE-400：资源消耗未受控制 漏洞。

### 1.1 场景描述

我们拥有的角色为：SupplierCompanies_Get 和 SupplierCompanies_UploadCertificateOfIncorporation。

查看「供应商公司（Supplier-Companies）」分组后，我们发现与第二个角色相关的接口只有一个：/api/v1/supplier-companies/certificates-of-incorporation POST 接口。展开该接口详情后可以看到，它要求调用者具备 SupplierCompanies_UploadCertificateOfIncorporation 角色权限，并允许供应商公司员工以 PDF 格式上传其公司注册证书，且文件会被永久存储在服务器磁盘上。

![1774569300574](images/APIAttacks/1774569300574.png)

让我们尝试上传一个包含随机字节的大型 PDF 文件。首先，我们将使用 /api/v1/supplier-companies/current-user 获取当前已认证用户 b75a7c76-e149-4ca7-9c55-d9fc4ffa87be 的供应商公司 ID：![1774569332531](images/APIAttacks/1774569332531.png)

接下来，我们将使用 dd 命令创建一个包含 30 兆字节随机大小的文件，并将其扩展名设置为 .pdf ：

```bash
$ dd if=/dev/urandom of=certificateOfIncorporation.pdf bs=1M count=30

30+0 records in
30+0 records out
31457280 bytes (31 MB, 30 MiB) copied, 0.139503 s, 225 MB/s
```

然后，在 /api/v1/supplier-companies/certificates-of-incorporation POST 端点中，我们将点击“选择文件”按钮并上传文件

调用该端点后，我们注意到 API 返回了上传成功的消息，以及上传文件的大小：![1774569406674](images/APIAttacks/1774569406674.png)

由于该端点未验证文件大小是否在指定范围内，后端会将任意大小的文件保存到磁盘。此外，如果该端点未实施速率限制，我们可以通过重复发送文件上传请求来尝试造成拒绝服务攻击，从而耗尽所有可用磁盘空间。

> 这个模块太垃圾了,我都不想学了,什么垃圾玩意儿



### 1.2 预防

为缓解无限制资源消耗漏洞，/api/v1/supplier-companies/certificates-of-incorporation POST 接口应针对上传文件的大小、后缀名和内容实施全面的验证机制。验证文件大小可防止服务器资源（如磁盘空间、内存）被过度消耗；同时，仅允许上传授权的指定文件类型，能有效规避潜在的安全风险。
实施文件大小校验可确保上传文件不超过规定上限，从而避免服务器资源被滥用。此外，验证文件后缀名能确保接口仅接收授权类型（如 PDF 或指定图片格式），阻止可执行文件（exe、bat、sh）等恶意文件或其他潜在有害文件上传，避免其危害服务器安全。结合服务端校验实施严格的文件后缀验证，有助于强化安全策略，防止文件被未授权访问和执行。
集成 ClamAV 等杀毒扫描工具，可在文件保存至磁盘前对其内容进行恶意软件特征码扫描，为系统增加一层安全防护。这种主动防御措施能够检测并阻止感染病毒的文件上传，避免服务器完整性遭到破坏。
此外，实施严格的身份认证与授权机制，可确保只有通过验证、具备对应权限的用户才能执行文件上传操作，并访问 wwwroot 等公开可访问目录下的资源。

# 六.BFLA

如果一个 Web API 允许未授权或低权限用户访问并调用高权限接口，从而获取敏感操作权限或机密信息，该 API 就存在 ** 函数级授权失效（Broken Function Level Authorization, BFLA）** 漏洞。
BOLA 与 BFLA 的区别在于：
BOLA 场景下，用户有权访问该接口本身，只是越权访问了他人的资源；
BFLA 场景下，用户根本无权访问该接口，却能直接调用。

## 1.敏感信息泄露给未经授权的人员

我们将要练习的端点存在的漏洞为[CWE-200: Exposure of Sensitive Information to an Unauthorized Actor.](https://cwe.mitre.org/data/definitions/200.html)

### 1.1 场景描述

在调用 /api/v1/authentication/customer/sign-in 以客户身份登录并获取 JWT 后，我们需要寻找那些需要授权但允许未经授权的用户与之交互的端点。产品组下有一个有趣的端点 /api/v1/products/discounts ，它似乎可以检索所有产品折扣，但是，它要求已认证的用户拥有 ProductDiscounts_GetAll 角色：![1774570906860](images/APIAttacks/1774570906860.png)

使用 /api/v1/roles/current-user 端点检查角色后，我们会发现当前已认证用户没有任何已分配的角色：![1774570921548](images/APIAttacks/1774570921548.png)

尽管没有任何角色，但如果我们尝试调用 /api/v1/products/discounts 端点，我们会发现它返回的数据包含了所有产品的折扣信息：![1774570938314](images/APIAttacks/1774570938314.png)

尽管 Web API 开发人员的本意是只有具有 ProductDiscounts_GetAll 角色的授权用户才能访问此端点，但他们并没有实现基于角色的访问控制检查。

### 1.2 预防

为了缓解 BFLA 漏洞， /api/v1/products/discounts 端点应在源代码级别强制执行授权检查，以确保只有具有 ProductDiscounts_GetAll 角色的用户才能与其交互。这包括在处理请求之前验证用户的角色，从而确保未经授权的用户无法访问端点的功能。

# 七 . 无限制访问敏感业务流程

所有商业运营的目的都是创造收益；但如果某一 Web API 暴露的操作或数据可被用户滥用并破坏业务体系（例如以折扣价恶意大量购买商品），该 API 就存在无限制访问敏感业务流程漏洞。若某 API 接口对外暴露了敏感业务流程，却未对其访问权限做恰当限制，即存在此类漏洞。

## 场景

在上一节中，我们利用了 BFLA 漏洞获取到了商品折扣数据。这种数据暴露同样会导致无限制访问敏感业务流程漏洞，因为它能让我们得知各供应商公司的商品打折时间以及对应的折扣力度。
例如，若我们想购买 ID 为 a923b706-0aaa-49b2-ad8d-21c97ff6fac7 的商品，就应在 2023-03-15 至 2023-09-15 期间下单，因为这段时间该商品可享受 ** 原价 3 折（70% OFF）** 的优惠。

![1774571518130](images/APIAttacks/1774571518130.png)

## 预防

为了缓解 Unrestricted Access to Sensitive Business Flows 漏洞，暴露关键业务操作的端点（例如 /api/v1/products/discounts ）应实施严格的访问控制，以确保只有授权用户才能查看或与敏感数据交互。

# 八. 服务器端请求伪造

如果 Web API 使用用户控制的输入来获取远程或本地资源而未进行验证，则该 API 容易受到 Server-Side Request Forgery ( SSRF ) 攻击（也称为 Cross-Site Port Attack ( XPSA )）。当 API 在未验证用户提供的 URL 的情况下获取远程资源时，就会出现 SSRF 漏洞。这使得攻击者可以诱使应用程序向意外的目标（尤其是本地目标）发送精心构造的请求，从而绕过防火墙或 VPN。

## 1. 服务器端请求伪造 (SSRF)

我们将要练习的端点存在的漏洞为:[CWE-918: Server-Side Request Forgery (SSRF).](https://cwe.mitre.org/data/definitions/918.html)

### 1.1 场景描述

在调用 /api/v1/authentication/suppliers/sign-in 以供应商身份登录并获取 JWT 后， /api/v1/roles/current-user 端点显示我们拥有 SupplierCompanies_Update 和 SupplierCompanies_UploadCertificateOfIncorporation 角色

检查供应商-公司组，我们注意到与这些角色相关的三个端点是 /api/v1/supplier-companies 、 /api/v1/supplier-companies/{ID}/certificates-of-incorporation 和 /api/v1/supplier-companies/certificates-of-incorporation 

/api/v1/supplier-companies/current-user 表示当前已认证用户属于 ID 为 b75a7c76-e149-4ca7-9c55-d9fc4ffa87be 的供应商公司：

展开 /api/v1/supplier-companies/certificates-of-incorporation POST 端点，我们注意到它需要 SupplierCompanies_UploadCertificateOfIncorporation 角色，并允许供应商公司的员工上传其公司注册证书的 PDF 文件。我们将为第一个字段提供任意 PDF 文件，并在其后填写我们供应商公司的 ID：![1774572008990](images/APIAttacks/1774572008990.png)

调用该端点后，我们会注意到响应包含三个字段，其中最有趣的是 fileURI 的值：![1774572032769](images/APIAttacks/1774572032769.png)\

Web API 使用[文件 URI 方案](https://datatracker.ietf.org/doc/html/rfc8089)存储文件路径，该方案用于表示本地文件路径，并允许访问本地文件系统上的文件。如果我们再次使用 /api/v1/supplier-companies/current-user 端点，我们会注意到 certificateOfIncorporationPDFFileURI 的值现在包含已上传文件的文件 URI：

![1774572097403](images/APIAttacks/1774572097403.png)

展开 /api/v1/supplier-companies PATCH 端点，我们注意到它需要 SupplierCompanies_Update 角色，更新必须由属于供应商公司的员工执行，并且允许修改 CertificateOfIncorporationPDFFileURI 字段的值：![1774572114793](images/APIAttacks/1774572114793.png)

因此，该接口存在对动态确定的对象属性的修改控制不当（CWE-915，即批量赋值漏洞），因为该字段的值本应仅由 /api/v1/supplier-companies/certificates-of-incorporation 这个 POST 接口来设置。接下来我们将发起一次 SSRF 攻击，并将 CertificateOfIncorporationPDFFileURI 字段的值修改为指向 /etc/passwd 文件。

![1774572158274](images/APIAttacks/1774572158274.png)

由于 Web API 的后端不会验证 CertificateOfIncorporationPDFFileURI 字段指向的路径，因此它会获取并返回本地文件的内容，包括敏感文件，例如 /etc/passwd 。

让我们调用 /api/v1/supplier-companies/{ID}/certificates-of-incorporation GET 端点来检索 CertificateOfIncorporationPDFFileURI 指向的文件 /etc/passwd 的内容，并以 base64 编码：![1774572195607](images/APIAttacks/1774572195607.png)

### 1.2 预防

为缓解 SSRF 漏洞，/api/v1/supplier-companies/certificates-of-incorporation（POST）和 /api/v1/supplier-companies（PATCH）接口必须严格禁止指向服务器本地非预期资源的文件 URI。通过校验机制确保文件 URI 仅指向允许的本地资源至关重要，在本场景中即限定在 wwwroot/SupplierCompaniesCertificatesOfIncorporation/ 目录以内。
此外，/api/v1/supplier-companies/{ID}/certificates-of-incorporation（GET）接口必须配置为仅返回指定目录 wwwroot/SupplierCompaniesCertificatesOfIncorporation 下的内容。这可以确保仅能访问公司注册证书，不会暴露该目录之外的本地资源或文件。同时，即便 POST 和 PATCH 接口的校验逻辑失效，该配置也能起到兜底防护作用。


# 九. 安全配置错误

Web API 与传统 Web 应用程序一样，都容易受到安全配置错误的影响。一个典型的例子是，Web API 端点接受用户控制的输入，并在未进行适当验证的情况下将其合并到 SQL 查询中，从而导致注入攻击。

## 1. 对 SQL 命令中特殊元素的净化处理不当（SQL 注入）

我们将要练习的端点存在[CWE-89: Improper Neutralization of Special Elements used in an SQL Command ('SQL Injection').  ](https://cwe.mitre.org/data/definitions/89.html)

### 1.1 场景描述

从 /api/v1/authentication/suppliers/sign-in 端点获取 JWT 作为供应商并使用该 JWT 进行身份验证后，我们观察到 /api/v1/roles/current-user 端点显示我们拥有 Products_GetProductsTotalCountByNameSubstring 角色：![1774573338828](images/APIAttacks/1774573338828.png)

与该角色名称相关的唯一端点是 /api/v1/products/{Name}/count ，它属于产品组。探索此端点后，我们发现它返回名称中包含用户提供的子字符串的产品总数：

![1774573363396](images/APIAttacks/1774573363396.png)

例如，如果我们使用 laptop 作为 Name 字符串参数，我们会发现总共有 18 个匹配的产品

但是，如果我们尝试使用 laptop' （末尾带撇号）作为输入，我们会发现端点返回错误消息，这表明存在 SQL 注入攻击的潜在漏洞：![1774573396236](images/APIAttacks/1774573396236.png)

让我们尝试使用有效载荷 laptop' OR 1=1 -- 来检索 Products 表中所有记录的数量；我们将发现该表中有 720 个产品：![1774573417413](images/APIAttacks/1774573417413.png)

## 2. http Headers

如果 API 没有使用正确的 HTTP 安全响应标头， 也可能出现安全配置错误。例如，假设某个 API 没有在其 CORS （ Cross-Origin Resource Sharing ）策略中设置安全的[ Access-Control-Allow-Origin ](https://cheatsheetseries.owasp.org/cheatsheets/HTTP_Headers_Cheat_Sheet.html#access-control-allow-origin)标头，那么它就可能面临安全风险，最显著的是[跨站请求伪造 ( CSRF ) 攻击](https://cwe.mitre.org/data/definitions/352.html)。

## 3.预防

为了缓解 Security Misconfiguration 错误漏洞， /api/v1/products/{Name}/count 端点应使用参数化查询或[对象关系映射器 ( ORM ) ](https://en.wikipedia.org/wiki/Object%E2%80%93relational_mapping)来安全地将用户控制的值插入 SQL 查询。如果无法使用参数化查询或 ORM，则必须在将用户控制的输入连接到 SQL 查询之前对其进行验证，但这并非万无一失。

此外，如果 Web API 不安全地使用 HTTP 标头或遗漏了安全相关的标头，则应实现安全标头以防止各种安全漏洞的发生。[OWASP Secure Headers ](https://github.com/OWASP/www-project-secure-headers)等项目提供了有关 HTTP 安全标头以及如何避免因标头配置不当而导致的安全漏洞的指导。



# 十. 不当清单管理

维护准确、最新的接口文档对 Web API 至关重要，尤其是因为 API 需要面向第三方使用者，而他们必须清楚如何正确地与 API 进行交互。
但随着 Web API 不断迭代更新，做好规范的版本管理就显得尤为关键，这能避免引入安全隐患。对 API 实施不当的清单管理（包括版本管控缺失 / 不规范），会导致安全配置缺陷，并扩大系统攻击面。这类问题的表现形式多种多样，比如过时或不再兼容的旧版 API 仍对外开放访问，为未授权用户留下潜在的入侵入口。

## 1.场景

在前面的章节中，我们主要使用的是 Inlanefreight 电商平台 Web API 的 v1 版本。但在查看 Swagger UI 里「选择接口定义」的下拉菜单时，我们发现还存在另一个版本：v0。

![1774574540942](images/APIAttacks/1774574540942.png)

查看 v0 的描述可知，该版本包含旧数据和已删除数据，属于未维护的备份，应予以删除。然而，检查端点后，我们会发现它们均未显示“锁定”图标，表明它们不需要任何形式的身份验证：![1774574568571](images/APIAttacks/1774574568571.png)

在调用 /api/v0/customers/deleted 端点时，API 会响应并公开已删除的客户数据，包括敏感的密码哈希值：![1774574603293](images/APIAttacks/1774574603293.png)

由于开发人员疏忽，未移除 v0 端点，我们未经授权访问了已删除的前客户数据。/api/v0/customers/deleted /api/v0/customers/deleted 中存在 Excessive Data Exposure 漏洞，导致我们能够查看客户密码哈希值，从而加剧了这一问题。利用这些泄露的信息，我们可以尝试破解密码。鉴于密码重复使用的普遍做法，这可能会危及活跃账户的安全，尤其是当同一客户使用相同密码重新注册时。

## 2.预防

有效的版本控制确保只有预期的 API 版本才能提供给用户，旧版本会被正确地弃用或终止。通过对 API 进行全面管理,可以最大限度地降低漏洞暴露的风险，并维护安全的用户界面。开发人员应完全移除 v0 ，或者至少将其访问权限限制在本地开发和测试范围内，确保外部用户无法访问。如果以上两种方案均不可行，则应使用严格的身份验证措施保护端点，仅允许管理员进行交互。

# 十一. 不安全地使用 API

API 之间频繁交互以交换数据，形成一个复杂的互联服务生态系统。这种互联性虽然增强了功能和效率，但如果管理不当，也会带来严重的安全风险。开发人员可能会盲目信任从第三方 API 接收的数据，尤其是在数据来自信誉良好的机构时，从而导致安全措施放松，特别是输入验证和数据清理方面。

API 之间的通信可能会出现多种严重漏洞：

* 不安全的数据传输：
  通过未加密通道通信的 API 会将敏感数据暴露在被窃听的风险下，破坏数据的保密性与完整性。

* 数据校验不足：
  在处理或转发来自外部 API 的数据给下游组件前，未对其进行 proper 校验与净化处理，可能导致注入攻击、数据损坏，甚至远程代码执行。
* 弱身份认证：
  与其他 API 通信时未采用健壮的身份认证机制，可能导致敏感数据或核心功能被未授权访问。
* 限流措施不足：
  某一 API 可能通过持续发送大量请求压垮另一 API，进而引发拒绝服务（DoS）。
* 监控不足：
  对 API 间交互行为的监控不到位，会导致安全事件难以被及时发现与响应。

若一个 API 以不安全的方式调用另一 API，则存在[CWE-1357: Reliance on Insufficiently Trustworthy Component.CWE-1357](https://cwe.mitre.org/data/definitions/1357.html)(依赖于不可信的组件漏洞)。

## 预防

为防止 API 间通信出现漏洞，Web API 开发人员应采取以下措施：

安全的数据传输：

使用加密通道进行数据传输，避免敏感数据在中间人攻击中泄露。

充分的数据校验：

在处理或转发外部API数据至下游组件前，确保对数据进行完善的校验与净化，降低注入攻击、数据损坏或远程代码执行的风险。

健壮的身份认证：

与其他API通信时采用安全的认证方式，防止敏感数据与核心功能被未授权访问。

有效的限流机制：

实施速率限制，避免某一API被大量请求压垮，抵御拒绝服务攻击。

全面的监控：

对API间通信进行严密监控，以便及时发现并响应安全事件。
