<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use backend\models\LoginForm;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use feehi\libs\ServerInfo;
use backend\models\Article as ArticleModel;

/**
 * Site controller
 */
class SiteController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error','language'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'main'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post','get'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->renderPartial('index');
    }

    public function actionMain()
    {
        $info = [
            'OPERATING_SYSTEM' => PHP_OS,
            'OPERATING_ENVIRONMENT' => $_SERVER["SERVER_SOFTWARE"],
            'PHP_RUN_MODE' => php_sapi_name(),
            'MYSQL_VERSION' => (new yii\db\Query())->select('VERSION()')->one()['VERSION()'],
            'PROGRAM_VERSION' => "1.0",
            'UPLOAD_MAX_FILESIZE' => ini_get('upload_max_filesize'),
            'MAX_EXECUTION_TIME' => ini_get('max_execution_time') . "s"
        ];
        $dt = round(@disk_total_space(".")/(1024*1024*1024),3); //总
        $df = round(@disk_free_space(".")/(1024*1024*1024),3); //可用
        $hdPercent = (floatval($dt)!=0)?($df/$dt)*100:0;
        $obj = new ServerInfo();
        $serverInfo = $obj->getinfo();
        $status = [
            'DISK_SPACE' => [
                'NUM' => ceil($df).'G'.' / '.ceil($dt).'G',
                'PERCENTAGE' => $hdPercent,
            ],
            'MEM' => [
                'NUM' => $serverInfo["memUsed"].'MB'.' / '.$serverInfo['memTotal'].'MB',
                'PERCENTAGE' => $serverInfo["memPercent"],
            ],
            'REAL_MEM' => [
                'NUM' => 'Used:'.$serverInfo["memRealUsed"].' / '.'Cached:'.$serverInfo["memCached"].'MB',
                'PERCENTAGE' => (($serverInfo["memRealUsed"]/$serverInfo['memTotal'])*100).'%',
            ],
        ];
        $statics = [
            'CATEGORY' => \common\models\Category::find()->count('id'),
            'ARTICLE' => ArticleModel::find()->where(['type'=>ArticleModel::ARTICLE])->count('id'),
            'PAGE' => ArticleModel::find()->where(['type'=>ArticleModel::SINGLE_PAGE])->count('id'),
            'BACKEND_MENU' => \backend\models\Menu::find()->where(['type'=>\backend\models\Menu::BACKEND_TYPE])->count('id'),
            'FRONTEND_MENU' => \frontend\models\Menu::find()->where(['type'=>\frontend\models\Menu::FRONTEND_TYPE])->count('id'),
            'USER' => \frontend\models\User::find()->count('id'),
            'ROLE' => \backend\models\AdminRoles::find()->count('id'),
            'ADMIN_USER' => \backend\models\User::find()->count('id'),
            'FRIEND_LINK' => \common\models\FriendLink::find()->count('id'),
        ];
        $comments = \backend\models\Comment::getRecentComments(10);
        return $this->render('main', [
            'info' => $info,
            'status' => $status,
            'statics' => $statics,
            'comments' => $comments,
        ]);
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionLanguage(){
        $language=  \Yii::$app->request->get('lang');
        if(isset($language)){
            \Yii::$app->session['language'] = $language;
        }
        $this->goBack(\Yii::$app->request->headers['Referer']);
    }
}
