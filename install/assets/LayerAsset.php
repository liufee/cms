<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-10-19 22:05
 */

namespace install\assets;

use yii\web\AssetBundle;

class LayerAsset extends AssetBundle
{

    public $baseUrl = '@web';
    public $sourcePath = '@backend/web/static';
    public $css = [];
    public $js = [
        'js/plugins/layer/layer.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}