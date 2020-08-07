<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace common\components;

use Yii;
use common\models\Category;
use feehi\cdn\DummyTarget;
use common\helpers\FileDependencyHelper;
use backend\components\CustomLog;
use yii\base\Component;
use backend\components\AdminLog;
use common\models\Options;
use yii\caching\FileDependency;
use yii\base\Event;
use yii\db\BaseActiveRecord;

class Feehi extends Component
{

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return isset($this->$name) ? $this->$name : '';
    }


    public function init()
    {
        parent::init();

        $cache = Yii::$app->getCache();
        $key = 'options';
        if (($data = $cache->get($key)) === false) {
            $data = Options::find()->where(['type' => Options::TYPE_SYSTEM])->orwhere([
                'type' => Options::TYPE_CUSTOM,
                'autoload' => Options::CUSTOM_AUTOLOAD_YES,
            ])->asArray()->indexBy("name")->all();
            /** @var FileDependencyHelper $cacheDependencyObject */
            $cacheDependencyObject = Yii::createObject([
                'class' => FileDependencyHelper::className(),
                'fileName' => Options::CACHE_DEPENDENCY_TYPE_SYSTEM_FILE_NAME,
            ]);
            $fileName = $cacheDependencyObject->createFileIfNotExists();
            $dependency = new FileDependency(['fileName' => $fileName]);
            $cache->set($key, $data, 30*60, $dependency);
        }

        foreach ($data as $v) {
            $this->{$v['name']} = $v['value'];
        }
    }


    private static function mergeAdminUserSettingConfig()
    {
        //merge backend admin user setting config options
        if (! empty(Yii::$app->feehi->website_url)) {
            Yii::$app->params['site']['url'] = Yii::$app->feehi->website_url;
        }

        if (! empty(Yii::$app->feehi->smtp_host) && ! empty(Yii::$app->feehi->smtp_username)) {
            Yii::configure(Yii::$app->mailer, [
                'useFileTransport' => false,
                'transport' => [
                    'class' => 'Swift_SmtpTransport',
                    'host' => Yii::$app->feehi->smtp_host,  //每种邮箱的host配置不一样
                    'username' => Yii::$app->feehi->smtp_username,
                    'password' => Yii::$app->feehi->smtp_password,
                    'port' => Yii::$app->feehi->smtp_port,
                    'encryption' => Yii::$app->feehi->smtp_encryption,

                ],
                'messageConfig' => [
                    'charset' => 'UTF-8',
                    'from' => [Yii::$app->feehi->smtp_username => Yii::$app->feehi->smtp_nickname]
                ],
            ]);
        }

        //format config options
        if (substr(Yii::$app->params['site']['url'], -1, 1) != '/') {
            Yii::$app->params['site']['url'] .= '/';
        }
        if (stripos(Yii::$app->params['site']['url'], 'http://') !== 0 && stripos(Yii::$app->params['site']['url'], 'https://') !== 0 && stripos(yii::$app->params['site']['url'], '//')) {
            Yii::$app->params['site']['url'] = (Yii::$app->getRequest()->getIsSecureConnection() ? "https://" : "http://") . yii::$app->params['site']['url'];
        }

        $cdn = Yii::$app->get('cdn');
        if( $cdn instanceof DummyTarget){
            Yii::configure(Yii::$app->cdn, [
                'host' => Yii::$app->params['site']['url']
            ]);
        }
    }

    public static function frontendInit()
    {
        self::mergeAdminUserSettingConfig();

        if (! Yii::$app->feehi->website_status) {
            Yii::$app->catchAll = ['site/offline'];
        }

        Yii::$app->language = Yii::$app->feehi->website_language;
        self::determineLanguage();

        Yii::$app->timeZone = Yii::$app->feehi->website_timezone;
        if (! isset(Yii::$app->params['site']['url']) || empty(Yii::$app->params['site']['url'])) {
            Yii::$app->params['site']['url'] = Yii::$app->request->getHostInfo();
        }
        if(isset(Yii::$app->session['view'])) Yii::$app->viewPath = Yii::getAlias('@frontend/view') . Yii::$app->session['view'];

        $category = new Category();
        Yii::configure(Yii::$app->getUrlManager(), [
            'rules' => array_merge(Yii::$app->getUrlManager()->rules, $category->getUrlRules())
        ]);
        Yii::$app->getUrlManager()->init();
    }

    public static function backendInit()
    {
        self::mergeAdminUserSettingConfig();
        Event::on(BaseActiveRecord::className(), BaseActiveRecord::EVENT_AFTER_INSERT, [
            AdminLog::className(),
            'create'
        ]);
        Event::on(BaseActiveRecord::className(), BaseActiveRecord::EVENT_AFTER_UPDATE, [
            AdminLog::className(),
            'update'
        ]);
        Event::on(BaseActiveRecord::className(), BaseActiveRecord::EVENT_AFTER_DELETE, [
            AdminLog::className(),
            'delete'
        ]);
        Event::on(CustomLog::className(), CustomLog::EVENT_AFTER_CREATE, [
            AdminLog::className(),
            'custom'
        ]);
        Event::on(CustomLog::className(), CustomLog::EVENT_AFTER_DELETE, [
            AdminLog::className(),
            'custom'
        ]);
        Event::on(CustomLog::className(), CustomLog::EVENT_CUSTOM, [
            AdminLog::className(),
            'custom'
        ]);
        Event::on(BaseActiveRecord::className(), BaseActiveRecord::EVENT_AFTER_FIND, function ($event) {
            if (isset($event->sender->updated_at) && $event->sender->updated_at == 0) {
                $event->sender->updated_at = null;
            }
        });
        self::determineLanguage();
    }

    public  static function determineLanguage()
    {
        if (isset(Yii::$app->session['language'])) {//user selected language already
            Yii::$app->language = Yii::$app->session['language'];
        }else {
            $supportLanguages = array_flip(Yii::$app->params['supportLanguages']);
            $supportLanguagesWithoutArea = [];
            foreach ($supportLanguages as $supportLanguage) {
                $arr = explode("-", $supportLanguage);
                if (is_array($arr) && count($arr) == 2) {
                    $supportLanguagesWithoutArea[ $arr[0]] = $supportLanguage;
                }
            }

            $determinedLanguage = false;
            $acceptLanguages = Yii::$app->getRequest()->getAcceptableLanguages();
            foreach ($acceptLanguages as $k=> $acceptLanguage) {//match like en-US
                if (in_array($acceptLanguage, $supportLanguages)) {
                    Yii::$app->language = $acceptLanguage;
                    $determinedLanguage = true;
                    break;
                }
                if( strpos($acceptLanguage, "-") !== false ) {
                    $temp = explode("-", $acceptLanguage);
                    $shortLanguage = $temp[0];
                }else{
                    $shortLanguage = $acceptLanguage;
                }
                if (isset($supportLanguagesWithoutArea[$shortLanguage])) {//match like en
                    Yii::$app->language = $supportLanguagesWithoutArea[$shortLanguage];
                    $determinedLanguage = true;
                    break;

                }
            }

            if ($determinedLanguage){
                return;
            }

            foreach ($acceptLanguages as $acceptLanguage){
                if( isset($supportLanguagesWithoutArea[$acceptLanguage]) ) {//match like en
                    Yii::$app->language = $supportLanguagesWithoutArea[$acceptLanguage];
                    break;
                }
            }
        }
    }

}