<?php
return [
    'supportEmail' => 'admin@feehi.com',
    'user.passwordResetTokenExpire' => 3600,
    'site' => [
        'url' => 'http://cms.feehi.com',//此配置用来正确的在前台显示后台上传的文件，会被后台 设置->网站设置 网站域名覆盖
        'sign' => '###~SITEURL~###',//数据库中保存的本站地址，展示时替换成正确url
    ],
    'category.template.directory' => [//分类列表页模版
        Yii::getAlias("@frontend/views/article/index.php") => "默认",
    ],
    'article.template.directory' => [//文章模版
        Yii::getAlias("@frontend/views/article/view.php") => "默认",
    ],
    'page.template.directory' => [//单页模版
        Yii::getAlias("@frontend/views/page/view.php") => "默认",
    ],
];
