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
use yii;
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

        $cache = yii::$app->getCache();
        $key = 'options';
        if (($data = $cache->get($key)) === false) {
            $data = Options::find()->where(['type' => Options::TYPE_SYSTEM])->orwhere([
                'type' => Options::TYPE_CUSTOM,
                'autoload' => Options::CUSTOM_AUTOLOAD_YES,
            ])->asArray()->indexBy("name")->all();
            $cacheDependencyObject = yii::createObject([
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
        if (! empty(yii::$app->feehi->website_url)) {
            yii::$app->params['site']['url'] = yii::$app->feehi->website_url;
        }
        if (substr(yii::$app->params['site']['url'], -1, 1) != '/') {
            yii::$app->params['site']['url'] .= '/';
        }
        if (stripos(yii::$app->params['site']['url'], 'http://') !== 0 && stripos(yii::$app->params['site']['url'], 'https://') !== 0 && stripos(yii::$app->params['site']['url'], '//')) {
            yii::$app->params['site']['url'] = ( yii::$app->getRequest()->getIsSecureConnection() ? "https://" : "http://" ) . yii::$app->params['site']['url'];
        }

        if (isset(yii::$app->session['language'])) {
            yii::$app->language = yii::$app->session['language'];
        }
        if (yii::$app->getRequest()->getIsAjax()) {
            yii::$app->getResponse()->format = Response::FORMAT_JSON;
        } else {
            yii::$app->getResponse()->format = Response::FORMAT_HTML;
        }

        if (! empty(yii::$app->feehi->smtp_host) && ! empty(yii::$app->feehi->smtp_username)) {
            Yii::configure(yii::$app->mailer, [
                'useFileTransport' => false,
                'transport' => [
                    'class' => 'Swift_SmtpTransport',
                    'host' => yii::$app->feehi->smtp_host,  //每种邮箱的host配置不一样
                    'username' => yii::$app->feehi->smtp_username,
                    'password' => yii::$app->feehi->smtp_password,
                    'port' => yii::$app->feehi->smtp_port,
                    'encryption' => yii::$app->feehi->smtp_encryption,

                ],
                'messageConfig' => [
                    'charset' => 'UTF-8',
                    'from' => [yii::$app->feehi->smtp_username => yii::$app->feehi->smtp_nickname]
                ],
            ]);
        }

        $cdn = yii::$app->get('cdn');
        if( $cdn instanceof DummyTarget){
            Yii::configure(yii::$app->cdn, [
                'host' => yii::$app->params['site']['url']
            ]);
        }
    }

    public static function frontendInit()
    {
        if (! yii::$app->feehi->website_status) {
            yii::$app->catchAll = ['site/offline'];
        }
        yii::$app->language = yii::$app->feehi->website_language;
        yii::$app->timeZone = yii::$app->feehi->website_timezone;
        if (! isset(yii::$app->params['site']['url']) || empty(yii::$app->params['site']['url'])) {
            yii::$app->params['site']['url'] = yii::$app->request->getHostInfo();
        }
        if(isset(yii::$app->session['view'])) yii::$app->viewPath = yii::getAlias('@frontend/view') . yii::$app->session['view'];

        yii::configure(yii::$app->getUrlManager(), [
            'rules' => array_merge(yii::$app->getUrlManager()->rules, Category::getUrlRules())
        ]);
        yii::$app->getUrlManager()->init();

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