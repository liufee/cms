<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{

    public $baseUrl = '@web/admin';
    public $sourcePath = '@backend/web/static';
    public $css = [
        'css/bootstrap.min14ed.css?v=3.3.6',
        //'//cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.min.css',
        'css/font-awesome.min93e3.css?v=4.4.0',
        'css/animate.min.css',
        'css/style.min862f.css?v=4.1.0',
        'css/plugins/sweetalert/sweetalert.css',
        'js/plugins/layer/laydate/need/laydate.css',
        //'js/plugins/layer/laydate/skins/default/laydate.css'
        'css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css',
        'css/plugins/toastr/toastr.min.css',

    ];
    public $js = [
        'js/feehi.js',
        'js/plugins/sweetalert/sweetalert.min.js',
        'js/plugins/layer/laydate/laydate.js',
        'js/plugins/layer/layer.min.js',
        'js/plugins/prettyfile/bootstrap-prettyfile.js',
        'js/plugins/toastr/toastr.min.js',
    ];
    public $depends = [
        'feehi\assets\YiiAsset',
        'feehi\assets\BootstrapAsset',
    ];
}
