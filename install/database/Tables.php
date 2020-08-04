<?php

namespace install\database;

use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\db\SchemaBuilderTrait;
use yii\rbac\DbManager;
use yii\rbac\ManagerInterface;

class Tables extends BaseObject
{
    use SchemaBuilderTrait;

    /**
     * @var Connection
     */
    public $db;

    /**
     * @var ManagerInterface
     */
    private $authManager;

    private $tableOptions = null;

    private $frontendURL = "";

    public function init()
    {
        parent::init();
        if ($this->isMySQL()) {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->authManager = Yii::$app->getAuthManager();
        if (!$this->authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }
    }

    public function getDb()
    {
        return $this->db;
    }

    public function importDatabase(){
        $tables = $this->getTables();
        foreach ($tables as $tableName => $table){
            /*try {
                $this->db->createCommand()->dropTable($tableName)->execute();
            }catch (\Exception $e){

            }*/
            $displayTableName = preg_replace("/{{%(\w+)}}/isu", $this->db->tablePrefix . "$1", $tableName);
            $this->db->createCommand()->createTable($tableName, $table['columns'], $table['tableOptions'])->execute();
            $this->showMessage(Yii::t('install', "Create table {table} finished", ['table' => $displayTableName]));
            if( isset($table['indexes']) ){
                foreach ($table['indexes'] as $index){
                    $this->db->createCommand()->createIndex($index['name'], $tableName, $index['columns'])->execute();
                    $this->showMessage(Yii::t('install', "Create table {table} index {index} finished", ['table' => $displayTableName, 'index' => $index['name']]));
                }
            }
            if( isset($table['fields']) ){//批量添加
                $this->db->createCommand()->batchInsert($tableName, $table['fields'], $table['rows'])->execute();
                $this->showMessage(Yii::t('install', "Insert table {table} data finished", ['table' => $displayTableName]));
            }else if( isset($table['rows']) ){
                foreach ($table['rows'] as $row){
                    $this->db->createCommand()->insert($tableName, $row)->execute();
                }
                $this->showMessage(Yii::t('install', "Insert table {table} data finished", ['table' => $displayTableName]));
            }
            if(isset($table['rawSQLs'])){
                foreach ($table['rawSQLs'] as $rawSQL){
                    $this->db->createCommand($rawSQL)->execute();
                }
            }

            if( $this->isPgSQL() ){
                $rawSQL = "ALTER SEQUENCE ###TABLE_NAME###_id_seq RESTART WITH ###VALUE###;";
                $value = null;
                if( isset($table['fields']) && isset($table['rows']) && count($table['rows']) > 0 ){//批量
                    $value = $table['rows'][count($table['rows'])-1][0];
                }else if( isset($table['rows']) && count($table['rows']) > 0 ){//逐个
                    $value = $table['rows'][count($table['rows'])-1]['id'];
                }
                if($value !== null && $value > 0){
                    $rawSQL = str_replace(["###TABLE_NAME###", "###VALUE###"], [$displayTableName, 100], $rawSQL);
                    $this->db->createCommand($rawSQL)->execute();
                }
            }
        }
    }

    public function showMessage($msg, $class = '')
    {
        echo str_repeat(" ", 1024 * 64 * 99);
        echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"{$class}\")</script>";
        ob_flush();
        flush();
    }

    public function getTables()
    {
        return [
            "{{%article}}" => $this->tableArticle(),
            "{{%user}}" => $this->tableUser(),
            "{{%admin_user}}" => $this->tableAdminUser(),
            "{{%admin_log}}" => $this->tableAdminLog(),
            "{{%category}}" => $this->tableCategory(),
            "{{%article_content}}" => $this->tableArticleContent(),
            "{{%article_meta}}" => $this->tableArticleMeta(),
            "{{%comment}}" => $this->tableComment(),
            "{{%friendly_link}}" => $this->tableFriendlyLink(),
            "{{%menu}}" => $this->tableMenu(),
            "{{%options}}" => $this->tableOptions(),
            $this->authManager->ruleTable => $this->tableAuthRule(),
            $this->authManager->itemTable => $this->tableAuthItem(),
            $this->authManager->itemChildTable => $this->tableAuthItemChild(),
            $this->authManager->assignmentTable => $this->tableAuthAssignment(),
        ];
    }


    public function tableUser()
    {
        //table user
        $userId = $this->primaryKey();
        $userUsername = $this->string()->notNull()->unique();
        $userAuthKey = $this->string(32)->notNull();
        $userPasswordHash = $this->string()->notNull();
        $userPasswordResetToken = $this->string()->unique();
        $userEmail = $this->string()->notNull()->unique();
        $userAvatar = $this->string()->defaultValue('');
        $userAccessToken = $this->string(42)->defaultValue("")->notNull();
        $userStatus = $this->smallInteger()->notNull()->defaultValue(10);
        $userCreatedAt = $this->integer()->notNull();
        $userUpdatedAt = $this->integer()->notNull();

        if (!$this->isSqlite()) {
            $userId->unsigned()->comment("user id(auto increment)");
            $userUsername->comment("username");
            $userAuthKey->comment("auth key for generate logged in cookie");
            $userPasswordHash->comment("crypt password");
            $userPasswordResetToken->comment("reset password temp token");
            $userEmail->comment("user email");
            $userAvatar->comment("avatar url");
            $userAccessToken->comment("token");
            $userStatus->comment("user status, (normal:10)");
            $userCreatedAt->comment("created at");
            $userUpdatedAt->comment("updated at");
        }

        return [
            "columns" => [
                'id' => $userId,
                'username' => $userUsername,
                'auth_key' => $userAuthKey,
                'password_hash' => $userPasswordHash,
                'password_reset_token' => $userPasswordResetToken,
                'email' => $userEmail,
                'avatar' => $userAvatar,
                "access_token" => $userAccessToken,
                'status' => $userStatus,
                'created_at' => $userCreatedAt,
                'updated_at' => $userUpdatedAt,
            ],
            'tableOptions' => $this->tableOptions,
            'fields' => ['id', 'username', 'auth_key', 'password_hash', 'email', 'status', 'created_at', 'updated_at'],
            'rows' => [],
        ];
    }

    public function tableAdminUser(){
        //table admin_user
        $adminUserId = $this->primaryKey();
        $adminUsername = $this->string()->notNull()->unique();
        $adminUserAuthKey = $this->string(32)->notNull();
        $adminUserPasswordHash =  $this->string()->notNull();
        $adminUserPasswordResetToken = $this->string()->unique();
        $adminUserEmail = $this->string()->notNull()->unique();
        $adminUserAvatar = $this->string()->defaultValue('');
        $adminUserStatus = $this->smallInteger()->notNull()->defaultValue(10);
        $adminUserAccessToken = $this->string(42)->defaultValue("")->notNull();
        $adminUserCreatedAt = $this->integer()->notNull();
        $adminUserUpdatedAt = $this->integer()->notNull();

        if (!$this->isSqlite()) {
            $adminUserId->unsigned()->comment("admin user id(auto increment)");
            $adminUsername->comment("admin username");
            $adminUserAuthKey->comment("admin user auth key for generate logged in cookie");
            $adminUserPasswordHash->comment("admin user crypt password");
            $adminUserPasswordResetToken->comment("admin user reset password temp token");
            $adminUserEmail->comment("admin user email");
            $adminUserAvatar->comment("admin user avatar url");
            $adminUserAccessToken->comment("token");
            $adminUserStatus->comment("admin user status, (normal:10)");
            $adminUserCreatedAt->comment("created at");
            $adminUserUpdatedAt->comment("updated at");
        }

        return[
            'columns' => [
                'id' => $adminUserId,
                'username' => $adminUsername,
                'auth_key' => $adminUserAuthKey,
                'password_hash' => $adminUserPasswordHash,
                'password_reset_token' => $adminUserPasswordResetToken,
                'email' => $adminUserEmail,
                'avatar' => $adminUserAvatar,
                "access_token" => $adminUserAccessToken,
                'status' => $adminUserStatus,
                'created_at' => $adminUserCreatedAt,
                'updated_at' => $adminUserUpdatedAt,
            ],
            'tableOptions' => $this->tableOptions,
            'fields' => ['id','username','auth_key','password_hash','email','avatar','status','created_at','updated_at'],
            'rows' => [
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
            ]
        ];
    }

    public function tableAdminLog()
    {
        $adminLogId = $this->primaryKey();
        $adminLogUserId = $this->integer()->unsigned()->notNull();
        $adminLogRoute = $this->string()->defaultValue('')->notNull();
        $adminLogDescription = $this->text();
        $adminLogCreatedAt = $this->integer()->unsigned()->notNull();

        if (!$this->isSqlite()) {
            $adminLogId->unsigned()->comment("admin log id(auto increment)");
            $adminLogUserId->comment("admin user id");
            $adminLogRoute->comment("admin user operate route, like article/create");
            $adminLogDescription->comment("admin user operate description");
            $adminLogCreatedAt->comment("created at");
        }

        return [
            "columns" => [
                'id' => $adminLogId,
                'user_id' => $adminLogUserId,
                'route' => $adminLogRoute,
                'description' => $adminLogDescription,
                'created_at' => $adminLogCreatedAt
            ],
            'tableOptions' => $this->tableOptions,
            'fields' => ["id", "user_id", "route", "description", "created_at"],
            'rows' => [
                [
                    '1',
                    '1',
                    '/feehi/index',
                    'this is a demo',
                    '1468293965'
                ]
            ]
        ];
    }

    public function tableCategory(){
        $categoryId = $this->primaryKey();
        $categoryParentId = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $categoryName = $this->string()->notNull();
        $categoryAlias = $this->string()->notNull();
        $categorySort = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $categoryTemplate = $this->string()->defaultValue("")->notNull();
        $categoryArticleTemplate = $this->string()->defaultValue("")->notNull();
        $categoryRemark = $this->string()->defaultValue('')->notNull();
        $categoryCreatedAt = $this->integer()->unsigned()->notNull();
        $categoryUpdatedAt = $this->integer()->unsigned()->defaultValue(0)->notNull();

        if (!$this->isSqlite()) {
            $categoryId->unsigned()->comment("category id(auto increment)");
            $categoryParentId->comment("category parent id(an exist category id)");
            $categoryName->comment("category name");
            $categoryAlias->comment("category alias");
            $categorySort->comment("category order");
            $categoryTemplate->comment("category page template path");
            $categoryArticleTemplate->comment("article detail page template path");
            $categoryRemark->comment("category remark info");
            $categoryCreatedAt->comment("created at");
            $categoryUpdatedAt->comment("updated at");
        }


        return[
            'columns' => [
                'id' => $categoryId,
                'parent_id' => $categoryParentId,
                'name' => $categoryName,
                'alias' => $categoryAlias,
                'sort' => $categorySort,
                'remark' => $categoryRemark,
                "template" => $categoryTemplate,
                "article_template" => $categoryArticleTemplate,
                'created_at' => $categoryCreatedAt,
                'updated_at' => $categoryUpdatedAt,
            ],
            'tableOptions' => $this->tableOptions,
            'fields' => ['id','parent_id','name','alias','sort','created_at','updated_at','remark'],
            'rows' => [
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
            ],
        ];
    }

    public function tableArticle(){
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
        $articleTemplate =   $this->string()->defaultValue("")->notNull();
        $articleCreatedAt = $this->integer()->unsigned()->notNull();
        $articleUpdatedAt = $this->integer()->unsigned()->defaultValue(0)->notNull();

        if (!$this->isSqlite()) {
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
            $articleTemplate->comment("article detail page template path");
            $articleCreatedAt->comment("created at");
            $articleUpdatedAt->comment("updated at");
        }

        $articles = require(Yii::getAlias("@console/migrations").'/article.php');

        $rows = [];
        foreach ($articles['article'] as $item) {
            $rows[] = [
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
            ];
        }

        return[
            'columns' => [
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
                "template" => $articleTemplate,
                'created_at' => $articleCreatedAt,
                'updated_at' => $articleUpdatedAt,
            ],
            'tableOptions' => $this->tableOptions,
            'rows' => $rows,
        ];
    }

    public function tableArticleContent(){
        $articleContentId = $this->primaryKey();
        $articleContentArticleId = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $articleContentContent = $this->text()->notNull();

        if (!$this->isSqlite()) {
            $articleContentId->unsigned()->comment("article content id(auto increment)");
            $articleContentArticleId->comment("article id");
            $articleContentContent->comment("article content");
        }

        $articles = require(Yii::getAlias("@console/migrations").'/article.php');

        foreach($articles['article_content'] as $item){
           $rows[] = ['aid'=>$item[1], 'content'=>$item[2]];
        }

        return[
            "columns" => [
                'id' => $articleContentId,
                'aid' => $articleContentArticleId,
                'content' => $articleContentContent,
            ],
            'tableOptions' => $this->tableOptions,
            'rows' => $rows,
        ];
    }

    public function tableArticleMeta()
    {
        $articleMetaId = $this->primaryKey();
        $articleMetaAid = $this->integer()->unsigned()->notNull();
        $articleMetaKey = $this->string()->defaultValue('')->notNull();
        $articleMetaValue = $this->text()->notNull();
        $articleMetaCratedAt = $this->integer()->unsigned()->notNull();

        if (!$this->isSqlite()) {
            $articleMetaId->unsigned()->comment("article meta id(auto increment)");
            $articleMetaAid->comment("article id");
            $articleMetaKey->comment("key");
            $articleMetaValue->comment("value");
            $articleMetaCratedAt->comment("article meta created at");
        }

        return [
            'columns' => [
                'id' => $articleMetaId,
                'aid' => $articleMetaAid,
                'key' => $articleMetaKey,
                'value' => $articleMetaValue,
                'created_at' => $articleMetaCratedAt,
            ],
            'tableOptions' => $this->tableOptions,
            'fields' => ['aid', 'key', 'value', 'created_at'],
            'indexes' => [
                ['name' => "article_meta_index_aid", "columns" => ["aid"]],
                ['name' => "article_meta_index_key", "columns" => ["key"]]
            ],
            'rows' => [
                ['1', 'tag', 'AngularJS', 1507514051],
                ['3', 'tag', 'Facebook', 1507514051],
                ['3', 'tag', 'hack', 1507514051],
                ['3', 'tag', 'php', 1507514051],
                ['5', 'tag', 'gc', 1507514051],
                ['5', 'tag', '垃圾回收', 1507514051],
                ['5', 'tag', 'java', 1507514051],
                ['6', 'tag', 'php7', 1507514051],
                ['6', 'tag', 'php', 1507514051],
                ['6', 'tag', 'wordpress', 1507514051],
                ['8', 'tag', 'spring', 1507514051],
                ['8', 'tag', 'java', 1507514051],
                ['9', 'tag', 'css', 1507514051],
                ['9', 'tag', '重构', 1507514051],
                ['10', 'tag', 'php', 1507514051],
                ['10', 'tag', '分页', 1507514051],
                ['11', 'tag', 'php脚本', 1507514051],
                ['11', 'tag', '下载', 1507514051],
                ['11', 'tag', '代码下载', 1507514051],
                ['12', 'tag', 'java', 1507514051],
                ['12', 'tag', 'Javascript', 1507514051],
                ['12', 'tag', 'Lisp', 1507514051],
                ['12', 'tag', 'php', 1507514051],
                ['12', 'tag', 'Python', 1507514051],
                ['12', 'tag', 'Ruby', 1507514051],
                ['12', 'tag', '编程语言', 1507514051],
                ['12', 'tag', '趣文', 1507514051],
                ['13', 'tag', '缓存', 1507514051],
                ['13', 'tag', '浏览器缓存', 1507514051],
                ['13', 'tag', 'http协议', 1507514051],
                ['14', 'tag', 'javascript', 1507514051],
                ['14', 'tag', '流行', 1507514051],
                ['15', 'tag', 'java', 1507514051],
                ['16', 'tag', 'java', 1507514051],
                ['16', 'tag', '用户界面', 1507514051],
                ['17', 'tag', 'css', 1507514051],
                ['17', 'tag', '样式', 1507514051],
                ['18', 'tag', 'java', 1507514051],
                ['18', 'tag', '入门', 1507514051],
                ['18', 'tag', '编程书籍', 1507514051],
                ['19', 'tag', 'java', 1507514051],
                ['19', 'tag', 'java8', 1507514051],
                ['19', 'tag', '垃圾收集', 1507514051],
                ['20', 'tag', 'jvm', 1507514051],
                ['20', 'tag', 'java', 1507514051],
                ['21', 'tag', 'jvm', 1507514051],
                ['21', 'tag', 'java', 1507514051],
                ['22', 'tag', 'java', 1507514051],
                ['22', 'tag', 'java集合', 1507514051],
            ],
        ];

    }

    public function tableComment()
    {
        $commentId = $this->primaryKey();
        $commentArticleId = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $commentUserId = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $commentAdminUserId = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $commentReplyTo = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $commentNickname = $this->string()->defaultValue('游客')->notNull();
        $commentEmail = $this->string()->defaultValue('')->notNull();
        $commentWebsiteUrl = $this->string()->defaultValue('')->notNull();
        $commentContent = $this->string()->notNull();
        $commentIp = $this->string()->defaultValue('')->notNull();
        $commentStatus = $this->smallInteger()->unsigned()->defaultValue(0)->notNull();
        $commentCreatedAt = $this->integer()->unsigned()->notNull();
        $commentUpdatedAt = $this->integer()->unsigned()->defaultValue(0)->notNull();

        if (!$this->isSqlite()) {
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

        return [
            'columns' => [
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
            ],
            'tableOptions' => $this->tableOptions,
            'indexes' => [
                ['name' => 'comment_index_aid', 'columns' => ['aid']]
            ],
            'fields' => ['id', 'aid', 'uid', 'reply_to', 'nickname', 'email', 'website_url', 'content', 'ip', 'status', 'created_at', 'updated_at'],
            'rows' => [
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
        ];
    }

    public function tableFriendlyLink(){
        $friendlyLinkId = $this->primaryKey();
        $friendlyLinkName = $this->string()->notNull();
        $friendlyLinkImage = $this->string()->defaultValue('')->notNull();
        $friendlyLinkURL = $this->string()->defaultValue('')->notNull();
        $friendlyLinkTarget = $this->string()->defaultValue('_blank')->notNull();
        $friendlyLinkSort = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $friendlyLinkStatus = $this->smallInteger()->unsigned()->defaultValue(0)->notNull();
        $friendlyLinkCreatedAt = $this->integer()->unsigned()->notNull();
        $friendlyLinkIdUpdatedAt = $this->integer()->unsigned()->defaultValue(0)->notNull();

        if (!$this->isSqlite()) {
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

        return [
            'columns' => [
                'id' => $friendlyLinkId,
                'name' => $friendlyLinkName,
                'image' => $friendlyLinkImage,
                'url' => $friendlyLinkURL,
                'target' => $friendlyLinkTarget,
                'sort' => $friendlyLinkSort,
                'status' => $friendlyLinkStatus,
                'created_at' => $friendlyLinkCreatedAt,
                'updated_at' => $friendlyLinkIdUpdatedAt,
            ],
            'tableOptions' => $this->tableOptions,
            'fields' => ['id','name','image','url','target','sort','status','created_at','updated_at'],
            'rows' => [
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
        ];
    }

    public function tableMenu()
    {
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

        if (!$this->isSqlite()) {
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

        return [
            'columns' => [
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
            ],
            'tableOptions' => $this->tableOptions,
            'fields' => ['id', 'type', 'parent_id', 'name', 'url', 'icon', 'sort', 'target', 'is_absolute_url', 'is_display', 'created_at', 'updated_at'],
            'rows' => [
                ['1', '0', '0', '设置', '', 'fa fa-cogs', '0', '_blank', '0', '1', '1505570067', '1505570067'],
                ['2', '0', '1', '网站设置', '/setting/website', '', '1', '_blank', '0', '1', '1505570108', '1505570108'],
                ['3', '0', '1', 'SMTP设置', 'setting/smtp', '', '2', '_blank', '0', '1', '1505570155', '1505570283'],
                ['4', '0', '1', '自定义设置', 'setting/custom', '', '4', '_blank', '0', '1', '1505570187', '1505570187'],
                ['5', '0', '0', '菜单', '', 'fa fa-th-list', '2', '_blank', '0', '1', '1505570320', '1512380045'],
                ['6', '0', '5', '前台菜单', 'frontend-menu/index', '', '0', '_blank', '0', '1', '1505570366', '1505570366'],
                ['7', '0', '5', '后台菜单', 'menu/index', '', '0', '_blank', '0', '1', '1505570382', '1505570382'],
                ['8', '0', '0', '内容', '', 'fa fa-edit', '3', '_blank', '0', '1', '1505570558', '1512380045'],
                ['9', '0', '8', '文章', 'article/index', '', '0', '_blank', '0', '1', '1505570610', '1505570610'],
                ['10', '0', '8', '分类', 'category/index', '', '0', '_blank', '0', '1', '1505570638', '1505570638'],
                ['11', '0', '8', '评论', 'comment/index', '', '0', '_blank', '0', '1', '1505570661', '1505570707'],
                ['12', '0', '8', '单页', 'page/index', '', '0', '_blank', '0', '1', '1505570687', '1505570687'],
                ['13', '0', '0', '用户', 'user/index', 'fa fa-users', '4', '_blank', '0', '1', '1505570745', '1512380045'],
                ['14', '0', '0', '权限管理', '', 'fa fa-th-large', '5', '_blank', '0', '1', '1505570819', '1512380045'],
                ['15', '0', '14', '权限', 'rbac/permissions', '', '0', '_blank', '0', '1', '1505570862', '1505570862'],
                ['16', '0', '14', '角色', 'rbac/roles', '', '0', '_blank', '0', '1', '1505570882', '1505570882'],
                ['17', '0', '14', '管理员', 'admin-user/index', '', '0', '_blank', '0', '1', '1505570902', '1505570902'],
                ['18', '0', '0', '友情链接', 'friendly-link/index', 'fa fa-link', '6', '_blank', '0', '1', '1505570934', '1512380045'],
                ['19', '0', '0', '缓存', '', 'fa fa-file', '7', '_blank', '0', '1', '1505570947', '1512380045'],
                ['20', '0', '19', '清除前台', 'clear/frontend', '', '0', '_blank', '0', '1', '1505570974', '1505570974'],
                ['21', '0', '19', '清除后台', 'clear/backend', '', '0', '_blank', '0', '1', '1505570994', '1505570994'],
                ['22', '0', '0', '日志', 'log/index', 'fa fa-history', '8', '_blank', '0', '1', '1505571212', '1512380045'],
                ['23', '1', '0', '首页', 'article/index', '', '0', '_self', '0', '1', '1505636890', '1505637024'],
                ['24', '1', '0', 'php', '{"0":"article/index","cat":"php"}', '', '0', '_self', '0', '1', '1505636915', '1505636937'],
                ['25', '1', '0', 'java', '{"0":"article/index","cat":"java"}', '', '0', '_self', '0', '1', '1505636975', '1505636975'],
                ['26', '1', '0', 'javascript', '{"0":"article/index","cat":"javascript"}', '', '0', '_self', '0', '1', '1505637000', '1505637000'],
                ['27', '0', '0', '运营管理', '', 'fa fa-ils', '1', '_self', '0', '1', '1505637000', '1505637000'],
                ['28', '0', '27', 'Banner管理', 'banner/index', '', '0', '_self', '0', '1', '1505637000', '1505637000'],
                ['29', '0', '27', '广告管理', 'ad/index', '', '0', '_self', '0', '1', '1505637000', '1505637000'],
            ],
        ];
    }

    public function tableOptions()
    {
        $optionsId = $this->primaryKey();
        $optionsType = $this->integer()->unsigned()->defaultValue(0)->notNull();
        $optionsName = $this->string()->notNull();
        $optionsValue = $this->text()->notNull();
        $optionsInputType = $this->smallInteger()->unsigned()->defaultValue(1)->notNull();
        $optionsAutoload = $this->smallInteger()->unsigned()->defaultValue(1)->notNull();
        $optionTips = $this->string()->defaultValue('')->notNull();
        $optionsSort = $this->integer()->unsigned()->defaultValue(0)->notNull();

        if (!$this->isSqlite()) {
            $optionsId->unsigned()->comment("options id(auto increment)");
            $optionsType->comment("type (0 system, 1 custom, 2 banner, 3 advertisement");
            $optionsName->comment("identifier");
            $optionsValue->comment("value");
            $optionsInputType->comment("input box type");
            $optionsAutoload->comment("is autoload(0 no, 1 yes");
            $optionTips->comment("tips");
            $optionsSort->comment("order");
        }

        return [
            'columns' => [
                'id' => $optionsId,
                'type' => $optionsType,
                'name' => $optionsName,
                'value' => $optionsValue,
                'input_type' => $optionsInputType,
                'autoload' => $optionsAutoload,
                'tips' => $optionTips,
                'sort' => $optionsSort,
            ],
            'tableOptions' => $this->tableOptions,
            'fields' => ['type', 'name', 'value', 'input_type', 'tips', 'autoload', 'sort'],
            'rows' => [
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
                    $this->frontendURL,
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

            ],
        ];
    }

    public function tableAuthRule()
    {
        return [
            'columns' => [
                'name' => $this->string(64)->notNull(),
                'data' => $this->binary(),
                'created_at' => $this->integer(),
                'updated_at' => $this->integer(),
                'PRIMARY KEY ([[name]])',
            ],
            'tableOptions' => $this->tableOptions
        ];
    }

    public function tableAuthItem()
    {
        $rows = [
            ['/ad/create:GET', 2, '创建(查看)', NULL, 's:75:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"622","category":"\u5e7f\u544a"}";', 1543937188, 1543937188],
            ['/ad/create:POST', 2, '创建(确定)', NULL, 's:75:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"623","category":"\u5e7f\u544a"}";', 1543937188, 1543937188],
            ['/ad/delete:POST', 2, '删除', NULL, 's:75:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"626","category":"\u5e7f\u544a"}";', 1543937188, 1543937188],
            ['/ad/index:GET', 2, '列表', NULL, 's:75:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"620","category":"\u5e7f\u544a"}";', 1543937188, 1543937188],
            ['/ad/sort:POST', 2, '排序', NULL, 's:75:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"627","category":"\u5e7f\u544a"}";', 1543937188, 1543937188],
            ['/ad/update:GET', 2, '修改(查看)', NULL, 's:75:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"624","category":"\u5e7f\u544a"}";', 1543937188, 1543937188],
            ['/ad/update:POST', 2, '修改(确定)', NULL, 's:75:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"625","category":"\u5e7f\u544a"}";', 1543937188, 1543937188],
            ['/ad/view-layer:GET', 2, '查看', NULL, 's:75:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"621","category":"\u5e7f\u544a"}";', 1543937188, 1543937188],
            ['/admin-user/create:GET', 2, '创建(查看)', NULL, 's:69:"{"group":"\u6743\u9650","sort":"524","category":"\u7ba1\u7406\u5458"}";', 1543937188, 1543937188],
            ['/admin-user/create:POST', 2, '创建(确定)', NULL, 's:69:"{"group":"\u6743\u9650","sort":"525","category":"\u7ba1\u7406\u5458"}";', 1543937188, 1543937188],
            ['/admin-user/delete:POST', 2, '删除', NULL, 's:69:"{"group":"\u6743\u9650","sort":"522","category":"\u7ba1\u7406\u5458"}";', 1543937188, 1543937188],
            ['/admin-user/index:GET', 2, '列表', NULL, 's:69:"{"group":"\u6743\u9650","sort":"520","category":"\u7ba1\u7406\u5458"}";', 1543937188, 1543937188],
            ['/admin-user/sort:POST', 2, '排序', NULL, 's:69:"{"group":"\u6743\u9650","sort":"523","category":"\u7ba1\u7406\u5458"}";', 1543937188, 1543937188],
            ['/admin-user/update:GET', 2, '修改(查看)', NULL, 's:69:"{"group":"\u6743\u9650","sort":"526","category":"\u7ba1\u7406\u5458"}";', 1543937188, 1543937188],
            ['/admin-user/update:POST', 2, '修改(确定)', NULL, 's:69:"{"group":"\u6743\u9650","sort":"527","category":"\u7ba1\u7406\u5458"}";', 1543937188, 1543937188],
            ['/admin-user/view-layer:GET', 2, '查看', NULL, 's:69:"{"group":"\u6743\u9650","sort":"521","category":"\u7ba1\u7406\u5458"}";', 1543937188, 1543937188],
            ['/article/create:GET', 2, '创建(查看)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"302","category":"\u6587\u7ae0"}";', 1543937188, 1543937188],
            ['/article/create:POST', 2, '创建(确定)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"303","category":"\u6587\u7ae0"}";', 1543937188, 1543937188],
            ['/article/delete:POST', 2, '删除', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"306","category":"\u6587\u7ae0"}";', 1543937188, 1543937188],
            ['/article/index:GET', 2, '列表', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"300","category":"\u6587\u7ae0"}";', 1543937188, 1543937188],
            ['/article/sort:POST', 2, '排序', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"307","category":"\u6587\u7ae0"}";', 1543937188, 1543937188],
            ['/article/update:GET', 2, '修改(查看)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"304","category":"\u6587\u7ae0"}";', 1543937188, 1543937188],
            ['/article/update:POST', 2, '修改(确定)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"305","category":"\u6587\u7ae0"}";', 1543937188, 1543937188],
            ['/article/view-layer:GET', 2, '查看', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"301","category":"\u6587\u7ae0"}";', 1543937188, 1543937188],
            ['/banner/banner-create:GET', 2, '创建(查看)', NULL, 's:69:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"611","category":"banner"}";', 1543937188, 1543937188],
            ['/banner/banner-create:POST', 2, '创建(确定)', NULL, 's:69:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"612","category":"banner"}";', 1543937188, 1543937188],
            ['/banner/banner-delete:POST', 2, '删除(确定)', NULL, 's:69:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"617","category":"banner"}";', 1543937188, 1543937188],
            ['/banner/banner-sort:POST', 2, '排序', NULL, 's:69:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"616","category":"banner"}";', 1543937188, 1543937188],
            ['/banner/banner-update:GET', 2, '修改(查看)', NULL, 's:69:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"614","category":"banner"}";', 1543937188, 1543937188],
            ['/banner/banner-update:POST', 2, '修改(确定)', NULL, 's:69:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"615","category":"banner"}";', 1543937188, 1543937188],
            ['/banner/banner-view-layer:GET', 2, '查看', NULL, 's:69:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"613","category":"banner"}";', 1543937188, 1543937188],
            ['/banner/banners:GET', 2, '列表', NULL, 's:69:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"610","category":"banner"}";', 1543937188, 1543937188],
            ['/banner/create:GET', 2, '创建(查看)', NULL, 's:81:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"601","category":"banner\u7c7b\u578b"}";', 1543937188, 1543937188],
            ['/banner/create:POST', 2, '创建(确定)', NULL, 's:81:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"602","category":"banner\u7c7b\u578b"}";', 1543937188, 1543937188],
            ['/banner/delete:POST', 2, '删除', NULL, 's:81:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"605","category":"banner\u7c7b\u578b"}";', 1543937188, 1543937188],
            ['/banner/index:GET', 2, '列表', NULL, 's:81:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"600","category":"banner\u7c7b\u578b"}";', 1543937188, 1543937188],
            ['/banner/update:GET', 2, '修改(查看)', NULL, 's:81:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"603","category":"banner\u7c7b\u578b"}";', 1543937188, 1543937188],
            ['/banner/update:POST', 2, '修改(确定)', NULL, 's:81:"{"group":"\u8fd0\u8425\u7ba1\u7406","sort":"604","category":"banner\u7c7b\u578b"}";', 1543937188, 1543937188],
            ['/category/create:GET', 2, '创建(查看)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"312","category":"\u5206\u7c7b"}";', 1543937188, 1543937188],
            ['/category/create:POST', 2, '创建(确定)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"313","category":"\u5206\u7c7b"}";', 1543937188, 1543937188],
            ['/category/delete:POST', 2, '删除', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"316","category":"\u5206\u7c7b"}";', 1543937188, 1543937188],
            ['/category/index:GET', 2, '列表', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"310","category":"\u5206\u7c7b"}";', 1543937188, 1543937188],
            ['/category/sort:POST', 2, '排序', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"317","category":"\u5206\u7c7b"}";', 1543937188, 1543937188],
            ['/category/update:GET', 2, '修改(查看)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"314","category":"\u5206\u7c7b"}";', 1543937188, 1543937188],
            ['/category/update:POST', 2, '修改(确定)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"315","category":"\u5206\u7c7b"}";', 1543937188, 1543937188],
            ['/category/view-layer:GET', 2, '查看', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"311","category":"\u5206\u7c7b"}";', 1543937188, 1543937188],
            ['/clear/backend:GET', 2, '清除后台缓存', NULL, 's:63:"{"group":"\u5176\u4ed6","sort":"720","category":"\u7f13\u5b58"}";', 1543937188, 1543937188],
            ['/clear/frontend:GET', 2, '清除前台缓存', NULL, 's:63:"{"group":"\u5176\u4ed6","sort":"721","category":"\u7f13\u5b58"}";', 1543937188, 1543937188],
            ['/comment/delete:POST', 2, '删除', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"324","category":"\u8bc4\u8bba"}";', 1543937188, 1543937188],
            ['/comment/index:GET', 2, '列表', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"320","category":"\u8bc4\u8bba"}";', 1543937188, 1543937188],
            ['/comment/update:GET', 2, '修改(查看)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"322","category":"\u8bc4\u8bba"}";', 1543937188, 1543937188],
            ['/comment/update:POST', 2, '修改(确定)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"323","category":"\u8bc4\u8bba"}";', 1543937188, 1543937188],
            ['/comment/view-layer:GET', 2, '查看', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"321","category":"\u8bc4\u8bba"}";', 1543937188, 1543937188],
            ['/friendly-link/create:GET', 2, '创建(查看)', NULL, 's:75:"{"group":"\u5176\u4ed6","sort":"702","category":"\u53cb\u60c5\u94fe\u63a5"}";', 1543937188, 1543937188],
            ['/friendly-link/create:POST', 2, '创建(确定)', NULL, 's:75:"{"group":"\u5176\u4ed6","sort":"703","category":"\u53cb\u60c5\u94fe\u63a5"}";', 1543937188, 1543937188],
            ['/friendly-link/delete:POST', 2, '删除', NULL, 's:75:"{"group":"\u5176\u4ed6","sort":"706","category":"\u53cb\u60c5\u94fe\u63a5"}";', 1543937188, 1543937188],
            ['/friendly-link/index:GET', 2, '列表', NULL, 's:75:"{"group":"\u5176\u4ed6","sort":"700","category":"\u53cb\u60c5\u94fe\u63a5"}";', 1543937188, 1543937188],
            ['/friendly-link/sort:POST', 2, '排序', NULL, 's:75:"{"group":"\u5176\u4ed6","sort":"707","category":"\u53cb\u60c5\u94fe\u63a5"}";', 1543937188, 1543937188],
            ['/friendly-link/update:GET', 2, '修改(查看)', NULL, 's:75:"{"group":"\u5176\u4ed6","sort":"704","category":"\u53cb\u60c5\u94fe\u63a5"}";', 1543937188, 1543937188],
            ['/friendly-link/update:POST', 2, '修改(确定)', NULL, 's:75:"{"group":"\u5176\u4ed6","sort":"705","category":"\u53cb\u60c5\u94fe\u63a5"}";', 1543937188, 1543937188],
            ['/friendly-link/view-layer:GET', 2, '查看', NULL, 's:75:"{"group":"\u5176\u4ed6","sort":"701","category":"\u53cb\u60c5\u94fe\u63a5"}";', 1543937188, 1543937188],
            ['/frontend-menu/create:GET', 2, '创建(查看)', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"202","category":"\u524d\u53f0"}";', 1543937188, 1543937188],
            ['/frontend-menu/create:POST', 2, '创建(确定)', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"203","category":"\u524d\u53f0"}";', 1543937188, 1543937188],
            ['/frontend-menu/delete:POST', 2, '删除', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"206","category":"\u524d\u53f0"}";', 1543937188, 1543937188],
            ['/frontend-menu/index:GET', 2, '列表', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"200","category":"\u524d\u53f0"}";', 1543937188, 1543937188],
            ['/frontend-menu/sort:POST', 2, '排序', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"207","category":"\u524d\u53f0"}";', 1543937188, 1543937188],
            ['/frontend-menu/update:GET', 2, '修改(查看)', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"204","category":"\u524d\u53f0"}";', 1543937188, 1543937188],
            ['/frontend-menu/update:POST', 2, '修改(确定)', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"205","category":"\u524d\u53f0"}";', 1543937188, 1543937188],
            ['/frontend-menu/view-layer:GET', 2, '查看', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"201","category":"\u524d\u53f0"}";', 1543937188, 1543937188],
            ['/log/delete:POST', 2, '删除', NULL, 's:63:"{"group":"\u5176\u4ed6","sort":"723","category":"\u65e5\u5fd7"}";', 1543937188, 1543937188],
            ['/log/index:GET', 2, '列表', NULL, 's:63:"{"group":"\u5176\u4ed6","sort":"711","category":"\u65e5\u5fd7"}";', 1543937188, 1543937188],
            ['/log/view-layer:GET', 2, '查看', NULL, 's:63:"{"group":"\u5176\u4ed6","sort":"712","category":"\u65e5\u5fd7"}";', 1543937188, 1543937188],
            ['/menu/create:GET', 2, '创建(查看)', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"212","category":"\u540e\u53f0"}";', 1543937188, 1543937188],
            ['/menu/create:POST', 2, '创建(确定)', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"213","category":"\u540e\u53f0"}";', 1543937188, 1543937188],
            ['/menu/delete:POST', 2, '删除', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"216","category":"\u540e\u53f0"}";', 1543937188, 1543937188],
            ['/menu/index:GET', 2, '列表', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"210","category":"\u540e\u53f0"}";', 1543937188, 1543937188],
            ['/menu/sort:POST', 2, '排序', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"217","category":"\u540e\u53f0"}";', 1543937188, 1543937188],
            ['/menu/update:GET', 2, '修改(查看)', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"214","category":"\u540e\u53f0"}";', 1543937188, 1543937188],
            ['/menu/update:POST', 2, '修改(确定)', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"215","category":"\u540e\u53f0"}";', 1543937188, 1543937188],
            ['/menu/view-layer:GET', 2, '查看', NULL, 's:63:"{"group":"\u83dc\u5355","sort":"211","category":"\u540e\u53f0"}";', 1543937188, 1543937188],
            ['/page/create:GET', 2, '创建(查看)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"332","category":"\u5355\u9875"}";', 1543937188, 1543937188],
            ['/page/create:POST', 2, '创建(确定)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"333","category":"\u5355\u9875"}";', 1543937188, 1543937188],
            ['/page/delete:POST', 2, '删除', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"336","category":"\u5355\u9875"}";', 1543937188, 1543937188],
            ['/page/index:GET', 2, '列表', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"330","category":"\u5355\u9875"}";', 1543937188, 1543937188],
            ['/page/sort:POST', 2, '排序', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"337","category":"\u5355\u9875"}";', 1543937188, 1543937188],
            ['/page/update:GET', 2, '修改(查看)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"334","category":"\u5355\u9875"}";', 1543937188, 1543937188],
            ['/page/update:POST', 2, '修改(确定)', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"335","category":"\u5355\u9875"}";', 1543937188, 1543937188],
            ['/page/view-layer:GET', 2, '查看', NULL, 's:63:"{"group":"\u5185\u5bb9","sort":"331","category":"\u5355\u9875"}";', 1543937188, 1543937188],
            ['/rbac/permission-create:GET', 2, '创建(查看)', NULL, 's:63:"{"group":"\u6743\u9650","sort":"502","category":"\u89c4\u5219"}";', 1543937187, 1543937187],
            ['/rbac/permission-create:POST', 2, '创建(确定)', NULL, 's:63:"{"group":"\u6743\u9650","sort":"503","category":"\u89c4\u5219"}";', 1543937187, 1543937187],
            ['/rbac/permission-delete:POST', 2, '删除', NULL, 's:63:"{"group":"\u6743\u9650","sort":"507","category":"\u89c4\u5219"}";', 1543937187, 1543937187],
            ['/rbac/permission-sort:POST', 2, '排序', NULL, 's:63:"{"group":"\u6743\u9650","sort":"501","category":"\u89c4\u5219"}";', 1543937187, 1543937187],
            ['/rbac/permission-update:GET', 2, '修改(查看)', NULL, 's:63:"{"group":"\u6743\u9650","sort":"504","category":"\u89c4\u5219"}";', 1543937187, 1543937187],
            ['/rbac/permission-update:POST', 2, '修改(确定)', NULL, 's:63:"{"group":"\u6743\u9650","sort":"505","category":"\u89c4\u5219"}";', 1543937187, 1543937187],
            ['/rbac/permission-view-layer:GET', 2, '查看', NULL, 's:63:"{"group":"\u6743\u9650","sort":"506","category":"\u89c4\u5219"}";', 1543937187, 1543937187],
            ['/rbac/permissions:GET', 2, '列表', NULL, 's:63:"{"group":"\u6743\u9650","sort":"500","category":"\u89c4\u5219"}";', 1543937187, 1543937187],
            ['/rbac/role-create:GET', 2, '创建(查看)', NULL, 's:63:"{"group":"\u6743\u9650","sort":"511","category":"\u89d2\u8272"}";', 1543937187, 1543937187],
            ['/rbac/role-create:POST', 2, '创建(确定)', NULL, 's:63:"{"group":"\u6743\u9650","sort":"512","category":"\u89d2\u8272"}";', 1543937187, 1543937187],
            ['/rbac/role-delete:POST', 2, '删除', NULL, 's:63:"{"group":"\u6743\u9650","sort":"517","category":"\u89d2\u8272"}";', 1543937188, 1543937188],
            ['/rbac/role-update:GET', 2, '修改(查看)', NULL, 's:63:"{"group":"\u6743\u9650","sort":"513","category":"\u89d2\u8272"}";', 1543937187, 1543937187],
            ['/rbac/role-update:POST', 2, '修改(确定)', NULL, 's:63:"{"group":"\u6743\u9650","sort":"514","category":"\u89d2\u8272"}";', 1543937187, 1543937187],
            ['/rbac/role-view-layer:GET', 2, '查看', NULL, 's:63:"{"group":"\u6743\u9650","sort":"515","category":"\u89d2\u8272"}";', 1543937187, 1543937187],
            ['/rbac/role-sort:POST', 2, '排序', NULL, 's:63:"{"group":"\u6743\u9650","sort":"516","category":"\u89d2\u8272"}";', 1543937188, 1543937188],
            ['/rbac/roles:GET', 2, '列表', NULL, 's:63:"{"group":"\u6743\u9650","sort":"510","category":"\u89d2\u8272"}";', 1543937187, 1543937187],
            ['/setting/custom-create:GET', 2, '自定义设置创建(查看)', NULL, 's:81:"{"group":"\u8bbe\u7f6e","sort":"133","category":"\u81ea\u5b9a\u4e49\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/custom-create:POST', 2, '自定义设置创建(确定)', NULL, 's:81:"{"group":"\u8bbe\u7f6e","sort":"134","category":"\u81ea\u5b9a\u4e49\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/custom-delete:POST', 2, '删除', NULL, 's:81:"{"group":"\u8bbe\u7f6e","sort":"132","category":"\u81ea\u5b9a\u4e49\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/custom-update:GET', 2, '自定义设置修改(查看)', NULL, 's:81:"{"group":"\u8bbe\u7f6e","sort":"135","category":"\u81ea\u5b9a\u4e49\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/custom-update:POST', 2, '自定义设置修改(确定)', NULL, 's:81:"{"group":"\u8bbe\u7f6e","sort":"136","category":"\u81ea\u5b9a\u4e49\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/custom:GET', 2, '修改(查看)', NULL, 's:81:"{"group":"\u8bbe\u7f6e","sort":"130","category":"\u81ea\u5b9a\u4e49\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/custom:POST', 2, '修改(确定)', NULL, 's:81:"{"group":"\u8bbe\u7f6e","sort":"131","category":"\u81ea\u5b9a\u4e49\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/smtp:GET', 2, '修改(查看)', NULL, 's:67:"{"group":"\u8bbe\u7f6e","sort":"110","category":"smtp\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/smtp:POST', 2, '修改(确定)', NULL, 's:67:"{"group":"\u8bbe\u7f6e","sort":"111","category":"smtp\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/test-smtp:POST', 2, '测试stmp设置', NULL, 's:67:"{"group":"\u8bbe\u7f6e","sort":"112","category":"smtp\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/website:GET', 2, '网站设置(查看)', NULL, 's:75:"{"group":"\u8bbe\u7f6e","sort":"100","category":"\u7f51\u7ad9\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/setting/website:POST', 2, '网站设置(确定)', NULL, 's:75:"{"group":"\u8bbe\u7f6e","sort":"101","category":"\u7f51\u7ad9\u8bbe\u7f6e"}";', 1543937188, 1543937188],
            ['/user/create:GET', 2, '创建(查看)', NULL, 's:75:"{"group":"\u7528\u6237","sort":"402","category":"\u524d\u53f0\u7528\u6237"}";', 1543937187, 1543937187],
            ['/user/create:POST', 2, '创建(确定)', NULL, 's:75:"{"group":"\u7528\u6237","sort":"403","category":"\u524d\u53f0\u7528\u6237"}";', 1543937187, 1543937187],
            ['/user/delete:POST', 2, '删除', NULL, 's:75:"{"group":"\u7528\u6237","sort":"406","category":"\u524d\u53f0\u7528\u6237"}";', 1543937187, 1543937187],
            ['/user/index:GET', 2, '列表', NULL, 's:75:"{"group":"\u7528\u6237","sort":"400","category":"\u524d\u53f0\u7528\u6237"}";', 1543937187, 1543937187],
            ['/user/sort:POST', 2, '排序', NULL, 's:75:"{"group":"\u7528\u6237","sort":"407","category":"\u524d\u53f0\u7528\u6237"}";', 1543937187, 1543937187],
            ['/user/update:GET', 2, '修改(查看)', NULL, 's:75:"{"group":"\u7528\u6237","sort":"404","category":"\u524d\u53f0\u7528\u6237"}";', 1543937187, 1543937187],
            ['/user/update:POST', 2, '修改(确定)', NULL, 's:75:"{"group":"\u7528\u6237","sort":"405","category":"\u524d\u53f0\u7528\u6237"}";', 1543937187, 1543937187],
            ['/user/view-layer:GET', 2, '查看', NULL, 's:75:"{"group":"\u7528\u6237","sort":"401","category":"\u524d\u53f0\u7528\u6237"}";', 1543937187, 1543937187],
        ];

        $rawSQLs = [];
        if ($this->isMSSQL()) {
            $rawSQLs[] = "CREATE TRIGGER dbo.trigger_auth_item_child
            ON dbo.{$this->authManager->itemTable}
            INSTEAD OF DELETE, UPDATE
            AS
            DECLARE @old_name VARCHAR (64) = (SELECT name FROM deleted)
            DECLARE @new_name VARCHAR (64) = (SELECT name FROM inserted)
            BEGIN
            IF COLUMNS_UPDATED() > 0
                BEGIN
                    IF @old_name <> @new_name
                    BEGIN
                        ALTER TABLE {$this->authManager->itemChildTable} NOCHECK CONSTRAINT FK__auth_item__child;
                        UPDATE {$this->authManager->itemChildTable} SET child = @new_name WHERE child = @old_name;
                    END
                UPDATE {$this->authManager->itemTable}
                SET name = (SELECT name FROM inserted),
                type = (SELECT type FROM inserted),
                description = (SELECT description FROM inserted),
                rule_name = (SELECT rule_name FROM inserted),
                data = (SELECT data FROM inserted),
                created_at = (SELECT created_at FROM inserted),
                updated_at = (SELECT updated_at FROM inserted)
                WHERE name IN (SELECT name FROM deleted)
                IF @old_name <> @new_name
                    BEGIN
                        ALTER TABLE {$this->authManager->itemChildTable} CHECK CONSTRAINT FK__auth_item__child;
                    END
                END
                ELSE
                    BEGIN
                        DELETE FROM dbo.{$this->authManager->itemChildTable} WHERE parent IN (SELECT name FROM deleted) OR child IN (SELECT name FROM deleted);
                        DELETE FROM dbo.{$this->authManager->itemTable} WHERE name IN (SELECT name FROM deleted);
                    END
            END;";
        }

        return [
            'columns' => [
                'name' => $this->string(64)->notNull(),
                'type' => $this->smallInteger()->notNull(),
                'description' => $this->text(),
                'rule_name' => $this->string(64),
                'data' => $this->text(),
                'created_at' => $this->integer(),
                'updated_at' => $this->integer(),
                'PRIMARY KEY ([[name]])',
                'FOREIGN KEY ([[rule_name]]) REFERENCES ' . $this->authManager->ruleTable . ' ([[name]])'.
                $this->buildFkClause('ON DELETE SET NULL', 'ON UPDATE CASCADE')
            ],
            'tableOptions' => $this->tableOptions,
            'indexes' => [
                ['name'=>'idx-auth_item-type', 'columns'=>['type']]
            ],
            'rawSQLs' => $rawSQLs,
            'fields' => ['name', 'type', 'description', 'rule_name', 'data', 'created_at', 'updated_at'],
            'rows' => $rows,
        ];
    }

    public function tableAuthItemChild()
    {
        return [
            'columns' => [
                'parent' => $this->string(64)->notNull(),
                'child' => $this->string(64)->notNull(),
                'PRIMARY KEY ([[parent]], [[child]])',
                'FOREIGN KEY ([[parent]]) REFERENCES ' . $this->authManager->itemTable . ' ([[name]])'.
                $this->buildFkClause('ON DELETE CASCADE', 'ON UPDATE CASCADE'),
                'FOREIGN KEY ([[child]]) REFERENCES ' . $this->authManager->itemTable . ' ([[name]])'.
                $this->buildFkClause('ON DELETE CASCADE', 'ON UPDATE CASCADE'),
            ],
            'tableOptions' => $this->tableOptions,
        ];
    }

    public function tableAuthAssignment()
    {
        return [
            'columns' =>  [
                'item_name' => $this->string(64)->notNull(),
                'user_id' => $this->string(64)->notNull(),
                'created_at' => $this->integer(),
                'PRIMARY KEY ([[item_name]], [[user_id]])',
                'FOREIGN KEY ([[item_name]]) REFERENCES ' . $this->authManager->itemTable . ' ([[name]])' .
                $this->buildFkClause('ON DELETE CASCADE', 'ON UPDATE CASCADE'),
            ],
            'tableOptions' => $this->tableOptions,
        ];
    }

    protected function buildFkClause($delete = '', $update = '')
    {
        if ($this->isMSSQL()) {
            return '';
        }

        if ($this->isOracle()) {
            return ' ' . $delete;
        }

        return implode(' ', ['', $delete, $update]);
    }

    protected function isMSSQL()
    {
        return $this->db->driverName === 'mssql' || $this->db->driverName === 'sqlsrv' || $this->db->driverName === 'dblib';
    }

    protected function isMySQL()
    {
        return $this->db->driverName === 'mysql';
    }

    protected function isOracle()
    {
        return $this->db->driverName === 'oci';
    }

    protected function isPgSQL()
    {
        return $this->db->driverName === 'pgsql';
    }

    protected function isSqlite()
    {
        return $this->db->driverName === 'sqlite';
    }

}