<?php

namespace feehi\assets;

use yii\web\AssetBundle;

class GridViewAsset extends AssetBundle
{
    public $sourcePath = '@yii/assets';
    public $js = [
        'yii.gridView.js',
    ];
    public $depends = [
        'feehi\assets\YiiAsset',
    ];
}
