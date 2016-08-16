<?php

namespace feehi\assets;

use yii\web\AssetBundle;

class YiiAsset extends AssetBundle
{
    public $sourcePath = '@yii/assets';
    public $js = [
        'yii.js',
    ];
    public $depends = [
        'feehi\assets\JqueryAsset',
    ];
}
