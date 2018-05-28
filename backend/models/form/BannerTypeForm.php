<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-01-19 09:31
 */

namespace backend\models\form;

use Yii;

class BannerTypeForm extends \common\models\Options
{

    public function init()
    {
        $this->on(self::EVENT_AFTER_FIND, [$this, 'afterFindEvent']);
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'beforeSaveEvent']);
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'beforeSaveEvent']);
        $this->on(self::EVENT_BEFORE_DELETE, [$this, 'beforeDeleteEvent']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'unique'],
            [
                ['name'],
                'match',
                'pattern' => '/^[a-zA-Z][0-9_]*/',
                'message' => Yii::t('app', 'Must begin with alphabet and can only includes alphabet,_,and number')
            ],
            [['name', 'tips'], 'required'],
            [['value'], 'default', 'value' => ''],
        ];
    }

    public function attributeLabels()
    {
        $attributeLabels = parent::attributeLabels();
        $attributeLabels['tips'] = Yii::t('app', 'Description');
        return $attributeLabels;
    }

    public function afterFindEvent($event)
    {
        if( empty( $event->sender->value ) ) $event->sender->value = "[]";
        $banners = json_decode($event->sender->value, true);
        /** @var $cdn \feehi\cdn\TargetAbstract */
        $cdn = Yii::$app->get('cdn');
        $models = [];
        foreach ($banners as $banner){
            $temp = [
                'sign' => $banner['sign'],
                'img' => $cdn->getCdnUrl($banner['img']),
                'target' => $banner['target'],
                'desc' => $banner['desc'],
                'link' => $banner['link'],
                'sort' => $banner['sort'],
                'status' => $banner['status'],
            ];
            $models[$banner['sign']] = new BannerForm($temp);
        }
        $event->sender->value = $models;
    }

    public function beforeSaveEvent($event)
    {
        $banners = $event->sender->value;
        /** @var $cdn \feehi\cdn\TargetAbstract */
        $cdn = Yii::$app->get('cdn');
        $array = [];
        foreach ($banners as $banner){
            $temp = [
                'sign' => $banner['sign'],
                'img' => str_replace($cdn->host, '', $banner['img']),
                'target' => $banner['target'],
                'desc' => $banner['desc'],
                'link' => $banner['link'],
                'sort' => $banner['sort'],
                'status' => $banner['status'],
            ];
            $array[] = $temp;
        }
        $event->sender->value = json_encode( $array );
        $event->sender->type = self::TYPE_BANNER;
    }

    public function beforeDeleteEvent($event)
    {
        if( !empty($event->sender->value) ) {
            $event->sender->addError("id", Yii::t('app', 'Delete failed, banner existed'));
            $event->isValid = false;
        }
    }

}