Feehi CMS
=================

(更多安装请访问.More installation info please visite) [http://cms.feehi.com](http://cms.feehi.com/help-install.html)

**Based on yii2.0, required php >=5.4
(include php7.0 or newer)**

Installing
------------------------

Install FeehiCMS only takes several steps

### Install via Composer
This is the preferred way of installing Feehi CMS. If you do not have Composer yet, you may install it by following the instructions [here](https://getcomposer.org/doc/00-intro.md#installation-nix "composer") .

With Composer installed, you can then install the application using the following commands:

```bash
$ php composer.phar global require "fxp/composer-asset-plugin:~1.1.1"
$ php composer.phar create-project feehi/cms webApp
```

### Install using git

```bash
$ git clone https://github.com/liufee/cms.git webApp
$ cd /path/to/webApp
$ php composer.phar global require "fxp/composer-asset-plugin:~1.1.1"
$ php composer.phar install
```

### Install from an Archive File
-------------------
1.click [here](http://7xjkuy.com1.z0.glb.clouddn.com/Feehi_CMS.zip) to download archive file

2.Visite http://frontend.dev/install.php


## Preparing application

After you install the application, you have to conduct the following steps to initialize
the installed application. You only need to do these once for all.

1. Open a console terminal, execute the `init` command and select `dev` as environment.

   ```
   /path/to/php-bin/php /path/to/yii-application/init
   ```

   If you automate it with a script you can execute `init` in non-interactive mode.

   ```
   /path/to/php-bin/php /path/to/yii-application/init --env=Production --overwrite=All
   ```

2. Create a new database and adjust the `components['db']` configuration in `common/config/main-local.php` accordingly.

3. Open a console terminal, apply migrations with command `/path/to/php-bin/php /path/to/yii-application/yii migrate`.

4. Set document roots of your web server:

   - for frontend `/path/to/yii-application/frontend/web/` and using the URL `http://frontend.dev/`
   - for backend `/path/to/yii-application/backend/web/` and using the URL `http://backend.dev/` also you can use `frontend.dev/admin`

   For Apache it could be the following:

   ```apache
       <VirtualHost *:80>
           ServerName frontend.dev
           DocumentRoot "/path/to/yii-application/frontend/web/"
           
           <Directory "/path/to/yii-application/frontend/web/">
               # use mod_rewrite for pretty URL support
               RewriteEngine on
               # If a directory or a file exists, use the request directly
               RewriteCond %{REQUEST_FILENAME} !-f
               RewriteCond %{REQUEST_FILENAME} !-d
               # Otherwise forward the request to index.php
               RewriteRule . index.php

               # use index.php as index file
               DirectoryIndex index.php

               # ...other settings...
           </Directory>
       </VirtualHost>
       
       <VirtualHost *:80>
           ServerName backend.dev
           DocumentRoot "/path/to/yii-application/backend/web/"
           
           <Directory "/path/to/yii-application/backend/web/">
               # use mod_rewrite for pretty URL support
               RewriteEngine on
               # If a directory or a file exists, use the request directly
               RewriteCond %{REQUEST_FILENAME} !-f
               RewriteCond %{REQUEST_FILENAME} !-d
               # Otherwise forward the request to index.php
               RewriteRule . index.php

               # use index.php as index file
               DirectoryIndex index.php

               # ...other settings...
           </Directory>
       </VirtualHost>
   ```

   For nginx:

   ```nginx
       server {
           charset utf-8;
           client_max_body_size 128M;

           listen 80; ## listen for ipv4
           #listen [::]:80 default_server ipv6only=on; ## listen for ipv6

           server_name frontend.dev;
           root        /path/to/yii-application/frontend/web/;
           index       index.php;

           access_log  /path/to/yii-application/log/frontend-access.log;
           error_log   /path/to/yii-application/log/frontend-error.log;

           location / {
               # Redirect everything that isn't a real file to index.php
               try_files $uri $uri/ /index.php$is_args$args;
           }

           # uncomment to avoid processing of calls to non-existing static files by Yii
           #location ~ \.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar)$ {
           #    try_files $uri =404;
           #}
           #error_page 404 /404.html;

           # deny accessing php files for the /assets directory
           location ~ ^/assets/.*\.php$ {
               deny all;
           }

           location ~ \.php$ {
               include fastcgi_params;
               fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
               fastcgi_pass 127.0.0.1:9000;
               #fastcgi_pass unix:/var/run/php5-fpm.sock;
               try_files $uri =404;
           }
       
           location ~* /\. {
               deny all;
           }
       }
        
       server {
           charset utf-8;
           client_max_body_size 128M;
       
           listen 80; ## listen for ipv4
           #listen [::]:80 default_server ipv6only=on; ## listen for ipv6
       
           server_name backend.dev;
           root        /path/to/yii-application/backend/web/;
           index       index.php;
       
           access_log  /path/to/yii-application/log/backend-access.log;
           error_log   /path/to/yii-application/log/backend-error.log;
       
           location / {
               # Redirect everything that isn't a real file to index.php
               try_files $uri $uri/ /index.php$is_args$args;
           }
       
           # uncomment to avoid processing of calls to non-existing static files by Yii
           #location ~ \.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar)$ {
           #    try_files $uri =404;
           #}
           #error_page 404 /404.html;

           # deny accessing php files for the /assets directory
           location ~ ^/assets/.*\.php$ {
               deny all;
           }

           location ~ \.php$ {
               include fastcgi_params;
               fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
               fastcgi_pass 127.0.0.1:9000;
               #fastcgi_pass unix:/var/run/php5-fpm.sock;
               try_files $uri =404;
           }
       
           location ~* /\. {
               deny all;
           }
       }
   ```

5. Change the hosts file to point the domain to your server.

   - Windows: `c:\Windows\System32\Drivers\etc\hosts`
   - Linux: `/etc/hosts`

   Add the following lines:

   ```
   127.0.0.1   frontend.dev
   127.0.0.1   backend.dev
   ```

To login into the application backend, the default username is admin and password is 123456.

To login into the application frontend, you need to first sign up, with any of your email address, username and password.

> to [Using advanced project template at shared hosting](https://github.com/yiisoft/yii2-app-advanced/blob/master/docs/guide/topic-shared-hosting.md).


That's all. You just need to wait for completion! After that you can access project locally by URLs:
* frontend: http://y2aa-frontend.dev
* backend: http://y2aa-backend.dev or http://y2aa-frontend.dev/admin
   
