<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace feehi\components;

use common\models\Category;
use feehi\cdn\DummyTarget;
use Yii;
use common\helpers\FileDependencyHelper;
use backend\components\CustomLog;
use yii\base\Component;
use backend\components\AdminLog;
use common\models\Options;
use yii\caching\FileDependency;
use yii\base\Event;
use yii\db\BaseActiveRecord;
use yii\web\Response;

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
            $cacheDependencyObject = Yii::createObject([
                'class' => FileDependencyHelper::className(),
                'rootDir' => '@backend/runtime/cache/file_dependency/',
                'fileName' => 'options.txt',
            ]);
            $fileName = $cacheDependencyObject->createFile();
            $dependency = new FileDependency(['fileName' => $fileName]);
            $cache->set($key, $data, 0, $dependency);
        }

        foreach ($data as $v) {
            $this->{$v['name']} = $v['value'];
        }
    }


    private static function configInit()
    {
        if (! empty(Yii::$app->feehi->website_url)) {
            Yii::$app->params['site']['url'] = Yii::$app->feehi->website_url;
        }
        if (substr(Yii::$app->params['site']['url'], -1, 1) != '/') {
            Yii::$app->params['site']['url'] .= '/';
        }
        if (stripos(Yii::$app->params['site']['url'], 'http://') !== 0 && stripos(Yii::$app->params['site']['url'], 'https://') !== 0 && stripos(yii::$app->params['site']['url'], '//')) {
            Yii::$app->params['site']['url'] = ( Yii::$app->getRequest()->getIsSecureConnection() ? "https://" : "http://" ) . yii::$app->params['site']['url'];
        }

        if (isset(Yii::$app->session['language'])) {
            Yii::$app->language = Yii::$app->session['language'];
        }
        if (Yii::$app->getRequest()->getIsAjax()) {
            Yii::$app->getResponse()->format = Response::FORMAT_JSON;
        } else {
            Yii::$app->getResponse()->format = Response::FORMAT_HTML;
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

        $cdn = Yii::$app->get('cdn');
        if( $cdn instanceof DummyTarget){
            Yii::configure(Yii::$app->cdn, [
                'host' => Yii::$app->params['site']['url']
            ]);
        }
    }

    public static function frontendInit()
    {
        if (! Yii::$app->feehi->website_status) {
            Yii::$app->catchAll = ['site/offline'];
        }
        Yii::$app->language = Yii::$app->feehi->website_language;
        Yii::$app->timeZone = Yii::$app->feehi->website_timezone;
        if (! isset(Yii::$app->params['site']['url']) || empty(Yii::$app->params['site']['url'])) {
            Yii::$app->params['site']['url'] = Yii::$app->request->getHostInfo();
        }
        if(isset(Yii::$app->session['view'])) Yii::$app->viewPath = Yii::getAlias('@frontend/view') . Yii::$app->session['view'];

        Yii::configure(Yii::$app->getUrlManager(), [
            'rules' => array_merge(Yii::$app->getUrlManager()->rules, Category::getUrlRules())
        ]);
        Yii::$app->getUrlManager()->init();

        self::configInit();
    }

    public static function backendInit()
    {
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
        self::configInit();
    }

}