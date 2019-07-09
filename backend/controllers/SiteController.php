<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\controllers;

use Yii;
use Exception;
use common\models\Comment;
use backend\models\form\LoginForm;
use common\libs\ServerInfo;
use backend\models\Article as ArticleModel;
use backend\models\Comment as BackendComment;
use common\models\FriendlyLink;
use frontend\models\User;
use yii\base\UserException;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\captcha\CaptchaAction;

/**
 * Site controller
 */
class SiteController extends \yii\web\Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' =>['login', 'captcha', 'language'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        $captcha = [
            'class' => CaptchaAction::className(),
            'backColor' => 0x66b3ff,//背景颜色
            'maxLength' => 4,//最大显示个数
            'minLength' => 4,//最少显示个数
            'padding' => 6,//验证码字体大小，数值越小字体越大
            'height' => 34,//高度
            'width' => 100,//宽度
            'foreColor' => 0xffffff,//字体颜色
            'offset' => 13,//设置字符偏移量
        ];
        if( YII_ENV_TEST ) $captcha = array_merge($captcha, ['fixedVerifyCode'=>'testme']);
        return [
            'captcha' => $captcha,
        ];
    }

    /**
     * 后台首页
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->renderPartial('index');
    }

    /**
     * 主页
     *
     * @return string
     */
    public function actionMain()
    {
        switch (Yii::$app->getDb()->driverName) {
            case "mysql":
                $dbInfo = 'MySQL ' . (new Query())->select('version()')->one()['version()'];
                break;
            case "pgsql":
                $dbInfo = (new Query())->select('version()')->one()['version'];
                break;
            default:
                $dbInfo = "Unknown";
        }
        $info = [
            'OPERATING_ENVIRONMENT' => PHP_OS . ' ' . $_SERVER['SERVER_SOFTWARE'],
            'PHP_RUN_MODE' => php_sapi_name(),
            'DB_INFO' => $dbInfo,
            'UPLOAD_MAX_FILE_SIZE' => ini_get('upload_max_filesize'),
            'MAX_EXECUTION_TIME' => ini_get('max_execution_time') . "s"
        ];
        $obj = new ServerInfo();
        $serverInfo = $obj->getinfo();
        $status = [
            'DISK_SPACE' => [
                'NUM' => ceil( $serverInfo['diskTotal'] - $serverInfo['freeSpace'] ) . 'G' . ' / ' . ceil($serverInfo['diskTotal']) . 'G',
                'PERCENTAGE' => (floatval($serverInfo['diskTotal']) != 0) ? round(($serverInfo['diskTotal'] - $serverInfo['freeSpace']) / $serverInfo['diskTotal'] * 100, 2) : 0,
            ],
            'MEM' => [
                'NUM' => $serverInfo["UsedMemory"] . ' / ' . $serverInfo['TotalMemory'],
                'PERCENTAGE' => $serverInfo["memPercent"],
            ],
            'REAL_MEM' => [
                'NUM' => $serverInfo["memRealUsed"] . "(Cached {$serverInfo['CachedMemory']})" . ' / ' . $serverInfo['TotalMemory'],
                'PERCENTAGE' => $serverInfo['memRealPercent'] . '%',
            ],
        ];
        $temp = [
            'ARTICLE' => ArticleModel::find()->where(['type' => ArticleModel::ARTICLE])->count('id'),
            'COMMENT' => Comment::find()->count('id'),
            'USER' => User::find()->count('id'),
            'FRIEND_LINK' => FriendlyLink::find()->count('id'),
        ];
        $percent = '0.00';
        $statics = [
            'ARTICLE' => [
                $temp['ARTICLE'],
                $temp['ARTICLE'] ? number_format(ArticleModel::find()->where([
                        'between',
                        'created_at',
                        strtotime(date('Y-m-01')),
                        strtotime(date('Y-m-01 23:59:59') . " +1 month -1 day")
                    ])->count('id') / $temp['ARTICLE'] * 100, 2) : $percent
            ],
            'COMMENT' => [
                $temp['COMMENT'],
                $temp['COMMENT'] ? number_format(Comment::find()->where([
                        'between',
                        'created_at',
                        strtotime(date('Y-m-d 00:00:00')),
                        time()
                    ])->count('id') / $temp['COMMENT'] * 100, 2) : $percent
            ],
            'USER' => [
                $temp['USER'],
                $temp['USER'] ? number_format(User::find()->where([
                        'between',
                        'created_at',
                        strtotime(date('Y-m-01')),
                        strtotime(date('Y-m-01 23:59:59') . " +1 month -1 day")
                    ])->count('id') / 1 * 100, 2) : $percent
            ],
            'FRIEND_LINK' => [
                $temp['FRIEND_LINK'],
                $temp['FRIEND_LINK'] ? number_format(FriendlyLink::find()->where([
                        'between',
                        'created_at',
                        strtotime(date('Y-m-01')),
                        strtotime(date('Y-m-01 23:59:59') . " +1 month -1 day")
                    ])->count('id') / $temp['FRIEND_LINK'] * 100, 2) : $percent
            ],
        ];
        $comments = BackendComment::getRecentComments(6);
        return $this->render('main', [
            'info' => $info,
            'status' => $status,
            'statics' => $statics,
            'comments' => $comments,
        ]);
    }

    /**
     * 管理员登录
     *
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        if (! Yii::$app->getUser()->getIsGuest()) {
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

    /**
     * 管理员退出登录
     *
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->getUser()->logout(false);

        return $this->goHome();
    }

    /**
     * 切换语言
     *
     */
    public function actionLanguage()
    {
        $language = Yii::$app->getRequest()->get('lang');
        if (isset($language)) {
            $session = Yii::$app->getSession();
            $session->set("language", $language);
        }
        $this->goBack(Yii::$app->getRequest()->headers['referer']);
    }

    /**
     * http异常捕捉后处理
     *
     * @return string
     */
    public function actionError()
    {
        if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
            // action has been invoked not from error handler, but by direct route, so we display '404 Not Found'
            $exception = new HttpException(404, Yii::t('yii', 'Page not found.'));
        }

        if ($exception instanceof HttpException) {
            $code = $exception->statusCode;
        } else {
            $code = $exception->getCode();
        }
        if ($exception instanceof Exception) {
            $name = $exception->getName();
        } else {
            $name = $this->defaultName ?: Yii::t('yii', 'Error');
        }
        if ($code) {
            $name .= " (#$code)";
        }

        if ($exception instanceof UserException) {
            $message = $exception->getMessage();
        } else {
            $message = $this->defaultMessage ?: Yii::t('yii', 'An internal server error occurred.');
        }
        $statusCode = $exception->statusCode ? $exception->statusCode : 500;
        if (Yii::$app->getRequest()->getIsAjax()) {
            return "$name: $message";
        } else {
            return $this->render('error', [
                'code' => $statusCode,
                'name' => $name,
                'message' => $message
            ]);
        }
    }

}
