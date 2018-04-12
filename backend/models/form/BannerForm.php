<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-03 22:05
 */

namespace backend\models\form;

use common\helpers\Util;
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

    public function scenarios()
    {
        return ['default'=>['sort', 'status', 'integer', 'sign', 'target', 'link', 'desc', 'img'], 'delete'=>['id', 'sign']];
    }

    public static function findOne($id)
    {
        if( in_array(yii::$app->controller->action->id, ['banner-sort', 'banner-delete']) ){//删除,排序
            $model = parent::findOne(yii::$app->getRequest()->get('id'));
            $model->sign = $id;
            return $model;
        }else{
            return parent::findOne($id);
        }
    }

    public function delete()
    {
        return $this->save();
    }

    public function afterFind()
    {
        if(empty($this->value)) {
            $this->value = [];
        }else {
            $temp = json_decode($this->value, true);
            ArrayHelper::multisort($temp, 'sort');
            $this->value = $temp;
            $sign = yii::$app->getRequest()->get('sign', null);
            if($sign !== null) {
                /** @var $cdn \feehi\cdn\TargetAbstract */
                $cdn = yii::$app->get('cdn');
                foreach ($this->value as $value) {
                    if( $sign === $value['sign'] ){
                        $this->sign = $value['sign'];
                        $this->img = $cdn->getCdnUrl($value['img']);
                        $this->target = $value['target'];
                        $this->desc = $value['desc'];
                        $this->link = $value['link'];
                        $this->sort = $value['sort'];
                        $this->status = $value['status'];
                        break;
                    }
                }
            }
        }
    }

   public static function getBanners($id, $asArray=false)
    {
        $model = parent::findOne(['id'=>$id, 'type'=>self::TYPE_BANNER]);
        if( $model == '' ) throw new NotFoundHttpException("Cannot find id $id");
        /** @var $cdn \feehi\cdn\TargetAbstract */
        $cdn = yii::$app->get('cdn');
        $models = [];
        foreach ($model->value as $banner){
            $temp = [
                'id' => $id,
                'sign' => $banner['sign'],
                'img' => $cdn->getCdnUrl($banner['img']),
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

    public function beforeSave($insert)
    {
        $sign = yii::$app->getRequest()->get('sign', null);
        if( $sign === null && $this->sign ) $sign = $this->sign;//删除
        $options = [];
        $oldFullName = "";
        if( $sign !== null ){//修改
            foreach ($this->value as $key => $value){
                if( $value['sign'] === $sign ){
                    $oldFullName = $value['img'];
                }
            }
            if( $this->getScenario() === 'delete' ) $options['deleteOldFile'] = true;
        }
        Util::handleModelSingleFileUploadAbnormal($this, 'img', '@uploads/setting/banner/', $oldFullName, $options);
        $data = [
            'img' => $this->img,
            'target' => $this->target,
            'desc' => $this->desc,
            'link' => $this->link,
            'sort' => $this->sort,
            'status' => $this->status,
        ];
        if( $sign === null ){//新增
            $data['sign'] = uniqid();
            $temp = $this->value;
            $temp[] = $data;
            $this->value = $temp;
        }else{
            $temp = $this->value;
            foreach ($this->value as $key => $value){
                if( $value['sign'] === $sign ){
                    if( $this->getScenario() === 'delete'){
                        unset($temp[$key]);
                    }else {
                        $data['sign'] = $sign;
                        $temp[$key] = $data;
                    }
                    break;
                }
                if( count($this->value) - 1 === $key ) throw new NotFoundHttpException("Id $this->id does not exists sign $sign");
            }
            $this->value = $temp;
        }
        $this->value = json_encode($this->value);
        return parent::beforeSave(false);
    }

    public function getBannerType()
    {
        return $this->hasOne(self::className(), ['id' => 'id']);
    }
}