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
use common\services\ArticleServiceInterface;
use common\services\UserServiceInterface;
use common\services\CommentServiceInterface;
use common\services\FriendlyLinkServiceInterface;
use common\services\MenuService;
use backend\models\form\LoginForm;
use common\libs\ServerInfo;
use yii\base\UserException;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\HttpException;
use yii\captcha\CaptchaAction;

/**
 * Site default controller
 *
 * - description:
 *          - site/index is index page(framework)
 *          - site/main is the iframe default page
 *          - site/login backend login page
 *          - site/logout backend user logout
 *          - site/language multi language change
 *          - site/error backend error unify handler
 *
 * Class SiteController
 * @package backend\controllers
 */
class SiteController extends \yii\web\Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' =>['login', 'captcha', 'language'],//no need login actions
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],//"@" represent authenticated user; "?" means guest user (not authenticated yet).
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],//logout action only permit POST request
                ],
            ],
        ];
    }

    public function actions()
    {
        $captcha = [
            'class' => CaptchaAction::className(),
            'backColor' => 0x66b3ff,//captcha background color
            'maxLength' => 4,//captcha max count of characters
            'minLength' => 4,//captcha min count of characters
            'padding' => 6,
            'height' => 34,
            'width' => 100,
            'foreColor' => 0xffffff,//captcha character color
            'offset' => 13,//offset between characters
        ];
        if( YII_ENV_TEST ) $captcha = array_merge($captcha, ['fixedVerifyCode'=>'testme']);
        return [
            'captcha' => $captcha,
        ];
    }

    /**
     * backend index page(backend default action)
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws \Throwable
     */
    public function actionIndex()
    {
        $service = new MenuService();
       $menus = $service->getAuthorizedBackendMenusByUserId(Yii::$app->getUser()->getId());
        return $this->renderPartial('index', [
            "menus" => $menus,
            'identity' => Yii::$app->getUser()->getIdentity(),
        ]);
    }

    /**
     * backend main info page(default right iframe page)
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionMain()
    {
        /** @var ArticleServiceInterface $articleService */
        $articleService = Yii::$app->get(ArticleServiceInterface::ServiceName);
        /** @var CommentServiceInterface $commentService */
        $commentService = Yii::$app->get(CommentServiceInterface::ServiceName);
        /** @var UserServiceInterface $userService */
        $userService = Yii::$app->get(UserServiceInterface::ServiceName);
        /** @var FriendlyLinkServiceInterface $friendlyLinkService */
        $friendlyLinkService = Yii::$app->get(FriendlyLinkServiceInterface::ServiceName);

        $info = [
            'OPERATING_ENVIRONMENT' => PHP_OS . ' ' . $_SERVER['SERVER_SOFTWARE'],
            'PHP_RUN_MODE' => php_sapi_name(),
            'DB_INFO' => Yii::$app->getDb()->getDriverName() . " " . Yii::$app->getDb()->getServerVersion(),
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
            'ARTICLE' => $articleService->getArticlesCountByPeriod(),
            'COMMENT' => $commentService->getCommentCountByPeriod(),
            'USER' => $userService->getUserCountByPeriod(),
            'FRIENDLY_LINK' => $friendlyLinkService->getFriendlyLinkCountByPeriod(),
        ];
        $percent = '0.00';
        $statics = [
            'ARTICLE' => [
                $temp['ARTICLE'],
                $temp['ARTICLE'] ? sprintf("%01.2f",($articleService->getArticlesCountByPeriod(strtotime(date('Y-m-01 00:00:00')), strtotime(date('Y-m-01 23:59:59') . " +1 month -1 day")) / $temp['ARTICLE']) * 100) : $percent
            ],
            'COMMENT' => [
                $temp['COMMENT'],
                $temp['COMMENT'] ? sprintf("%01.2f",($commentService->getCommentCountByPeriod(strtotime(date('Y-m-01 00:00:00')), strtotime(date('Y-m-01 23:59:59'))) / $temp['COMMENT']) * 100) : $percent
            ],
            'USER' => [
                $temp['USER'],
                $temp['USER'] ? sprintf("%01.2f",($userService->getUserCountByPeriod(strtotime(date('Y-m-01 00:00:00')), strtotime(date('Y-m-01 23:59:59') . " +1 month -1 day")) / $temp['USER']) * 100) : $percent
            ],
            'FRIENDLY_LINK' => [
                $temp['FRIENDLY_LINK'],
                $temp['FRIENDLY_LINK'] ? sprintf("%01.2f",($friendlyLinkService->getFriendlyLinkCountByPeriod(strtotime(date('Y-m-01 00:00:00')), strtotime(date('Y-m-01 23:59:59') . " +1 month -1 day")) / $temp['FRIENDLY_LINK']) * 100) : $percent
            ],
        ];
        /** @var CommentServiceInterface $commentService */
        $commentService = Yii::$app->get(CommentServiceInterface::ServiceName);
        $comments = $commentService->getRecentComments(6);
        return $this->render('main', [
            'info' => $info,
            'status' => $status,
            'statics' => $statics,
            'comments' => $comments,
        ]);
    }

    /**
     * admin login
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
     * admin logout
     *
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->getUser()->logout(false);

        return $this->goHome();
    }

    /**
     *
     * language change
     */
    public function actionLanguage()
    {
        $language = Yii::$app->getRequest()->get('lang');
        if (isset($language)) {
            $session = Yii::$app->getSession();
            $session->set("language", Html::encode($language));
        }
        $this->goBack(Yii::$app->getRequest()->headers['referer']);
    }

    /**
     * backend unify exception handler
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
            $name = Yii::t('yii', 'Error');
        }
        if ($code) {
            $name .= " (#$code)";
        }

        if ($exception instanceof UserException) {
            $message = $exception->getMessage();
        } else {
            $message = Yii::t('yii', 'An internal server error occurred.');
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
