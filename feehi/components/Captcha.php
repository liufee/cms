<?php
/**
 * Created by PhpStorm.
 * User: f
 * Date: 2017/1/4
 * Time: 下午9:37
 */
namespace feehi\components;

use feehi\assets\CaptchaAsset;
use yii\helpers\Json;

class Captcha extends \yii\captcha\Captcha
{
    public function registerClientScript()
    {
        $options = $this->getClientOptions();
        $options = empty($options) ? '' : Json::htmlEncode($options);
        $id = $this->imageOptions['id'];
        $view = $this->getView();
        CaptchaAsset::register($view);
        $view->registerJs("jQuery('#$id').yiiCaptcha($options);");
    }

}