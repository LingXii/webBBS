# 1.安装WampServer
下载地址：http://www.wampserver.com/

它已经包含了 PHP、Apache、Mysql 等服务,免去了开发人员将时间花费在繁琐的配置环境过程。

# 2.重新设置工作目录
在windows工具栏找到wampserver，打开apache→httpd.conf，搜索DocumentRoot，修改DocumentRoot和Directory到指定目录（“…/webBBS/”），保存，httpd-vhosts.conf进行同样的操作，然后重启所有服务。

打开浏览器，输入网址“localhost/test.php”，如正常打开则设置成功。

# 3.熟悉开发系统
访问localhost/debug_login.php进入开发者登录界面，数据库账号root，无密码，登录后页面跳转至debug_database.php，此处提供了最基本的数据库操作接口，可在此页面熟悉与数据库的交互以及学习代码写法等。

# 4.各代码文件功能概述
## database_util.php
数据库操作的常用函数。

```query_one``` 在指定数据表中查询满足条件的唯一条目。

```find``` 在指定数据表中查找是否存在符合条件的条目，返回True或False.

```execute_sql``` 执行一条sql语句。

```build_web_database``` 网站数据库定义与初始化。

## sign_up.php
用户注册页面，包含一个注册表和一个注册函数。

~~**TODO: 输入字符的正确性检测与长度溢出检测**~~

## sign_in.php
用户登录页面，包含一个登录表单和一个登录函数。

## header.php
网页头部文件，左侧为返回主页（或上一页）超链接，右侧为用户信息。

~~**TODO: 头部左侧超链接给出浏览层次信息**~~

## index.php
主页(bid=1)与版面浏览页(bid>1)，列举所有帖子信息及链接。对于主页，附加列举所有版面及链接；对于非主页，登录用户显示文本编辑框用于发帖。

**TODO: 文本编辑改为独立页面，减少代码重复**

**TODO: 帖子分页显示**

**TODO: 版面管理员可直接在此页面管理帖子**

## post_reader.php
阅读指定帖子及其回复贴，登录用户可发表回复。

**TODO: 版面管理员可直接在此页面管理帖子与回复**

## board_manage.php
论坛超级管理员页面，可管理所有版面及其帖子，包括增删版面、帖子，任命版面管理员，发布论坛公告。

## post_manage.php
论坛某版面管理员页面，可管理该版面所有帖子，发布版面公告。

## user_space.php
用户的个人空间，用于查看用户信息，收发私信。

**TODO: 用户个人信息编辑**

**TODO: 用户间私信**

# 工作日志
### 10.7 ###
封装一键部署按钮“一刀999”。排除了oninvalid字段表单提交失败的错误。

### 11.3 ###
注册表单填写时进行了正确性检测，头部左侧显示了当前的浏览层次。