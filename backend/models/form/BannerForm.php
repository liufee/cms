<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-03 22:05
 */

namespace backend\models\form;

use Yii;
use common\libs\Constants;
use yii\web\UploadedFile;

class BannerForm extends \common\models\Options
{
    public $id;

    public $tips;

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

    public function beforeValidate()
    {
        if ($this->img !== "0") {//为0表示需要删除图片，Util::handleModelSingleFileUpload()会有判断删除图片
            $this->img = UploadedFile::getInstance($this, "img");
        }
        return parent::beforeValidate();
    }

    public function setAttributes($values, $safeOnly = true)
    {
        if( is_string($values) ){
            $banner = json_decode($values, true);
            $this->sign = $banner['sign'];
            $this->img = $banner['img'];
            $this->target = $banner['target'];
            $this->desc = $banner['desc'];
            $this->link = $banner['link'];
            $this->sort = $banner['sort'];
            $this->status = $banner['status'];
        }else{
            parent::setAttributes($values, $safeOnly);
        }
    }

    public function getValue()
    {
        return [
            'sign' => $this->sign ? $this->sign : uniqid(),
            'img' => $this->img,
            'target' => $this->target,
            'desc' => $this->desc,
            'link' => $this->link,
            'sort' => $this->sort,
            'status' => $this->status,
        ];
    }
}