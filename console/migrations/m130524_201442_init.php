<?php

use yii\db\Migration;

/**
 * migrate database tables execute command:
 *   windows: /path/to/php /path/to/feehiproject/yii.bat migrate frontendUri //your-frontend-domain.com
 *   unix-like: /path/to/php /path/to/feehiproject/yii migrate frontendUri //your-frontend-domain.com
 *
 * Class m130524_201442_init
 */
class m130524_201442_init extends Migration
{
    public function up()
    {
        $params = $this->getParams();
        $frontendUri = "";
        isset($params['frontendUri']) && $frontendUri = $params['frontendUri'];
        while( strpos($frontendUri, 'http://') !== 0 && strpos($frontendUri, 'https://') !== 0 && strpos($frontendUri, '//') !== 0 ){
            if( $frontendUri == "" ){
                yii::$app->controller->stdout("Input your frontend web url(like //www.xxx.com) :");
            }else {
                yii::$app->controller->stdout("Must begin with 'http', 'https' or '//' :");
            }
            $frontendUri = trim(fgets(STDIN));
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }


        //table user
        $userId = $this->primaryKey();
        $userUsername = $this->string()->notNull()->unique();
        $userAuthKey = $this->string(32)->notNull();
        $userPasswordHash =  $this->string()->notNull();
        $userPasswordResetToken = $this->string()->unique();
        $userEmail = $this->string()->notNull()->unique();
        $userAvatar = $this->string()->defaultValue('');
        $userStatus = $this->smallInteger()->notNull()->defaultValue(10);
        $userCreatedAt = $this->integer()->notNull();
        $userUpdatedAt = $this->integer()->notNull();

        if ($this->db->driverName === 'mysql') {
            $userId->unsigned()->comment("user id(auto increment)");
            $userUsername->comment("username");
            $userAuthKey->comment("auth key for generate logged in cookie");
            $userPasswordHash->comment("crypt password");
            $userPasswordResetToken->comment("reset password temp token");
            $userEmail->comment("user email");
            $userAvatar->comment("avatar url");
            $userStatus->comment("user status, (normal:10)");
            $userCreatedAt->comment("created at");
            $userUpdatedAt->comment("updated at");
        }

        $this->createTable('{{%user}}', [
            'id' => $userId,
            'username' => $userUsername,
            'auth_key' => $userAuthKey,
            'password_hash' => $userPasswordHash,
            'password_reset_token' => $userPasswordResetToken,
            'email' => $userEmail,
            'avatar' => $userAvatar,
            'status' => $userStatus,
            'created_at' => $userCreatedAt,
            'updated_at' => $userUpdatedAt,
        ], $tableOptions);

        $this->batchInsert("{{%user}}", ['id','username','auth_key','password_hash','email','status','created_at','updated_at',],
            [
                /**[
                    '1',
                    'aaa',
                    'y-I4ci4glWqom_ZeW6IItOCWFx69FjqQ',
                    '$2y$13$h2GSh/y8qa1WU.ZRVU3VaOr2/Zfh/VxCUfLmbY4xNeZ1Ql2lbMF36',
                    'aaa@feehi.com',
                    '10',
                    '1469070407',
                    '0',
                ],
                [
                    '2',
                    'bbb',
                    '9wMhzQEqGW8h1_NFBoCYY3StQ_ZJ8UaM',
                    '$2y$13$MbDa4j1TujVid9Zk0saOYu7eGk/N52nOJjTYr22dCYRhJ/D9jV29.',
                    'bbb@feehi.com',
                    '10',
                    '1469070568',
                    '0',
                ],**/
            ]
        );


        //table admin_user
        $adminUserId = $this->primaryKey();
        $adminUsername = $this->string()->notNull()->unique();
        $adminUserAuthKey = $this->string(32)->notNull();
        $adminUserPasswordHash =  $this->string()->notNull();
        $adminUserPasswordResetToken = $this->string()->unique();
        $adminUserEmail = $this->string()->notNull()->unique();
        $adminUserAvatar = $this->string()->defaultValue('');
        $adminUserStatus = $this->smallInteger()->notNull()->defaultValue(10);
        $adminUserCreatedAt = $this->integer()->notNull();
        $adminUserUpdatedAt = $this->integer()->notNull();

        if ($this->db->driverName === 'mysql') {
            $adminUserId->unsigned()->comment("admin user id(auto increment)");
            $adminUsername->comment("admin username");
            $adminUserAuthKey->comment("admin user auth key for generate logged in cookie");
            $adminUserPasswordHash->comment("admin user crypt password");
            $adminUserPasswordResetToken->comment("admin user reset password temp token");
            $adminUserEmail->comment("admin user email");
            $adminUserAvatar->comment("admin user avatar url");
            $adminUserStatus->comment("admin user status, (normal:10)");
            $adminUserCreatedAt->comment("created at");
            $adminUserUpdatedAt->comment("updated at");
        }

        $this->createTable('{{%admin_user}}', [
            'id' => $adminUserId,
            'username' => $adminUsername,
            'auth_key' => $adminUserAuthKey,
            'password_hash' => $adminUserPasswordHash,
            'password_reset_token' => $adminUserPasswordResetToken,
            'email' => $adminUserEmail,
            'avatar' => $adminUserAvatar,
            'status' => $adminUserStatus,
            'created_at' => $adminUserCreatedAt,
            'updated_at' => $adminUserUpdatedAt,
        ], $tableOptions);

        $this->batchInsert("{{%admin_user}}", ['id','username','auth_key','password_hash','email','avatar','status','created_at','updated_at'],
            [
                [
                    "1",
                    "admin",
                    "zr9mY7lt23oAhj_ZYjydbLJKcbE3FJ19",
                    "$2y$13$8aF72c/7Nqq/atyMivhVTej0bIXS1t8daPJXKtVjFzJUsG68eGgaG",
                    "admin@feehi.com",
                    "",
                    "10",
                    "1468288038",
                    "1476711945",

                ],
                /*[
                    "2",
                    "fff",
                    "1JC2paBZhxrLPXNEpGDqW8Bp130x0_g6",
                    '$2y$13$v.WxC/zKWasDR2fVZsa5u.xoVCh.8VE1QtyqCQNFrZO7DgEvZoZhS',
                    "fff@feehi.com",
                    "",
                    "10",
                    "1469087451",
                    "1476711969",

                ],**/
            ]
        );


        //table admin_log
        $adminLogId = $this->primaryKey();
        $adminLogUserId = $this->integer()->unsigned()->notNull();
        $adminLogRoute = $this->string()->defaultValue('')->notNull();
        $adminLogDescription = $this->text();
        $adminLogCreatedAt = $this->integer()->unsigned()->notNull();

        if ($this->db->driverName === 'mysql') {
            $adminLogId->unsigned()->comment("admin log id(auto increment)");
            $adminLogUserId->comment("admin user id");
            $adminLogRoute->comment("admin user operate route, like article/create");
            $adminLogDescription->comment("admin user operate description");
            $adminLogCreatedAt->comment("created at");
        }

        $this->createTable('{{%admin_log}}', [
            'id' => $adminLogId,
            'user_id' => $adminLogUserId,
            'route' => $adminLogRoute,
            'description' => $adminLogDescription,
            'created_at' => $adminLogCreatedAt
        ], $tableOptions);
        $this->batchInsert("{{%admin_log}}", ["id", "user_id", "route", "description", "created_at"], [
            [
                '1',
                '1',
                '/feehi/index',
                'this is a demo',
                '1468293965'
            ]
        ]);


        //table category
        $categoryId = $this->primaryKey();
        $categoryParentId = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $categoryName = $this->string()->notNull();
        $categoryAlias = $this->string()->notNull();
        $categorySort = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $categoryRemark = $this->string()->defaultValue('')->notNull();
        $categoryCreatedAt = $this->integer()->unsigned()->notNull();
        $categoryUpdatedAt = $this->integer()->unsigned()->defaultValue(0)->notNull();

        if ($this->db->driverName === 'mysql') {
            $categoryId->unsigned()->comment("category id(auto increment)");
            $categoryParentId->comment("category parent id(an exist category id)");
            $categoryName->comment("category name");
            $categoryAlias->comment("category alias");
            $categorySort->comment("category order");
            $categoryRemark->comment("category remark info");
            $categoryCreatedAt->comment("created at");
            $categoryUpdatedAt->comment("updated at");
        }


        $this->createTable('{{%category}}', [
            'id' => $categoryId,
            'parent_id' => $categoryParentId,
            'name' => $categoryName,
            'alias' => $categoryAlias,
            'sort' => $categorySort,
            'remark' => $categoryRemark,
            'created_at' => $categoryCreatedAt,
            'updated_at' => $categoryUpdatedAt,
        ], $tableOptions);

        $this->batchInsert("{{%category}}", ['id','parent_id','name','alias','sort','created_at','updated_at','remark'],
            [
                [
                    '1',
                    '0',
                    'php',
                    'php',
                    '0',
                    '1468293958',
                    '0',
                    '',
                ],
                [
                    '2',
                    '0',
                    'java',
                    'java',
                    '0',
                    '1468293965',
                    '0',
                    '',
                ],
                [
                    '3',
                    '0',
                    'javascript',
                    'javascript',
                    '0',
                    '1468293974',
                    '0',
                    '',
                ],
            ]
        );


        //table article
        $articleId = $this->primaryKey();
        $articleCategoryId = $this->integer()->defaultValue(0)->notNull();
        $articleType = $this->integer()->defaultValue(0)->notNull();
        $articleTitle = $this->string()->notNull();
        $articleSubTitle = $this->string()->defaultValue('')->notNull();
        $articleSummary = $this->string()->defaultValue('')->notNull();
        $articleThumb = $this->string()->defaultValue('')->notNull();
        $articleSeoTitle = $this->string()->defaultValue('')->notNull();
        $articleSeoKeywords = $this->string()->defaultValue('')->notNull();
        $articleSeoDescription = $this->string()->defaultValue('')->notNull();
        $articleStatus = $this->smallInteger()->unsigned()->defaultValue(1)->notNull();
        $articleSort = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $articleAuthorId = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $articleAuthorName = $this->string()->defaultValue('')->notNull();
        $articleScanCount = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $articleCommentCount = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $articleCanComment = $this->smallInteger()->unsigned()->defaultValue(1)->notNull();
        $articleVisibility = $this->smallInteger()->unsigned()->defaultValue(1)->notNull();
        $articlePassword = $this->string()->defaultValue('')->notNull();
        $articleFlagHeadLine = $this->smallInteger()->unsigned()->defaultValue(0)->notNull();
        $articleFlagRecommend = $this->smallInteger()->unsigned()->defaultValue(0)->notNull();
        $articleFlagSlideShow = $this->smallInteger()->unsigned()->defaultValue(0)->notNull();
        $articleFlagSpecialRecommend = $this->smallInteger()->unsigned()->defaultValue(0)->notNull();
        $articleFlagRoll = $this->smallInteger()->unsigned()->defaultValue(0)->notNull();
        $articleFlagBold = $this->smallInteger()->unsigned()->defaultValue(0)->notNull();
        $articleFlagPicture = $this->smallInteger()->unsigned()->defaultValue(0)->notNull();
        $articleCreatedAt = $this->integer()->unsigned()->notNull();
        $articleUpdatedAt = $this->integer()->unsigned()->defaultValue(0)->notNull();

        if ($this->db->driverName === 'mysql') {
            $articleId->unsigned()->comment("article id(auto increment)");
            $articleCategoryId->unsigned()->comment("article category id");
            $articleType->unsigned()->comment("type(0 article, 1 page)");
            $articleTitle->comment("article title");
            $articleSubTitle->comment("article sub title");
            $articleSummary->comment("article summary");
            $articleThumb->comment("thumb");
            $articleSeoTitle->comment("seo title");
            $articleSeoKeywords->comment("seo keywords");
            $articleSeoDescription->comment("seo description");
            $articleStatus->comment("article status(0 draft,1 published)");
            $articleSort->comment("article order");
            $articleAuthorId->comment("article published by admin user id");
            $articleAuthorName->comment("article published by admin username");
            $articleScanCount->comment("article visited count");
            $articleCommentCount->comment("article comment count");
            $articleCanComment->comment("article can be comment. (0 no, 1 yes)");
            $articleVisibility->comment("article visibility(1.public,2.after commented,3.password,4.after logged in)");
            $articlePassword->comment("article password(plain text)");
            $articleFlagHeadLine->comment("is head line(0 no, 1 yes");
            $articleFlagRecommend->comment("is recommend(0 no, 1 yes");
            $articleFlagSlideShow->comment("is slide show(0 no, 1 yes");
            $articleFlagSpecialRecommend->comment("is special recommend(0 no, 1 yes");
            $articleFlagRoll->comment("is roll(0 no, 1 yes");
            $articleFlagBold->comment("is bold(0 no, 1 yes");
            $articleFlagPicture->comment("is picture(0 no, 1 yes");
            $articleCreatedAt->comment("created at");
            $articleUpdatedAt->comment("updated at");
        }

        $this->createTable('{{%article}}', [
            'id' => $articleId,
            'cid' => $articleCategoryId,
            'type' => $articleType,
            'title' => $articleTitle,
            'sub_title' => $articleSubTitle,
            'summary' => $articleSummary,
            'thumb' => $articleThumb,
            'seo_title' => $articleSeoTitle,
            'seo_keywords' => $articleSeoKeywords,
            'seo_description' => $articleSeoDescription,
            'status' => $articleStatus,
            'sort' => $articleSort,
            'author_id' => $articleAuthorId,
            'author_name' => $articleAuthorName,
            'scan_count' => $articleScanCount,
            'comment_count' => $articleCommentCount,
            'can_comment' => $articleCanComment,
            'visibility' => $articleVisibility,
            'password' => $articlePassword,
            'flag_headline' => $articleFlagHeadLine,
            'flag_recommend' => $articleFlagRecommend,
            'flag_slide_show' => $articleFlagSlideShow,
            'flag_special_recommend' => $articleFlagSpecialRecommend,
            'flag_roll' => $articleFlagRoll,
            'flag_bold' => $articleFlagBold,
            'flag_picture' => $articleFlagPicture,
            'created_at' => $articleCreatedAt,
            'updated_at' => $articleUpdatedAt,
        ], $tableOptions);

        $articles = require(__DIR__.'/article.php');
        foreach ($articles['article'] as $item){
            $this->insert("{{%article}}", [
                'id' => $item[0],
                'cid' => $item[1],
                'type' => $item[2],
                'title' => $item[3],
                'sub_title' => $item[4],
                'summary' => $item[5],
                'thumb' => $item[6],
                'seo_title' => $item[7],
                'seo_keywords' => $item[8],
                'seo_description' => $item[9],
                'status' => $item[10],
                'sort' => $item[11],
                'author_id' => $item[12],
                'author_name' => $item[13],
                'scan_count' => $item[14],
                'comment_count' => $item[15],
                'can_comment' => $item[16],
                'visibility' => $item[17],
                'flag_headline' => $item[18],
                'flag_recommend' => $item[19],
                'flag_slide_show' => $item[20],
                'flag_special_recommend' => $item[21],
                'flag_roll' => $item[22],
                'flag_bold' => $item[23],
                'flag_picture' => $item[24],
                'created_at' => $item[25],
                'updated_at' => $item[26],
            ]);
        }

        //table article_content
        $articleContentId = $this->primaryKey();
        $articleContentArticleId = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $articleContentContent = $this->text()->notNull();

        if ($this->db->driverName === 'mysql') {
            $articleContentId->unsigned()->comment("article content id(auto increment)");
            $articleContentArticleId->comment("article id");
            $articleContentContent->comment("article content");
        }

        $this->createTable('{{%article_content}}', [
            'id' => $articleContentId,
            'aid' => $articleContentArticleId,
            'content' => $articleContentContent,
        ], $tableOptions);

        foreach($articles['article_content'] as $item){
            $this->insert("{{%article_content}}", ['aid'=>$item[1], 'content'=>$item[2]]);
        }


        //table article_meta
        $articleMetaId = $this->primaryKey();
        $articleMetaAid = $this->integer()->unsigned()->notNull();
        $articleMetaKey = $this->string()->defaultValue('')->notNull();
        $articleMetaValue = $this->text()->notNull();
        $articleMetaCratedAt = $this->integer()->unsigned()->notNull();

        if ($this->db->driverName === 'mysql') {
            $articleMetaId->unsigned()->comment("article meta id(auto increment)");
            $articleMetaAid->comment("article id");
            $articleMetaKey->comment("key");
            $articleMetaValue->comment("value");
            $articleMetaCratedAt->comment("article meta created at");
        }

        $this->createTable('{{%article_meta}}', [
            'id' => $articleMetaId,
            'aid' => $articleMetaAid,
            'key' => $articleMetaKey,
            'value' => $articleMetaValue,
            'created_at' => $articleMetaCratedAt,
        ], $tableOptions);

        $this->createIndex("article_meta_index_aid", "{{%article_meta}}", 'aid');
        $this->createIndex("article_meta_index_key", "{{%article_meta}}", 'key');

        $this->batchInsert("{{%article_meta}}", ['aid','key','value','created_at'],
            [
                ['1','tag','AngularJS',1507514051],
                ['3','tag','Facebook',1507514051],
                ['3','tag','hack',1507514051],
                ['3','tag','php',1507514051],
                ['5','tag','gc',1507514051],
                ['5','tag','垃圾回收',1507514051],
                ['5','tag','java',1507514051],
                ['6','tag','php7',1507514051],
                ['6','tag','php',1507514051],
                ['6','tag','wordpress',1507514051],
                ['8','tag','spring',1507514051],
                ['8','tag','java',1507514051],
                ['9','tag','css',1507514051],
                ['9','tag','重构',1507514051],
                ['10','tag','php',1507514051],
                ['10','tag','分页',1507514051],
                ['11','tag','php脚本',1507514051],
                ['11','tag','下载',1507514051],
                ['11','tag','代码下载',1507514051],
                ['12','tag','java',1507514051],
                ['12','tag','Javascript',1507514051],
                ['12','tag','Lisp',1507514051],
                ['12','tag','php',1507514051],
                ['12','tag','Python',1507514051],
                ['12','tag','Ruby',1507514051],
                ['12','tag','编程语言',1507514051],
                ['12','tag','趣文',1507514051],
                ['13','tag','缓存',1507514051],
                ['13','tag','浏览器缓存',1507514051],
                ['13','tag','http协议',1507514051],
                ['14','tag','javascript',1507514051],
                ['14','tag','流行',1507514051],
                ['15','tag','java',1507514051],
                ['16','tag','java',1507514051],
                ['16','tag','用户界面',1507514051],
                ['17','tag','css',1507514051],
                ['17','tag','样式',1507514051],
                ['18','tag','java',1507514051],
                ['18','tag','入门',1507514051],
                ['18','tag','编程书籍',1507514051],
                ['19','tag','java',1507514051],
                ['19','tag','java8',1507514051],
                ['19','tag','垃圾收集',1507514051],
                ['20','tag','jvm',1507514051],
                ['20','tag','java',1507514051],
                ['21','tag','jvm',1507514051],
                ['21','tag','java',1507514051],
                ['22','tag','java',1507514051],
                ['22','tag','java集合',1507514051],
            ]
        );

        //table comment
        $commentId = $this->primaryKey();
        $commentArticleId = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $commentUserId = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $commentAdminUserId = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $commentReplyTo = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $commentNickname = $this->string()->defaultValue('游客')->notNull();
        $commentEmail = $this->string()->defaultValue('')->notNull();
        $commentWebsiteUrl = $this->string()->defaultValue('')->notNull();
        $commentContent  = $this->string()->notNull();
        $commentIp = $this->string()->defaultValue('')->notNull();
        $commentStatus = $this->smallInteger()->unsigned()->defaultValue(0)->notNull();
        $commentCreatedAt = $this->integer()->unsigned()->notNull();
        $commentUpdatedAt = $this->integer()->unsigned()->defaultValue(0)->notNull();

        if ($this->db->driverName === 'mysql') {
            $commentId->unsigned()->comment("comment id(auto increment)");
            $commentArticleId->comment("article id");
            $commentUserId->comment("user id(0 for guest)");
            $commentAdminUserId->comment("admin user id(other user reply 0)");
            $commentReplyTo->comment("reply to comment id");
            $commentNickname->comment("user nickname");
            $commentEmail->comment("email");
            $commentWebsiteUrl->comment("user website");
            $commentContent->comment("comment content");
            $commentIp->comment("user ip");
            $commentStatus->comment("comment status(0 to be audit, 1 approved, 2 reject");
            $commentCreatedAt->comment("created at");
            $commentUpdatedAt->comment("updated at");
        }

        $this->createTable('{{%comment}}', [
            'id' => $commentId,
            'aid' => $commentArticleId,
            'uid' => $commentUserId,
            'admin_id' => $commentAdminUserId,
            'reply_to' => $commentReplyTo,
            'nickname' => $commentNickname,
            'email' => $commentEmail,
            'website_url' => $commentWebsiteUrl,
            'content' => $commentContent,
            'ip' => $commentIp,
            'status' => $commentStatus,
            'created_at' => $commentCreatedAt,
            'updated_at' => $commentUpdatedAt,
        ], $tableOptions);

        $this->createIndex("comment_index_aid", "{{%comment}}", "aid");

        $this->batchInsert("{{%comment}}", ['id','aid','uid','reply_to','nickname','email','website_url','content','ip','status','created_at','updated_at'],
            [
                [
                    "1",
                    "12",
                    "0",
                    "0",
                    "aaa",
                    "",
                    "",
                    "你好，世界！",
                    "127.0.0.1",
                    "1",
                    "1476066961",
                    "0",

                ],
                [
                    "2",
                    "22",
                    "0",
                    "0",
                    "aaa",
                    "",
                    "",
                    " :mrgreen:  :roll: 哎哟，不错哟~",
                    "127.0.0.1",
                    "1",
                    "1476066990",
                    "0",

                ],
                [
                    "3",
                    "22",
                    "0",
                    "2",
                    "bbb",
                    "",
                    "",
                    "呵呵哒",
                    "127.0.0.1",
                    "1",
                    "1476067011",
                    "0",

                ],
                [
                    "4",
                    "12",
                    "0",
                    "0",
                    "ccc",
                    "",
                    "",
                    " :shock: ",
                    "127.0.0.1",
                    "1",
                    "1476067042",
                    "0",

                ],
                [
                    "5",
                    "12",
                    "0",
                    "0",
                    "aaa",
                    "",
                    "",
                    "嘻嘻嘻",
                    "127.0.0.1",
                    "1",
                    "1476067060",
                    "0",

                ],
                [
                    "6",
                    "21",
                    "0",
                    "0",
                    "aaa",
                    "",
                    "",
                    "流弊哄哄~~~",
                    "127.0.0.1",
                    "1",
                    "1476067093",
                    "0",

                ],
            ]
        );


        //table friendly_link
        $friendlyLinkId = $this->primaryKey();
        $friendlyLinkName = $this->string()->notNull();
        $friendlyLinkImage = $this->string()->defaultValue('')->notNull();
        $friendlyLinkURL = $this->string()->defaultValue('')->notNull();
        $friendlyLinkTarget = $this->string()->defaultValue('_blank')->notNull();
        $friendlyLinkSort = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $friendlyLinkStatus = $this->smallInteger()->unsigned()->defaultValue(0)->notNull();
        $friendlyLinkCreatedAt = $this->integer()->unsigned()->notNull();
        $friendlyLinkIdUpdatedAt = $this->integer()->unsigned()->defaultValue(0)->notNull();

        if ($this->db->driverName === 'mysql') {
            $friendlyLinkId->unsigned()->comment("friendly link id(auto increment)");
            $friendlyLinkName->comment("website name");
            $friendlyLinkImage->comment("website icon url");
            $friendlyLinkURL->comment("website url");
            $friendlyLinkTarget->comment("open method(_blank, _self)");
            $friendlyLinkSort->comment("order");
            $friendlyLinkStatus->comment("status(0 hide, 1 display");
            $friendlyLinkCreatedAt->comment("created at");
            $friendlyLinkIdUpdatedAt->comment("updated at");
        }

        $this->createTable('{{%friendly_link}}', [
            'id' => $friendlyLinkId,
            'name' => $friendlyLinkName,
            'image' => $friendlyLinkImage,
            'url' => $friendlyLinkURL,
            'target' => $friendlyLinkTarget,
            'sort' => $friendlyLinkSort,
            'status' => $friendlyLinkStatus,
            'created_at' => $friendlyLinkCreatedAt,
            'updated_at' => $friendlyLinkIdUpdatedAt,
        ], $tableOptions);

        $this->batchInsert("{{%friendly_link}}", ['id','name','image','url','target','sort','status','created_at','updated_at'],
            [
                [
                    '1',
                    '飞嗨博客',
                    '',
                    'http://blog.feehi.com',
                    '_blank',
                    '0',
                    '1',
                    '1468303851',
                    '0',
                ],
                [
                    '2',
                    '飞嗨网',
                    '',
                    'http://www.feehi.com',
                    '_blank',
                    '0',
                    '1',
                    '1468303882',
                    '0',
                ],
                [
                    '3',
                    '36kr',
                    '',
                    'http://www.36kr.com',
                    '_blank',
                    '0',
                    '1',
                    '1468303902',
                    '0',
                ],
                [
                    '4',
                    '破晓电影',
                    '',
                    'http://www.poxiao.com',
                    '_blank',
                    '0',
                    '1',
                    '1468303938',
                    '0',
                ],
                [
                    '5',
                    '翠竹林主题',
                    '',
                    'http://www.cuizl.com/',
                    '_blank',
                    '0',
                    '1',
                    '1468303974',
                    '0',
                ],
            ]
        );


        //table menu
        $menuId = $this->primaryKey();
        $menuType = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $menuParentId = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $menuName = $this->string()->notNull();
        $menuURL = $this->string()->notNull();
        $menuIcon = $this->string()->defaultValue('')->notNull();
        $menuSort = $this->float()->unsigned()->defaultValue(0)->notNull();
        $menuTarget = $this->string()->defaultValue('_blank')->notNull();
        $menuIsAbsoluteURL = $this->smallInteger()->unsigned()->defaultValue(0)->notNull();
        $menuIsDisplay = $this->smallInteger()->unsigned()->defaultValue(1)->notNull();
        $menuCreatedAt = $this->integer()->unsigned()->notNull();
        $menuUpdatedAt = $this->integer()->unsigned()->defaultValue(0)->notNull();

        if ($this->db->driverName === 'mysql') {
            $menuId->unsigned()->comment("menu id(auto increment)");
            $menuType->comment("menu type(0 backend, 1 frontend");
            $menuParentId->comment("parent menu id");
            $menuName->comment("menu name");
            $menuURL->comment("menu url");
            $menuIcon->comment("menu icon");
            $menuSort->comment("menu order");
            $menuTarget->comment("open method(_blank, _self");
            $menuIsAbsoluteURL->comment("is absolute url");
            $menuIsDisplay->comment("is display(0 no, 1 yes");
            $menuCreatedAt->comment("created at");
            $menuUpdatedAt->comment("updated at");
        }

        $this->createTable('{{%menu}}', [
            'id' => $menuId,
            'type' => $menuType,
            'parent_id' => $menuParentId,
            'name' => $menuName,
            'url' => $menuURL,
            'icon' => $menuIcon,
            'sort' => $menuSort,
            'target' => $menuTarget,
            'is_absolute_url' => $menuIsAbsoluteURL,
            'is_display' => $menuIsDisplay,
            'created_at' => $menuCreatedAt,
            'updated_at' => $menuUpdatedAt,
        ], $tableOptions);

        $this->batchInsert("{{%menu}}", ['id','type','parent_id','name','url','icon','sort','target','is_absolute_url','is_display','created_at','updated_at'
            ],
            [
                ['1','0','0','设置','','fa fa-cogs','0','_blank','0','1','1505570067','1505570067'],
                ['2','0','1','网站设置','/setting/website','','1','_blank','0','1','1505570108','1505570108'],
                ['3','0','1','SMTP设置','setting/smtp','','2','_blank','0','1','1505570155','1505570283'],
                ['4','0','1','自定义设置','setting/custom','','4','_blank','0','1','1505570187','1505570187'],
                ['5','0','0','菜单','','fa fa-th-list','2','_blank','0','1','1505570320','1512380045'],
                ['6','0','5','前台菜单','frontend-menu/index','','0','_blank','0','1','1505570366','1505570366'],
                ['7','0','5','后台菜单','menu/index','','0','_blank','0','1','1505570382','1505570382'],
                ['8','0','0','内容','','fa fa-edit','3','_blank','0','1','1505570558','1512380045'],
                ['9','0','8','文章','article/index','','0','_blank','0','1','1505570610','1505570610'],
                ['10','0','8','分类','category/index','','0','_blank','0','1','1505570638','1505570638'],
                ['11','0','8','评论','comment/index','','0','_blank','0','1','1505570661','1505570707'],
                ['12','0','8','单页','page/index','','0','_blank','0','1','1505570687','1505570687'],
                ['13','0','0','用户','user/index','fa fa-users','4','_blank','0','1','1505570745','1512380045'],
                ['14','0','0','权限管理','','fa fa-th-large','5','_blank','0','1','1505570819','1512380045'],
                ['15','0','14','权限','rbac/permissions','','0','_blank','0','1','1505570862','1505570862'],
                ['16','0','14','角色','rbac/roles','','0','_blank','0','1','1505570882','1505570882'],
                ['17','0','14','管理员','admin-user/index','','0','_blank','0','1','1505570902','1505570902'],
                ['18','0','0','友情链接','friendly-link/index','fa fa-link','6','_blank','0','1','1505570934','1512380045'],
                ['19','0','0','缓存','','fa fa-file','7','_blank','0','1','1505570947','1512380045'],
                ['20','0','19','清除前台','clear/frontend','','0','_blank','0','1','1505570974','1505570974'],
                ['21','0','19','清除后台','clear/backend','','0','_blank','0','1','1505570994','1505570994'],
                ['22','0','0','日志','log/index','fa fa-history','8','_blank','0','1','1505571212','1512380045'],
                ['23','1','0','首页','article/index','','0','_self','0','1','1505636890','1505637024'],
                ['24','1','0','php','{"0":"article/index","cat":"php"}','','0','_self','0','1','1505636915','1505636937'],
                ['25','1','0','java','{"0":"article/index","cat":"java"}','','0','_self','0','1','1505636975','1505636975'],
                ['26','1','0','javascript','{"0":"article/index","cat":"javascript"}','','0','_self','0','1','1505637000','1505637000'],
                ['27','0','0','运营管理','','fa fa-ils','1','_self','0','1','1505637000','1505637000'],
                ['28','0','27','Banner管理','banner/index','','0','_self','0','1','1505637000','1505637000'],
                ['29','0','27','广告管理','ad/index','','0','_self','0','1','1505637000','1505637000'],
            ]
        );


        //table options
        $optionsId = $this->primaryKey();
        $optionsType = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $optionsName = $this->string()->notNull();
        $optionsValue = $this->text()->notNull();
        $optionsInputType = $this->smallInteger()->unsigned()->defaultValue(1)->notNull();
        $optionsAutoload = $this->smallInteger()->unsigned()->defaultValue(1)->notNull();
        $optionTips = $this->string()->defaultValue('')->notNull();
        $optionsSort = $this->integer()->unsigned()->defaultValue(0)->notNull();

        if ($this->db->driverName === 'mysql') {
            $optionsId->unsigned()->comment("options id(auto increment)");
            $optionsType->comment("type (0 system, 1 custom, 2 banner, 3 advertisement");
            $optionsName->comment("identifier");
            $optionsValue->comment("value");
            $optionsInputType->comment("input box type");
            $optionsAutoload->comment("is autoload(0 no, 1 yes");
            $optionTips->comment("tips");
            $optionsSort->comment("order");
        }

        $this->createTable('{{%options}}', [
            'id' => $optionsId,
            'type' => $optionsType,
            'name' => $optionsName,
            'value' => $optionsValue,
            'input_type' => $optionsInputType,
            'autoload' => $optionsAutoload,
            'tips' => $optionTips,
            'sort' => $optionsSort,
        ], $tableOptions);

        $this->batchInsert("{{%options}}", ['type','name','value','input_type','tips','autoload','sort'],
            [
                [
                    '0',
                    'seo_keywords',
                    '飞嗨,cms,yii2,php,feehi cms',
                    '1',
                    '',
                    '0',
                    '0',
                ],
                [
                    '0',
                    'seo_description',
                    'Feehi CMS，最好的cms之一',
                    '1',
                    '',
                    '0',
                    '0',
                ],
                [
                    '0',
                    'website_title',
                    'Feehi CMS',
                    '1',
                    '',
                    '0',
                    '0',
                ],
                [
                    '0',
                    'website_description',
                    'Based on most popular php framework yii2',
                    '1',
                    '',
                    '0',
                    '0',
                ],
                [
                    '0',
                    'website_email',
                    'admin@feehi.com',
                    '1',
                    '',
                    '0',
                    '0',
                ],
                [
                    '0',
                    'website_language',
                    'zh-CN',
                    '1',
                    '',
                    '0',
                    '0',
                ],
                [
                    '0',
                    'website_icp',
                    '粤ICP备15018643号',
                    '1',
                    '',
                    '0',
                    '0',
                ],
                [
                    '0',
                    'website_statics_script',
                    '',
                    '1',
                    '',
                    '0',
                    '0',
                ],
                [
                    '0',
                    'website_status',
                    '1',
                    '1',
                    '',
                    '0',
                    '0',
                ],
                [
                    '0',
                    'website_comment',
                    '1',
                    '1',
                    '',
                    '0',
                    '0',
                ],
                [
                    '0',
                    'website_comment_need_verify',
                    '0',
                    '1',
                    '',
                    '0',
                    '0',
                ],
                [
                    '0',
                    'website_timezone',
                    'Asia/Shanghai',
                    '1',
                    '',
                    '0',
                    '0',
                ],
                [
                    '0',
                    'website_url',
                    $frontendUri,
                    '1',
                    '',
                    '0',
                    '0',
                ],
                [
                    '0',
                    'smtp_host',
                    '',
                    '1',
                    '',
                    '0',
                    '0',
                ],
                [
                    '0',
                    'smtp_username',
                    '',
                    '1',
                    '',
                    '0',
                    '0',
                ],
                [
                    '0',
                    'smtp_password',
                    '',
                    '1',
                    '',
                    '0',
                    '0',
                ],
                [
                    '0',
                    'smtp_port',
                    '',
                    '1',
                    '',
                    '0',
                    '0',
                ],
                [
                    '0',
                    'smtp_encryption',
                    '',
                    '1',
                    '',
                    '0',
                    '0',
                ],
                [
                    '0',
                    'smtp_nickname',
                    '',
                    '1',
                    '',
                    '0',
                    '0',
                ],
                [
                    '1',
                    'weibo',
                    'http://www.weibo.com/feeppp',
                    '1',
                    '新浪微博',
                    '1',
                    '0',
                ],
                [
                    '1',
                    'facebook',
                    'http://www.facebook.com/liufee',
                    '1',
                    'facebook',
                    '1',
                    '0',
                ],
                [
                    '1',
                    'wechat',
                    '飞得更高',
                    '1',
                    '微信',
                    '1',
                    '0',
                ],
                [
                    '1',
                    'qq',
                    '1838889850',
                    '1',
                    'QQ号码',
                    '1',
                    '0',
                ],
                [
                    '1',
                    'email',
                    'admin@feehi.com',
                    '1',
                    '邮箱',
                    '1',
                    '0',
                ],
                ['2', 'index', '[{"sign":"5a251a3013586","img":"\/uploads\/setting\/banner\/5a251a301280d_1.png","target":"_blank","link":"\/view\/11","sort":"3","status":"1","desc":""},{"sign":"5a251a4932a52","img":"\/uploads\/setting\/banner\/5a251a4930fc2_2.jpg","target":"_blank","link":"\/view\/15","sort":"2","status":"1","desc":""},{"sign":"5a251a5690fe9","img":"\/uploads\/setting\/banner\/5a251a568f966_3.jpg","target":"_blank","link":"\/view\/16","sort":"1","status":"1","desc":""}]', '1', '首页banner', '1', '0'],
                ['3', 'sidebar_right_1', '{"ad":"\/uploads\/setting\/ad\/5a292c0fda836_cms.jpg","link":"http://www.feehi.com","target":"_blank","desc":"FeehiCMS","created_at":1512641320,"updated_at":1512647776}', '1', '网站右侧广告位1', '1', '0'],
                ['3', 'sidebar_right_2', '{"ad":"\/uploads\/setting\/ad\/5a291f9236479_22.jpg","link":"","target":"_blank","desc":"\u6700\u597d\u7684\u8fd0\u52a8\u624b\u8868","created_at":1512644498,"updated_at":1512647586}', '1', '网站右侧广告位2', '1', '0'],

            ]
        );

    }

    public function down()
    {
        $this->dropForeignKey('fk_aid', '{{%article_content}}');
        $this->dropForeignKey('fk_article_meta_aid', '{{%article_meta}}');
        $this->dropForeignKey('fk_comment_aid', '{{%comment}}');

        $this->dropIndex('index_key', '{{%article_meta}}');
        $this->dropIndex('index_aid', '{{%article_meta}}');
        $this->dropIndex('index_aid', '{{%comment}}');

        $this->dropTable('{{%options}}');
        $this->dropTable('{{%menu}}');
        $this->dropTable('{{%friendly_link}}');
        $this->dropTable('{{%comment}}');
        $this->dropTable('{{%article_meta}}');
        $this->dropTable('{{%article_content}}');
        $this->dropIndex('index_title', '{{%article}}');
        $this->dropTable('{{%article}}');
        $this->dropTable('{{%category}}');
        $this->dropTable('{{%admin_log}}');
        $this->dropTable('{{%admin_user}}');
        $this->dropTable('{{%user}}');
    }

    public function getParams()
    {
        $rawParams = [];
        if (isset($_SERVER['argv'])) {
            $rawParams = $_SERVER['argv'];
            array_shift($rawParams);
        }

        $params = [];
        foreach ($rawParams as $param) {
            if (preg_match('/^--(\w+)(=(.*))?$/', $param, $matches)) {
                $name = $matches[1];
                $params[$name] = isset($matches[3]) ? $matches[3] : true;
            } else {
                $params[] = $param;
            }
        }
        $return = [];
        foreach ($params as $v){
            if( strpos($v, '=') !== false ) {
                $array = explode('=', $v);
                $return[$array[0]] = $array[1];
            }else{
                $return[$v] = "";
            }
        }
        return $return;
    }
}
