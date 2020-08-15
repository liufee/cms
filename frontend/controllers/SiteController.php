<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace frontend\controllers;

use Yii;
use frontend\models\form\SignupForm;
use frontend\models\form\LoginForm;
use frontend\models\form\PasswordResetRequestForm;
use frontend\models\form\ResetPasswordForm;
use yii\base\InvalidParamException;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\HttpException;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post', 'get'],
                ],
            ],
        ];
    }

    /**
     * Logs in a user.
     *
     * @return mixed
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
            Yii::$app->getUser()->setReturnUrl(Yii::$app->getRequest()->getHeaders()->get('referer'));
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->getUser()->logout(false);

        return $this->goHome();
    }

    /**
     * Signs user up.
     *
     * @return mixed
     * @throws yii\base\Exception
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->getRequest()->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()
                    ->setFlash('success', Yii::t('app', 'Check your email for further instructions.'));

                return $this->goHome();
            } else {
                Yii::$app->getSession()
                    ->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'New password was saved.'));

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * website maintain shows page
     * when at "/admin/index.php?r=site/website" change website status to closed every request will execute this action
     */
    public function actionOffline()
    {
        Yii::$app->getResponse()->statusCode = 503;
        return "sorry, the site is temporary unserviceable";
    }


    /**
     * change view template
     * development website template first，then config according to yii2 document
     */
    public function actionView()
    {
        $view = Yii::$app->getRequest()->get('type', null);
        if (isset($view) && !empty($view)) {
            Yii::$app->session['view'] = $view;
        }
        $this->goBack( Yii::$app->getRequest()->getReferrer() );
    }

    /**
     * change language
     */
    public function actionLanguage()
    {
        $language = Yii::$app->getRequest()->get('lang');
        if (isset($language)) {
            $session = Yii::$app->getSession();
            $session['language'] = Html::encode($language);
        }
        $this->redirect( Yii::$app->getRequest()->getReferrer() );
    }

    /**
     * exception handler
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
        //if ($exception instanceof Exception) {
        $name = $exception->getName();
        //} else {
        //$name = $this->defaultName ?: Yii::t('Yii', 'Error');
        //}
        if ($code) {
            $name .= " (#$code)";
        }

        //if ($exception instanceof UserException) {
        $message = $exception->getMessage();
        //} else {
        //$message = $this->defaultMessage ?: Yii::t('Yii', 'An internal server error occurred.');
        //}
        $statusCode = $exception->statusCode ? $exception->statusCode : 500;
        if (Yii::$app->getRequest()->getIsAjax()) {
            return "$name: $message";
        } else {
            return $this->render('error', [
                'code' => $statusCode,
                'name' => $name,
                'message' => $message,
                'exception' => $exception,
            ]);
        }
    }

}
