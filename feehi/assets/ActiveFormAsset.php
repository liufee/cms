<?php

namespace feehi\assets;

use yii\web\AssetBundle;

class ActiveFormAsset extends AssetBundle
{
    public $sourcePath = '@yii/assets';
    public $js = [
        'yii.activeForm.js',
    ];
    public $depends = [
        'feehi\assets\YiiAsset',
    ];
}
