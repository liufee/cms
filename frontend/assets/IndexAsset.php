<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-08-12 22:37
 */

namespace frontend\assets;


class IndexAsset extends \yii\web\AssetBundle
{
    public $js = [
        'static/js/responsiveslides.min.js',
    ];

    public $depends = [
        'frontend\assets\AppAsset'
    ];
}