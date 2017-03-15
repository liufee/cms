<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

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
