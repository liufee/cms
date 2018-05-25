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
        parent::init();
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'beforeSaveEvent']);
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'beforeSaveEvent']);
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

    public function beforeSaveEvent($event)
    {
        $event->sender->type = self::TYPE_BANNER;
    }

}