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

    public function afterFind()
    {
        if( empty( $this->value ) ) $this->value = "[]";
        $banners = json_decode($this->value, true);
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
        $this->value = $models;
        parent::afterFind();
    }

    public function beforeSave($insert)
    {
        /** @var array $banners */
        $banners = !is_array( $this->value ) ? [] : $this->value;
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
        $this->value = json_encode( $array );
        $this->type = self::TYPE_BANNER;
        return parent::beforeSave($insert);
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