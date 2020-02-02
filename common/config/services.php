<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-02-02 21:34
 */
return [
    \common\services\MenuServiceInterface::MenuService => [
        'class' => \common\services\MenuService::className(),
    ],
    \common\services\FriendlyLinkServiceInterface::ServiceName => [
        'class' => \common\services\FriendlyLinkService::className(),
    ],
    \common\services\CommentServiceInterface::ServiceName => [
        'class' => \common\services\CommentService::className(),
    ],
    \common\services\LogServiceInterface::ServiceName => [
        'class' => \common\services\LogService::className(),
    ],
    \common\services\SettingServiceInterface::ServiceName => [
        'class' => \common\services\SettingService::className(),
    ],
    \common\services\AdServiceInterface::ServiceName => [
        'class' => \common\services\AdService::className(),
    ],
    \common\services\AdminUserServiceInterface::ServiceName => [
        'class' => \common\services\AdminUserService::className(),
    ],
    \common\services\UserServiceInterface::ServiceName => [
        'class' => \common\services\UserService::className(),
    ],
    \common\services\RBACServiceInterface::ServiceName => [
        'class' =>\common\services\RBACService::className(),
    ],
    \common\services\CategoryServiceInterface::ServiceName => [
        'class' => \common\services\CategoryService::className(),
    ],
    \common\services\ArticleServiceInterface::ServiceName => [
        'class' => \common\services\ArticleService::className(),
    ],
    \common\services\BannerServiceInterface::ServiceName => [
        'class' => \common\services\BannerService::className(),
    ],
];