FeehiCMS  __[(English)](docs/running_screenshot/README_EN.md)__  首款编写单元测试、功能测试、验收测试的yii2开源系统
===============================

基于yii2的CMS系统，运行环境与yii2(php>=5.4)一致。FeehiCMS旨在为yii2爱好者提供一个基础功能稳定完善的系统，使开发者更专注于业务功能开发。
FeehiCMS没有对yii2做任何的修改、封装，但是把yii2的一些优秀特性几乎都用在了FeehiCMS上，虽提供文档，
但FeehiCMS提倡简洁、快速上手，基于FeehiCMS开发可以无需文档，反倒FeehiCMS为yii2文档提供了最好的实例

[![Latest Stable Version](https://poser.pugx.org/feehi/cms/v/stable)](https://packagist.org/packages/feehi/cms)
[![License](https://poser.pugx.org/feehi/cms/license)](https://packagist.org/packages/feehi/cms)
[![Build Status](https://www.travis-ci.org/liufee/cms.svg?branch=master)](https://www.travis-ci.org/liufee/cms)


演示站点
-------
演示站点后台   **用户名:feehicms 密码123456**
* 后台 [http://demo.cms.feehi.com/admin](http://demo.cms.feehi.com/admin)
* 前台 [http://demo.cms.feehi.com](http://demo.cms.feehi.com/)
* api [http://demo.cms.feehi.com/api/articles](http://demo.cms.feehi.com/api/articles)


[更新记录](docs/UPGRADING.md)
-------


帮助
---------------
1. 开发文档[http://doc.feehi.com](http://doc.feehi.com)

2. QQ群 936448696

3. 微信 <br> ![微信](http://img-1251086492.cosgz.myqcloud.com/github/wechat.png)

4. Email job@feehi.com

5. [bug反馈](http://www.github.com/liufee/cms/issues)


功能
---------------
 * 多语言
 * 单元测试
 * 功能测试
 * 验收测试
 * RBAC权限管理
 * restful api
 * 文章管理 
 * 操作日志
 * 适配手机
 
 FeehiCMS提供完备的web系统基础通用功能，包括前后台菜单管理,文章标签,广告,banner,缓存,网站设置,seo设置,邮件设置,分类管理,单页...
 
 
使用Docker
-------
1.下载镜像
```bash
    $ docker pull registry.cn-hangzhou.aliyuncs.com/feehi/cms #FQ后建议直接使用docker pull feehi/cms
```
    
2.创建容器
```bash
    $ docker run --name feehicms -h feehicms -itd -v /path/to/data:/data -e DBDSN=sqlite:/data/feehi.db -e TablePrefix=feehi_ -e AdminUsername=admin -e AdminPassword=123456 -p 8080:80 feehi/cms
```
以上命令将会自动初始化FeehiCMS，并导入数据库(默认数据库为sqlite)  
如果需要更使用其他数据库，比如mysql，执行:  
```bash
    $ docker run --name feehicms -h feehicms -itd -e DBDSN=mysql:host=mysql-ip;dbname=feehi -e DBUser=dbuser -e DBPassword=dbpassword -e TablePrefix=feehi_ -e AdminUsername=admin -e AdminPassword=123456 -p 8080:80 feehi/cms
```
如果需要使用postgresql则将DBDSN改为pgsql:host=pgsql-ip  
  
也可以仅初始化FeehiCMS，然后通过web在线安装 
```bash
    $ docker run --name feehicms -h feehicms -itd -p 8080:80 feehi/cms -o start
```
然后访问http://ip:port/install.php，根据提示选择数据库类型，填写数据库用户名、数据库密码、后台管理员用户名、密码完成安装。  
  
  
以上方式启动的容器只能用作开发环境，容器启动命令最终调用为php -S 0.0.0.0:80,如果用作production，可以执行
```bash
    $ docker run --name feehicms -h feehicms -itd -p 8080:80 feehi/cms -m start
```
容器将启动php-fpm，并监听9000端口，配合nginx使用。nginx配置大致为
```bash
    location ~ \.php$ {
        ...
        fastcgi_pass fpm-ip:9000;
        fastcgi_param  SCRIPT_FILENAME  /usr/local/feehicms/frontend/web$fastcgi_script_name;
        ...
    }
```
**因为yii2会生成js/css，以及新上传的文件（图片）需要nginx webroot使用php fpm容器同一个文件夹:/usr/local/feehicms/frontend/web**


安装
---------------
前置条件: 如未特别说明，本文档已默认您把php命令加入了环境变量，如果您未把php加入环境变量，请把以下命令中的php替换成/path/to/php
> 无论是使用归档文件还是composer，都有相应阶段让您填入后台管理用户名、密码
1. 使用归档文件(简单，适合没有yii2经验者)
    1. 下载FeehiCMS源码 [点击此处下载最新版](http://resource-1251086492.cossh.myqcloud.com/Feehi_CMS.zip)
    2. 解压到目录 
    3. 配置web服务器[web服务器配置](docs/WEBSERVER_CONFIG.md)
    4. 浏览器打开 http://localhost/install.php 按照提示完成安装(若使用php内置web服务a器则地址为 http://localhost:8080/install.php )
    5. 完成
    
2. 使用composer (`推荐使用此方式安装`) 
     >composer的安装以及国内镜像设置请点击 [此处](https://developer.aliyun.com/composer)
     
     >以下命令默认您已全局安装composer，如果您是局部安装的composer:请使用php /path/to/composer.phar来替换以下命令中的composer
     
     1. 使用composer创建FeehiCMS项目
        
        ```bash
            $ composer create-project feehi/cms webApp //此命令创建的FeehiCMS项目不能平滑升级新版本(目录结构简单,目前主力维护版本)
        ```
     2. 依次执行以下命令初始化yii2框架以及导入数据库
         ```bash
         $ cd webApp
         $ php ./init --env=Development #初始化yii2框架，线上环境请使用--env=Production
         $ php ./yii migrate/up --interactive=0 #导入FeehiCMS sql数据库，执行此步骤之前请先到common/config/main-local.php修改成正确的数据库配置
         ```
     3. 配置web服务器[web服务器配置](docs/WEBSERVER_CONFIG.md)
     4. 完成
 
 
 
运行测试
-------
1. 仅运行单元测试,功能测试(不需要配置web服务器)
 ```bash
    cd /path/to/webApp
    vendor/bin/codecept run
 ```
2. 运行单元测试,功能测试,验收测试(需要配置完web服务器)
    1. 分别拷贝backend,frontend,api三个目录下的tests/acceptance.suite.yml.example到各自目录，并均重名为acceptance.suite.yml,且均修改里面的url为各自的访问url地址
    2. 与上(仅运行单元测试,功能测试)命令一致


项目展示
------------
* [山东城市服务技师学院](http://www.sdcc.edu.cn/)   
* [优悦娱乐网](http://www.qqyouyue.com/)  
* [吉安市食品药品监督管理局](http://www.jamsda.gov.cn/)  
* [完美娱乐](http://www.qqwanmei.com/)  
* [房产网](http://www.itufang.cn/)
* [中丞法拍网](http://www.fapaihouse.com/)  
* [51前途网](http://www.51uit.com/) 
* [用友财务软件](http://www.myyonyou.cn/) 
*  ......


[运行效果](docs/running_screenshot)
---------
