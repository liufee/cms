<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-01-19 09:31
 */

namespace backend\models\form;

use yii;

class BannerTypeForm extends \common\models\Options
{
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
                'message' => yii::t('app', 'Must begin with alphabet and can only includes alphabet,_,and number')
            ],
            [['name', 'tips'], 'required'],
            [['value'], 'default', 'value' => ''],
        ];
    }

    public function attributeLabels()
    {
        $attributeLabels = parent::attributeLabels();
        $attributeLabels['tips'] = yii::t('app', 'Description');
        return $attributeLabels;
    }

    public function beforeSave($insert)
    {
        $this->type = self::TYPE_BANNER;
        return parent::beforeSave($insert);
    }

}