<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-01-19 09:31
 */

namespace backend\models\form;

use Yii;
use common\models\Options;

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
                'message' => Yii::t('app', 'Must begin with alphabet and can only includes alphabet,_,and number')
            ],
            [['name', 'tips'], 'required'],
        ];
    }

    public function beforeSave($insert)
    {
        $this->type = Options::TYPE_BANNER;
        if( $this->value === null ){
            $this->value = "[]";
        }
        return parent::beforeSave($insert);
    }

    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'tips' => Yii::t('app', 'Description'),
        ];
    }

    public function beforeDelete()
    {
        if( !empty($this->value) ) {
            $this->addError("id", Yii::t('app', 'Delete failed, banner existed'));
            return false;
        }
        return parent::beforeDelete();
    }

}