# 1.安装WampServer
下载地址：http://www.wampserver.com/

它已经包含了 PHP、Apache、Mysql 等服务,免去了开发人员将时间花费在繁琐的配置环境过程。

# 2.重新设置工作目录
在windows工具栏找到wampserver，打开apache→httpd.conf，搜索DocumentRoot，修改DocumentRoot和Directory到指定目录（“…/webBBS/”），保存，httpd-vhosts.conf进行同样的操作，然后重启所有服务。

打开浏览器，输入网址“localhost/test.php”，如正常打开则设置成功。

# 3.熟悉开发系统
访问localhost/debug_login.php进入开发者登录界面，数据库账号root，无密码，登录后页面跳转至debug_database.php，此处提供了最基本的数据库操作接口，可在此页面熟悉与数据库的交互以及学习代码写法等。

# 工作日志
### 10.7 ###
封装一键部署按钮“一刀999”。排除了oninvalid字段表单提交失败的错误。
