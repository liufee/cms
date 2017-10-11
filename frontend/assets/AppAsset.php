<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace frontend\assets;

class AppAsset extends \yii\web\AssetBundle
{

    public $css = [
        'static/css/style.css',
        'static/plugins/toastr/toastr.min.css',
    ];

    public $js = [
        'static/js/index.js',
        'static/plugins/toastr/toastr.min.js',
    ];

    public $depends = [
        'feehi\assets\JqueryAsset',
    ];

}
