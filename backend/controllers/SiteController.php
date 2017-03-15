<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\controllers;

use Yii;
use common\models\Comment;
use backend\models\LoginForm;
use common\libs\ServerInfo;
use backend\models\Article as ArticleModel;
use backend\models\Comment as BackendComment;
use common\models\FriendLink;
use frontend\models\User;
use yii\db\Query;

/**
 * Site controller
 */
class SiteController extends BaseController
{

    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'backColor' => 0x66b3ff,//背景颜色
                'maxLength' => 4, //最大显示个数
                'minLength' => 4,//最少显示个数
                'padding' => 10,//间距
                'height' => 34,//高度
                'width' => 100,  //宽度
                'foreColor' => 0xffffff,     //字体颜色
                'offset' => 16,        //设置字符偏移量 有效果
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->renderPartial('index');
    }

    public function actionMain()
    {
        switch (yii::$app->getDb()->driverName) {
            case "mysql":
                $dbInfo = 'MySQL ' . (new Query())->select('version()')->one()['version()'];
                break;
            case "pgsql":
                $dbInfo = (new Query())->select('version()')->one()['version'];
                break;
            default:
                "Unknown";
        }
        $info = [
            'OPERATING_ENVIRONMENT' => PHP_OS . ' ' . $_SERVER["SERVER_SOFTWARE"],
            'PHP_RUN_MODE' => php_sapi_name(),
            'DB_INFO' => $dbInfo,
            'PROGRAM_VERSION' => "1.0",
            'UPLOAD_MAX_FILESIZE' => ini_get('upload_max_filesize'),
            'MAX_EXECUTION_TIME' => ini_get('max_execution_time') . "s"
        ];
        $dt = round(@disk_total_space(".") / (1024 * 1024 * 1024), 3); //总
        $df = round(@disk_free_space(".") / (1024 * 1024 * 1024), 3); //可用
        $hdPercent = (floatval($dt) != 0) ? ($df / $dt) * 100 : 0;
        $obj = new ServerInfo();
        $serverInfo = $obj->getinfo();
        $status = [
            'DISK_SPACE' => [
                'NUM' => ceil($df) . 'G' . ' / ' . ceil($dt) . 'G',
                'PERCENTAGE' => $hdPercent,
            ],
            'MEM' => [
                'NUM' => $serverInfo["memUsed"] . 'MB' . ' / ' . $serverInfo['memTotal'] . 'MB',
                'PERCENTAGE' => $serverInfo["memPercent"],
            ],
            'REAL_MEM' => [
                'NUM' => 'Used:' . $serverInfo["memRealUsed"] . ' / ' . 'Cached:' . $serverInfo["memCached"] . 'MB',
                'PERCENTAGE' => (($serverInfo["memRealUsed"] / $serverInfo['memTotal']) * 100) . '%',
            ],
        ];
        $temp = [
            'ARTICLE' => ArticleModel::find()->where(['type' => ArticleModel::ARTICLE])->count('id'),
            'COMMENT' => Comment::find()->count('id'),
            'USER' => User::find()->count('id'),
            'FRIEND_LINK' => FriendLink::find()->count('id'),
        ];
        $statics = [
            'ARTICLE' => [
                $temp['ARTICLE'],
                number_format(ArticleModel::find()->where([
                        'between',
                        'created_at',
                        strtotime(date('Y-m-01')),
                        strtotime(date('Y-m-01 23:59:59') . " +1 month -1 day")
                    ])->count('id') / $temp['ARTICLE'] * 100, 2)
            ],
            'COMMENT' => [
                $temp['COMMENT'],
                number_format(Comment::find()->where([
                        'between',
                        'created_at',
                        strtotime(date('Y-m-d 00:00:00')),
                        time()
                    ])->count('id') / $temp['COMMENT'] * 100, 2)
            ],
            'USER' => [
                $temp['USER'],
                number_format(User::find()->where([
                        'between',
                        'created_at',
                        strtotime(date('Y-m-01')),
                        strtotime(date('Y-m-01 23:59:59') . " +1 month -1 day")
                    ])->count('id') / $temp['USER'] * 100, 2)
            ],
            'FRIEND_LINK' => [
                $temp['FRIEND_LINK'],
                number_format(FriendLink::find()->where([
                        'between',
                        'created_at',
                        strtotime(date('Y-m-01')),
                        strtotime(date('Y-m-01 23:59:59') . " +1 month -1 day")
                    ])->count('id') / $temp['FRIEND_LINK'] * 100, 2)
            ],
        ];
        $comments = BackendComment::getRecentComments(4);
        return $this->render('main', [
            'info' => $info,
            'status' => $status,
            'statics' => $statics,
            'comments' => $comments,
        ]);
    }

    public function actionLogin()
    {
        if (! Yii::$app->getUser()->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->getRequest()->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->renderPartial('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->getUser()->logout(false);

        return $this->goHome();
    }

    public function actionLanguage()
    {
        $language = Yii::$app->getRequest()->get('lang');
        if (isset($language)) {
            Yii::$app->session['language'] = $language;
        }
        $this->goBack(Yii::$app->getRequest()->headers['referer']);
    }
}
