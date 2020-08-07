<?php
return [
    'supportEmail' => 'admin@feehi.com',
    'user.passwordResetTokenExpire' => 3600,
    'site' => [
        /*
         * because frontend and backend may have different domain. but FeehiCMS put all uploads files to frontend/web/uploads.
         * so, at backend we should use the frontend url to display uploaded images.
         * this url will be covered by admin user filled at "/admin?r=setting/website"(if filled not an empty string)
         *
         * 因为前台和后台可能使用不同的域名，但是FeehiCMS把所有上传的文件放在frontend/web/uploads。
         * 因为，在后台显示已上传的图片时，需要拼接这个前台的域名才能正常显示图片。
         * 此值，会被后台管理页面填入的值覆盖掉（如果填入的值不为空的话）
         */
        'url' => 'http://cms.feehi.com',
        'sign' => '###~SITEURL~###',//cause domain may be changed, so database store a placeholder. when after fetched data should replace this with frontend domain
    ],
    'category.template.directory' => [//分类列表页模版
        Yii::getAlias("@frontend/views/article/index.php") => Yii::t("app", "Default"),
    ],
    'article.template.directory' => [//文章模版
        Yii::getAlias("@frontend/views/article/view.php") => Yii::t("app", "Default"),
    ],
    'page.template.directory' => [//单页模版
        Yii::getAlias("@frontend/views/page/view.php") => Yii::t("app", "Default"),
    ],
];
