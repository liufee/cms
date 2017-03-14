<?php

namespace backend\assets;

use yii\web\AssetBundle;

class JstreeAsset extends AssetBundle
{
    public $baseUrl = '@web/admin';
    public $sourcePath = '@backend/web/static';
    public $css = [
        'plugins/jstree/themes/default/style.min.css',
    ];
    public $js = [
        'plugins/jstree/jstree.min.js',
    ];
    public $depends = [
        'feehi\assets\JqueryAsset',
    ];
}
