<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-03 22:05
 */

namespace backend\models\form;

use Yii;
use common\helpers\Util;
use common\libs\Constants;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class BannerForm extends \common\models\Options
{
    public $sign;

    public $img;

    public $target = Constants::TARGET_BLANK;

    public $link;

    public $sort = 0;

    public $status = Constants::Status_Enable;

    public $desc;

    public function init()
    {
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'beforeSaveEvent']);
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'beforeSaveEvent']);
        $this->on(self::EVENT_AFTER_FIND, [$this, 'afterFindEvent']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Sign'),
            'tips' => Yii::t('app', 'Description'),
            'img' => Yii::t('app', 'Image'),
            'target' => Yii::t('app', 'Target'),
            'link' => Yii::t('app', 'Jump Link'),
            'sort' => Yii::t('app', 'Sort'),
            'status' => Yii::t('app', 'Status'),
            'desc' => Yii::t('app', 'Description'),
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
        if( in_array(Yii::$app->controller->action->id, ['banner-sort', 'banner-delete']) ){//删除,排序
            $model = parent::findOne(Yii::$app->getRequest()->get('id'));
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

    public function afterFindEvent($event)
    {
        if(empty($event->sender->value)) {
            $event->sender->value = [];
        }else {
            $temp = json_decode($event->sender->value, true);
            ArrayHelper::multisort($temp, 'sort');
            $event->sender->value = $temp;
            $sign = Yii::$app->getRequest()->get('sign', null);
            if( $sign === null ){
                if( Yii::$app->getRequest()->getIsPost() && Yii::$app->controller->action->id === "banner-sort" ){
                    $condition = array_keys(Yii::$app->getRequest()->post()["sort"])[0];
                    $temp = json_decode($condition, true);
                    $sign = $temp['sign'];
                }
            }
            if($sign !== null) {
                /** @var $cdn \feehi\cdn\TargetAbstract */
                $cdn = Yii::$app->get('cdn');
                foreach ($event->sender->value as $value) {
                    if( $sign === $value['sign'] ){
                        $event->sender->sign = $value['sign'];
                        $event->sender->img = $cdn->getCdnUrl($value['img']);
                        $event->sender->target = $value['target'];
                        $event->sender->desc = $value['desc'];
                        $event->sender->link = $value['link'];
                        $event->sender->sort = $value['sort'];
                        $event->sender->status = $value['status'];
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
        $cdn = Yii::$app->get('cdn');
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

    public function beforeSaveEvent($event)
    {
        $sign = Yii::$app->getRequest()->get('sign', null);
        if( $sign === null ){
            if( Yii::$app->getRequest()->getIsPost() && Yii::$app->controller->action->id === "banner-sort" ){
                $condition = array_keys(Yii::$app->getRequest()->post()["sort"])[0];
                $temp = json_decode($condition, true);
                $sign = $temp['sign'];
            }
        }
        if( $sign === null && $event->sender->sign ) $sign = $event->sender->sign;//删除
        $options = [];
        $oldFullName = "";
        if( $sign !== null ){//修改
            foreach ($event->sender->value as $key => $value){
                if( $value['sign'] === $sign ){
                    $oldFullName = $value['img'];
                }
            }
            if( $event->sender->getScenario() === 'delete' ) $options['deleteOldFile'] = true;
        }
        Util::handleModelSingleFileUploadAbnormal($event->sender, 'img', '@uploads/setting/banner/', $oldFullName, $options);
        $data = [
            'img' => $event->sender->img,
            'target' => $event->sender->target,
            'desc' => $event->sender->desc,
            'link' => $event->sender->link,
            'sort' => $event->sender->sort,
            'status' => $event->sender->status,
        ];
        if( $sign === null ){//新增
            $data['sign'] = uniqid();
            $temp = $event->sender->value;
            $temp[] = $data;
            $event->sender->value = $temp;
        }else{
            $temp = $event->sender->value;
            foreach ($event->sender->value as $key => $value){
                if( $value['sign'] === $sign ){
                    if( $event->sender->getScenario() === 'delete'){
                        unset($temp[$key]);
                    }else {
                        $data['sign'] = $sign;
                        $temp[$key] = $data;
                    }
                    break;
                }
                if( count($event->sender->value) - 1 === $key ) throw new NotFoundHttpException("Id $this->id does not exists sign $sign");
            }
            $event->sender->value = $temp;
        }
        $event->sender->value = json_encode($event->sender->value);
    }

    public function getBannerType()
    {
        return $this->hasOne(self::className(), ['id' => 'id']);
    }
}