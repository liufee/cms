<?php


namespace common\services;


use backend\models\form\SettingSMTPForm;
use backend\models\form\SettingWebsiteForm;
use common\models\Options;
use Yii;
use yii\base\Model;
use yii\swiftmailer\Mailer;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class SettingService extends Service implements SettingServiceInterface
{

    public function getSearchModel(array $query, array $options = [])
    {
        // TODO: Implement getSearchModel() method.
    }

    public function getModel($id, array $options = [])
    {
        if($id == "website") {
            return new SettingWebsiteForm();
        }else if($id == "custom"){
            return $this->getCustomSettings();
        }else if($id == "smtp"){
            return new SettingSMTPForm();
        } else if( is_numeric($id) ){
            return Options::findOne($id);
        }
    }

    public function getNewModel(array $options = [])
    {
        $model = new Options();
        $model->loadDefaultValues();
        if( isset($options['type']) && in_array($options['type'], [Options::TYPE_SYSTEM, Options::TYPE_CUSTOM, Options::TYPE_BANNER, Options::TYPE_AD]) ){
            $model->type = $options['type'];
        }
        return $model;
    }

    public function getCustomSettings(){
        return Options::find()->where(['type' => Options::TYPE_CUSTOM])->orderBy("sort")->indexBy('id')->all();
    }

    public function updateWebsiteSetting(array $postData = [])
    {
        $model = $this->getModel("website");
        if ( $model->load($postData) && $model->setWebsiteConfig() ){
            return true;
        }
        return $model->getErrors();
    }

    public function updateCustomSetting(array $postData=[]){
        $errors = [];
        $settings = $this->getCustomSettings();
        if (Model::loadMultiple($settings, $postData) && Model::validateMultiple($settings)) {
            foreach ($settings as $setting) {
                $result = $setting->save(false);
                if (!$result){
                    $errors[] = $setting->getErrors();
                }
            }
        }
        if(count($errors) === 0 ){
            return true;
        }
        return $errors;
    }

    public function updateSMTPSetting(array $postData = [])
    {
        $model = $this->getModel("smtp");
        if ( $model->load($postData) && $model->setSMTPSettingConfig() ){
            return true;
        }
        return $model->getErrors();
    }

    public function testSMTPSetting(array $postData = []){
        $model = $this->getModel("smtp");
        if ($model->load($postData) && $model->validate()) {
            /** @var Mailer $mailer */
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
            $result = $mailer->compose()
                ->setFrom($model->smtp_username)
                ->setTo($model->smtp_username)
                ->setSubject('Email SMTP test ' . Yii::$app->name)
                ->setTextBody('Email SMTP config works successful')
                ->send();
            if( $result === false ){
                return $result;
            }
            return "";
        } else {
           return $model->getErrors();
        }
    }
}