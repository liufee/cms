<?php
/**
 * Created by PhpStorm.
 * User: f
 * Date: 2016/10/19
 * Time: 下午10:05
 */
namespace install\assets;

use yii\web\AssetBundle;

class LayerAsset extends AssetBundle
{

    public $baseUrl = '@web';
    public $sourcePath = '@backend/web/static';
    public $css = [
    ];
    public $js = [
        'js/plugins/layer/layer.min.js',
    ];
    public $depends = [
        'feehi\assets\JqueryAsset',
    ];
}