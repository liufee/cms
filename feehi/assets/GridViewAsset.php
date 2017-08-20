<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace feehi\assets;

class GridViewAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@yii/assets';

    public $js = [
        'yii.gridView.js',
    ];

    public $depends = [
        'feehi\assets\YiiAsset',
    ];
}
