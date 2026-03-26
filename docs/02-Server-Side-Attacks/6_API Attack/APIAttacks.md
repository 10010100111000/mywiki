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

# 二. Broken Object Level Authorization

Web API 允许用户通过各类参数请求数据或记录，这些参数包括唯一标识符，例如通用唯一识别码（UUID，也称为全局唯一标识符 / GUID）和整型 ID。然而，如果未通过对象级别授权机制对用户进行严谨、安全的校验，确认其对某一资源拥有所有权与查看权限，就会导致数据泄露，产生安全漏洞。
当一个 Web API 接口在授权校验（代码层面实现）中，无法正确确保已认证用户具备请求、查看特定数据或执行相关操作的足够权限时，该接口就存在对象级别授权失效（BOLA） 漏洞，该漏洞也被称为不安全直接对象引用（IDOR）。

## 1. 通过用户可控密钥实现授权绕过

我们将要练习测试的接口存在 [CWE-639 漏洞：通过用户可控密钥实现授权绕过](https://cwe.mitre.org/data/definitions/639.html)。

场景说明
Inlanefreight 电商平台的管理员为我们提供了测试账号：htbpentester1@pentestercompany.com:HTBPentester1
希望我们测试该用户凭借分配的角色，能够利用哪些 API 漏洞。
由于该账号属于供应商角色，我们需要调用接口 /api/v1/authentication/suppliers/sign-in 完成登录，并获取 JWT 身份凭证。
