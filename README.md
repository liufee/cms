Feehi CMS
===============================
Feehi CMS based on yii2, easy to secondary development, with a common backend(include website setting, seo setting, article category, articles publish, files manage,backend/frontend menu manage, log track,RBAC, users,friendly links) and a relative simple frontend display articles.


Getting Started
---------------

* [Installation](docs/installation.md)
* [Backend Demo (username:feehicms,password:123456)](http://demo.cms.feehi.com/admin)
* [Frontend Demo](http://demo.cms.feehi.com)

Quick experience
----------------
```bash
 $ docker pull registry.cn-hangzhou.aliyuncs.com/liufee/cms
 $ docker run --name feehicms -h feehicms -itd -p 80:80 -p 22:22 liufee/cms
```

Tips:

 * First excute docker run commands needs few seconds to init mysql and FeehiCMS(execute yii migrate/up import sql).After seconds can http://127.0.0.1 will see the correct result. If you seen "An internal error ..." just take few minutes
 * You can enter the container through ssh, default password for linux user root is 123456
 * Fully docker environment click [here](http://www.github.com/liufee/docker)

HOW TO PARTICIPATE
---------

Your participation to Feehi CMS development is very welcome!

Any question/bug please click [here](http://www.github.com/liufee/cms/issues)

SCREENSHOT
---------

![](docs/backend.png)

![](docs/frontend.png)
