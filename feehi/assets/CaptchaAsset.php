<?php
/**
 * Created by PhpStorm.
 * User: f
 * Date: 2017/1/4
 * Time: 下午9:44
 */
namespace feehi\assets;

class CaptchaAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@yii/assets';
    public $js = [
        'yii.captcha.js',
    ];
    public $depends = [
        'feehi\assets\YiiAsset',
    ];
}