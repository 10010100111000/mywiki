# 通过 Web Shell 上传执行远程代码

[本实验室](https://portswigger.net/web-security/file-upload/lab-file-upload-remote-code-execution-via-web-shell-upload)包含一个存在漏洞的图片上传功能。该功能在将用户上传的文件存储到服务器文件系统之前，不会对其进行任何验证。

要解决本实验，请上传一个基本的 PHP Web Shell，并使用它来窃取文件 /home/carlos/secret 的内容。使用实验横幅中提供的按钮提交此机密。

您可以使用以下凭证登录自己的帐户： wiener:peter
