<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-03 22:05
 */

namespace backend\models\form;

use common\helpers\Util;
use Exception;
use yii;
use common\libs\Constants;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class BannerForm extends \Common\models\Options
{
    public $sign;

    public $img;

    public $target = Constants::TARGET_BLANK;

    public $link;

    public $sort = 0;

    public $status = Constants::Status_Enable;

    public $desc;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => yii::t('app', 'Sign'),
            'tips' => yii::t('app', 'Description'),
            'img' => yii::t('app', 'Image'),
            'target' => yii::t('app', 'Target'),
            'link' => yii::t('app', 'Jump Link'),
            'sort' => yii::t('app', 'Sort'),
            'status' => yii::t('app', 'Status'),
            'desc' => yii::t('app', 'Description'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort', 'status'], 'integer'],
            [['sign', 'target', 'link', 'desc'], 'string'],
            [['img'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, webp'],
        ];
    }

    public function getBanners($id, $asArray=false)
    {
        $model = self::findOne(['id'=>$id, 'type'=>self::TYPE_BANNER]);
        if( $model == '' ) throw new NotFoundHttpException("Cannot find id $id");
        $banners = json_decode($model->value, true);
        if($banners == null) $banners = [];
        ArrayHelper::multisort($banners, 'sort');
        $models = [];
        foreach ($banners as $banner){
            $temp = [
                'id' => $model->id,
                'sign' => $banner['sign'],
                'img' => $banner['img'],
                'target' => $banner['target'],
                'desc' => $banner['desc'],
                'link' => $banner['link'],
                'sort' => $banner['sort'],
                'status' => $banner['status'],
            ];
            $models[$banner['sign']] = $asArray ? $temp : new self($temp);
        }
        return $models;
    }

    public function getBanner($sign)
    {
        $banners = $this->getBanners($this->id);
        $banners = ArrayHelper::index($banners, 'sign');
        if( isset($banners[$sign]) ) return $banners[$sign];
        throw new NotFoundHttpException("Cannot find id $this->id img $sign");
    }

    public function beforeSave($insert)
    {
        $this->id = yii::$app->getRequest()->get('id', '');
        $this->sign = yii::$app->getRequest()->get('id', null);
        if( $this->sign === null ){
            $oldFullName = "";
        }else{
            $banner = $this->getBanner(yii::$app->getRequest()->get('sign', ''));
            $oldFullName = $banner->img;
        }
        //Util::handleModelSingleFileUploadAbnormal($this, 'img', '@uploads/setting/banner/', $insert, $oldFullName);
        $data = [
            'sign' => $this->sign,
            'img' => $this->img,
            'target' => $this->target,
            'desc' => $this->desc,
            'link' => $this->link,
            'sort' => $this->sort,
            'status' => $this->status,
        ];
        if( $this->sign === null ){
            $data['sign'] = uniqid();
            $temp = [$data];
            $this->value = json_encode($temp);
        }else{
            $banners = $this->getBanners($this->id, true);
            if( !isset($banners[$this->sign]) ) throw new Exception("Id $this->id does not exists sign $this->sign");
            $banners[$this->sign] = $data;
            $temp = [];
            foreach ($banners as $banner){
                $temp[] = $banner;
            }

        }
        $this->value = json_encode($temp);
        return parent::beforeSave($insert);
    }

    public function getBannerType()
    {
        return $this->hasOne(self::className(), ['id' => 'id']);
    }
}