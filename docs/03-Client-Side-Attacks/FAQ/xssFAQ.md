# window.origin

window.origin 是 JavaScript 中只读的全局属性，用于返回当前页面的源（Origin）—— 这个 “源” 是浏览器安全模型的核心概念，由 协议 + 主机名 + 端口号 三部分组成（端口号在默认值时可省略，比如 HTTP 的 80、HTTPS 的 443）

# IFrame

IFrame (Inline Frame，内联框架) 就像是网页里的**“画中画”**。它允许你在当前网页的内部，开辟一个小窗口，去加载并显示另一个完全独立的网页。

假设你的主网页是 A.html，你想在里面开个小窗显示 B.html。代码其实非常简单：

```html
<body>
    <h1>这是我的主网页</h1>
    <p>下面是一个嵌入的页面：</p>
  
    <iframe src="https://www.example.com/B.html" width="800" height="600"></iframe>
</body>
```

IFrame 在安全领域最重要的特性是：DOM 树是完全独立的。

虽然你在视觉上看到主网页（父页面）和 IFrame 网页（子页面）天衣无缝地拼装在一起，但在浏览器的内存里，它们是两套完全隔离的执行环境。

如果主站的域名是 www.taobao.com，而 IFrame 里加载的反馈表单指向了 sandbox.taobao.com，浏览器就会在这两个窗口之间竖起一道名为**“同源策略 (SOP)”**的防弹玻璃。

如果攻击者在 IFrame (sandbox.taobao.com) 的表单里成功注入了 <script> 恶意代码。

这段代码只能在 IFrame 自己的“小黑屋”里执行。它绝对无法穿透防弹玻璃，去读取外面主站 (www.taobao.com) 用户的敏感 Cookie，也无法修改主站的页面内容。

## IFrame 的“不安全性”

如果开发者没有正确配置，IFrame 会引入非常致命的安全漏洞，最典型的就是以下两种：

* 点击劫持 (Clickjacking / UI Redressing): 这是 IFrame 最臭名昭著的攻击方式。攻击者建立一个恶意网站（比如一个诱人的小游戏），然后在这个网页上用 IFrame 透明地叠加目标网站（比如某银行的转账页面，或者某个关键项目的后台管理页面）。受害者以为自己在点游戏里的“开始”，实际上点击的是透明 IFrame 里的“确认转账”或“删除资产”按钮。
* 顶层导航劫持 (Top-Level Navigation): 如果你在你的网页里嵌入了一个不可信的第三方 IFrame，这个 IFrame 里的恶意 JavaScript 可以执行 window.top.location = "http://malicious.com"。这会直接把受害者的整个浏览器标签页强行跳转到钓鱼网站。

为了应对这些威胁，现代浏览器为 IFrame 引入了强大的安全属性和 HTTP 响应头：

* sandbox 属性（限制 IFrame 权限）: HTML5 引入了 <iframe sandbox>。这是一个极其严格的隔离机制。一旦加上这个属性，IFrame 里面的页面将被禁止执行 JavaScript、禁止提交表单、禁止弹窗、甚至会被视为一个完全独立的跨域源（即使它加载的是同源链接）。开发者可以通过白名单（如 sandbox="allow-scripts"）来精准按需放权。
* 防止被别人 IFrame 嵌套（防御点击劫持）: * X-Frame-Options: 后端在 HTTP 响应头中设置 X-Frame-Options: DENY（完全禁止被嵌套）或 SAMEORIGIN（只允许同源嵌套）。
* CSP (Content-Security-Policy): 更现代的做法是使用 CSP 头 frame-ancestors 'self'。这直接从浏览器层面粉碎了点击劫持的可能。

## 现代前端的替代机制

在现代架构中，传统的 IFrame 正在被以下更优雅的机制取代：

1. 获取数据 vs. 获取完整页面 (CORS + Fetch/Axios)
   过去，如果想在一个页面显示另一个域名的天气预报，只能用 IFrame 嵌整个页面。
   现在，跨域资源共享 (CORS) 成为了标准。前端通过 AJAX/Fetch 直接向第三方 API 请求纯 JSON 数据，拿到数据后，利用 Vue 等框架的数据绑定功能，由前端自己渲染 UI 样式。这消除了跨域 UI 混淆的风险，也让性能大幅提升。
2. 前端组件化与 Shadow DOM
   当你需要在页面中引入一个完全独立、样式不会与全局冲突的模块（比如一个复杂的图表插件或自定义视频播放器）时，不再需要用 IFrame 硬生生隔离出一个新文档。
   Shadow DOM 允许浏览器在渲染 DOM 树时，创建一个“影子”节点。这个节点内部的 CSS 和 HTML 是完全封装的，外界的样式影响不进来，里面的机制也漏不出去。它实现了 IFrame 的“UI 隔离”特性，但没有 IFrame 巨大的内存开销。
3. 安全跨域通信：postMessage
   如果某些场景下必须使用 IFrame（比如集成第三方单点登录 SSO，或者嵌入完全不可控的外部业务线），旧时代只能利用 URL hash 盲目传参。
   现代浏览器提供了 window.postMessage API。它允许主窗口和 IFrame 之间安全地、异步地互发消息。但在安全测试时要注意： 如果接收端没有严格校验 event.origin（消息来源），这又会变成 DOM 型 XSS 的重灾区。

## 跨域IFrame和xss攻击

假设你正在测试一个大型项目，比如“淘宝”。这种级别的项目下通常包含非常多不同的域名（主站 www.taobao.com，以及各种边缘业务子域）。

为了防御主站被攻击，开发者在主站上放了一个“用户反馈表单”时，可能不会把表单代码直接写在主站里。相反，他们会通过 <iframe> 标签把表单嵌入进来，并且让这个 IFrame 指向一个完全不同的、专门用来处理危险输入的域名（比如 feedback.taobao-sandbox.com）。

这样做的巧妙之处在于：
这就利用了浏览器的“同源策略”。即使你在那个表单里成功注入了 XSS 代码，这段恶意代码也只能在 feedback.taobao-sandbox.com 这个沙箱域名的环境下运行。它绝对无法跨越同源策略的限制，去读取主站 www.taobao.com 的 Cookie、Local Storage 或者操控主站的 DOM 树。

如果你在测试时，习惯性地输入 <script>alert(1)</script>：

页面确实弹出了一个写着 1 的框。但实际上，这个弹窗可能只是在那个被隔离的 IFrame 里执行的。由于你只输出了一个毫无意义的 1，你根本不知道这个弹窗的**执行上下文（环境）**到底是谁。如果你把这个写进渗透报告里，很可能会被安全团队判定为“低危漏洞”，因为你根本没有威胁到主站。

为什么 alert(window.origin) 是高手的选择？
window.origin 在 JavaScript 中代表当前执行环境的源（协议 + 域名 + 端口）。

如果你把 Payload 换成 <script>alert(window.origin)</script>：

情况 A（打穿主站）： 弹窗显示 https://www.taobao.com。恭喜你，这是一个高危漏洞，你的恶意脚本成功在主站上下文中执行了！

情况 B（困在沙箱）： 弹窗显示 https://feedback.taobao-sandbox.com。你立刻就会冷静下来。你瞬间明白：哦，原来这个表单是在一个跨域的 IFrame 里处理的。虽然存在 XSS，但危害被隔离了。
