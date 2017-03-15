<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-01-04 09:44
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