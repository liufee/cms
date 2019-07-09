<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-23 12:08
 */

namespace backend\controllers;

use backend\actions\CreateAction;
use backend\actions\IndexAction;
use Yii;
use backend\actions\UpdateAction;
use backend\actions\DeleteAction;
use backend\models\form\SettingWebsiteForm;
use backend\models\form\SettingSmtpForm;
use common\models\Options;
use yii\base\Model;
use yii\web\Response;
use yii\swiftmailer\Mailer;
use yii\web\BadRequestHttpException;
use yii\web\UnprocessableEntityHttpException;

/**
 * Setting controller
 */
class SettingController extends \yii\web\Controller
{

    /**
     * @auth
     * - item group=设置 category=网站设置 description=网站设置 sort-get=100 sort-post=101 method=get,post
     * - item group=设置 category=自定义设置 description-post=删除  sort=132 method=post
     * - item group=设置 category=自定义设置 description=自定义设置创建 sort-get=133 sort-post=134 method=get,post
     * - item group=设置 category=自定义设置 description=自定义设置修改 sort-get=135 sort-post=136 method=get,post
     * - item group=设置 category=smtp设置 description=修改 sort-get=110 sort-post=111 method=get,post
     *
     * @return array
     */
    public function actions()
    {
        return [
            'website' => [
                "class" => UpdateAction::className(),
                'model' => function(){
                    $model = Yii::createObject( SettingWebsiteForm::className() );
                    $model->getWebsiteSetting();
                    return $model;
                },
                'executeMethod' => function($model){
                    /** @var SettingWebsiteForm $model  */
                    if( $model->validate() && $model->setWebsiteConfig() ){
                        return true;
                    }
                    return false;
                },
                'successRedirect' => ["setting/website"]
            ],
            "custom-delete" => [
                "class" => DeleteAction::className(),
                "modelClass" => Options::className(),
            ],
            'custom-create' => [
                "class" => CreateAction::className(),
                "model" => function(){
                    $this->layout = false;
                    /** @var Options $model */
                    $model = Yii::createObject( Options::className() );
                    $model->type = Options::TYPE_CUSTOM;
                    return $model;
                }
            ],
            'custom-update' => [
                "class" => UpdateAction::className(),
                "model" => function(){
                    $this->layout = false;
                    $id = Yii::$app->getRequest()->get("id", "");
                    $model = Options::findOne(['id' => $id]);
                    return $model;
                }
            ],
            "smtp" => [
                "class" => UpdateAction::className(),
                "model" => function(){
                    /** @var SettingSmtpForm $model */
                    $model = Yii::createObject( SettingSmtpForm::className() );
                    $model->getSmtpConfig();
                    return $model;
                },
                "executeMethod" => function($model){
                    /** @var SettingSmtpForm $model */
                    if( $model->validate() && $model->setSmtpConfig() ){
                        return true;
                    }
                    return false;
                },
                'successRedirect' => ['setting/smtp']
            ]
        ];
    }


    /**
     * 自定义设置
     *
     * @auth - item group=设置 category=自定义设置 description=修改 sort-get=130 sort-post=131 method=get,post
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCustom()
    {
        $settings = Options::find()->where(['type' => Options::TYPE_CUSTOM])->orderBy("sort")->indexBy('id')->all();

        if (Model::loadMultiple($settings, Yii::$app->getRequest()->post()) && Model::validateMultiple($settings)) {
            foreach ($settings as $setting) {
                $setting->save(false);
            }
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Success'));
        }
        $options = Yii::createObject( Options::className() );
        $options->loadDefaultValues();

        return $this->render('custom', [
            'settings' => $settings,
            'model' => $options,
        ]);
    }

    /**
     * 发送测试邮件确认smtp设置是否正确
     *
     * @auth - item group=设置 category=smtp设置 description-post=测试stmp设置 sort-post=112 method=post
     * @return mixed
     * @throws BadRequestHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionTestSmtp()
    {
        $model = Yii::createObject( SettingSmtpForm::className() );
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;
        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            $mailer = Yii::createObject([
                'class' => Mailer::className(),
                'useFileTransport' => false,
                'transport' => [
                    'class' => 'Swift_SmtpTransport',
                    'host' => $model->smtp_host,
                    'username' => $model->smtp_username,
                    'password' => $model->smtp_password,
                    'port' => $model->smtp_port,
                    'encryption' => $model->smtp_encryption,

                ],
                'messageConfig' => [
                    'charset' => 'UTF-8',
                    'from' => [$model->smtp_username => $model->smtp_nickname]
                ],
            ]);
            return $mailer->compose()
                ->setFrom($model->smtp_username)
                ->setTo($model->smtp_username)
                ->setSubject('Email SMTP test ' . Yii::$app->name)
                ->setTextBody('Email SMTP config works successful')
                ->send();
        } else {
            $error = '';
            foreach ($model->getErrors() as $item) {
                $error .= $item[0] . "<br/>";
            }
            throw new BadRequestHttpException( $error );
        }
    }

}
