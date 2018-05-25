<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-05 12:47
 */

namespace backend\models\form;

use common\helpers\Util;
use Yii;
use common\libs\Constants;

class AdForm extends \Common\models\Options
{
    public $ad;

    public $link;

    public $desc;

    public $target = Constants::TARGET_BLANK;

    public $created_at;

    public $updated_at;


    public function init()
    {
        parent::init();
        $this->on(self::EVENT_AFTER_FIND, [$this, 'afterFindEvent']);
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'beforeSaveEvent']);
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'beforeSaveEvent']);
        $this->on(self::EVENT_AFTER_DELETE, [$this, 'afterDeleteEvent']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Sign'),
            'input_type' => Yii::t('app', 'Ad Type'),
            'tips' => Yii::t('app', 'Description'),
            'ad' => Yii::t('app', 'Ad'),
            'link' => Yii::t('app', 'Jump Link'),
            'desc' => Yii::t('app', 'Ad Explain'),
            'autoload' => Yii::t('app', 'Status'),
            'sort' => Yii::t('app', 'Sort'),
            'target' => Yii::t('app', 'Target'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
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
            [['name', 'tips', 'input_type'], 'required'],
            [['sort', 'autoload'], 'integer'],
            [[ 'link', 'target', 'desc'], 'string'],
            [['ad'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, webp'],
        ];
    }

    public function beforeSaveEvent($event)
    {
        $event->sender->type = self::TYPE_AD;
        if( $event->sender->input_type == Constants::AD_TEXT ){
            $oldInput = $event->sender->getOldAttribute('input_type');
            if( $oldInput != Constants::AD_TEXT ){//删除旧广告文件
                $text = $event->sender->ad;
                Util::handleModelSingleFileUploadAbnormal($event->sender, 'ad', '@uploads/setting/ad/', $event->sender->getOldAttribute('ad'), ['deleteOldFile'=>true]);
                $event->sender->ad = $text;
            }
        }else {
            Util::handleModelSingleFileUploadAbnormal($event->sender, 'ad', '@uploads/setting/ad/', $event->sender->getOldAttribute('ad'));
        }

        $value = [
            'ad' => $event->sender->ad,
            'link' => $event->sender->link,
            'target' => $event->sender->target,
            'desc' => $event->sender->desc,
            'created_at' => $event->sender->getIsNewRecord() ? time() : $event->sender->created_at,
            'updated_at' => time(),
        ];
        $event->sender->value = json_encode($value);
    }

    public function afterFindEvent($event)
    {
        $value = json_decode($event->sender->value);
        if( $event->sender->input_type !== Constants::AD_TEXT){
            /** @var $cdn \feehi\cdn\TargetAbstract */
            $cdn = Yii::$app->get('cdn');
            $event->sender->ad = $cdn->getCdnUrl($value->ad);
        }else{
            $event->sender->ad = $value->ad;
        }
        $event->sender->link = $value->link;
        $event->sender->desc = $value->desc;
        $event->sender->target = $value->target;
        $event->sender->updated_at = $value->updated_at;
        $event->sender->created_at = $value->created_at;
        $event->sender->setOldAttributes([
            'id' => $event->sender->id,
            'name' => $event->sender->name,
            'value' => $event->sender->value,
            'input_type' => $event->sender->input_type,
            'autoload' => $event->sender->autoload,
            'tips' => $event->sender->tips,
            'sort' => $event->sender->sort,
            'ad' => $value->ad,
            'link' => $value->link,
            'desc' => $value->desc,
            'target' => $value->target,
            'created_at' => $value->created_at,
            'updated_at' => $value->updated_at,
        ]);
    }

    public function afterDeleteEvent($event)
    {
        if( $event->sender->input_type != Constants::AD_TEXT ){
            $file = Yii::getAlias('@frontend/web') . $event->sender->ad;
            if( file_exists($file) && is_file($file) ) unlink($file);
        }
    }
}