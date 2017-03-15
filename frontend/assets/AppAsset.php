<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace frontend\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'static/css/style.css',
        'static/plugins/toastr/toastr.min.css',
    ];
    public $js = [
        'static/js/jquery.min.js',
        'static/js/jquery.js',
        'static/plugins/toastr/toastr.min.js',
    ];
    public $depends = [
        //'feehi\assets\YiiAsset',
        //'feehi\assets\BootstrapAsset',
    ];
}
