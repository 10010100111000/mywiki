# OWASP Top 10 2021 网络安全十大漏洞

## 1.Broken Access Control（失效的访问控制）

【核心：权限管控、越权、业务逻辑缺陷】
│  ├─ Access control（访问控制）- 13个实验
│  ├─ Business logic vulnerabilities（业务逻辑漏洞）- 11个实验
│  ├─ Cross-site request forgery (CSRF)（跨站请求伪造）- 12个实验
│  ├─ API testing（API测试）- 5个实验
│  └─ GraphQL API vulnerabilities（GraphQL API漏洞）- 5个实验

## 2.Cryptographic Failures（加密失败）

│【核心：敏感数据未加密、加密机制缺陷】
│  └─ Information disclosure（信息泄露）- 5个实验（多因加密失败导致敏感数据泄露）
│

## 3.Injection（注入）

│【核心：恶意指令注入、数据篡改解析】
│  ├─ SQL injection（SQL注入）- 18个实验
│  ├─ Command injection（命令注入）- 5个实验
│  ├─ XXE injection（XXE注入）- 9个实验
│  ├─ NoSQL injection（NoSQL注入）- 4个实验
│  ├─ Cross-site scripting (XSS)（跨站脚本）- 30个实验（2021版OWASP将XSS归为注入类）
│  ├─ DOM-based vulnerabilities（DOM型漏洞）- 7个实验（核心为DOM XSS，归注入类）
│  └─ Server-side template injection（服务端模板注入）- 7个实验

## 4.Insecure Design（不安全的设计）

│【核心：设计层面的安全缺陷、业务逻辑规划不足】
│  ├─ Race conditions（竞争条件）- 6个实验
│  └─ Web LLM attacks（Web大语言模型攻击）- 4个实验（因LLM设计安全考虑不足引发）

## 5.Security Misconfiguration（安全配置错误）

【核心：服务器/组件/跨域等配置不当】
│  ├─ Path traversal（路径遍历）
│  ├─ Cross-origin resource sharing (CORS)（跨域资源共享）
│  ├─ Clickjacking（点击劫持）（因缺少反嵌配置导致）
│  ├─ Web cache deception（Web缓存欺骗
│  ├─ Web cache poisoning（Web缓存投毒）
│  ├─ HTTP Host header attacks（HTTP主机头攻击）
│  ├─ HTTP request smuggling（HTTP请求走私）（服务器/代理配置冲突导致）
│  └─ WebSockets（Web套接字）（核心为WS配置不当引发漏洞）
│

├─ 6. Vulnerable and Outdated Components（易受攻击和过时的组件）
│  └─ 无直接对应实验
│

├─ 7. Identification and Authentication Failures（身份验证和标识失败）【核心：认证机制缺陷、令牌漏洞】
│  ├─ Authentication（身份验证）- 14个实验
│  ├─ OAuth authentication（OAuth认证）- 6个实验
│  └─ JWT attacks（JWT令牌攻击）- 8个实验
│

├─ 8. Software and Data Integrity Failures（软件和数据完整性失败）【核心：数据/代码篡改、完整性破坏】
│  ├─ File upload vulnerabilities（文件上传漏洞）- 7个实验（恶意文件破坏软件完整性）
│  ├─ Insecure deserialization（不安全的反序列化）- 10个实验
│  └─ Prototype pollution（原型污染）- 10个实验（破坏前端数据完整性）
│

├─ 9. Security Logging and Monitoring Failures（安全日志和监控失败）
│  └─ 无直接对应实验
│


└─ 10. Server-Side Request Forgery（服务端请求伪造，SSRF）
└─ Server-side request forgery (SSRF)（服务端请求伪造）- 7个实验
