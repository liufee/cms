<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2019-01-06 12:47
 */

namespace backend\assets;

class WebuploaderAsset extends \yii\web\AssetBundle
{
    public $css = [
    	'css/plugins/webuploader/style.css',
        'css/plugins/webuploader/webuploader.css',
    ];
    public $js = [
        'js/plugins/webuploader/webuploader.min.js',
        'js/plugins/webuploader/init.js'
    ];
    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    public $basePath = "@web";

    public $sourcePath = '@backend/web/static/';
}
