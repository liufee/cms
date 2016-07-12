<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'static/css/bootstrap.min14ed.css?v=3.3.6',
        //'//cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.min.css',
        'static/css/font-awesome.min93e3.css?v=4.4.0',
        'static/css/animate.min.css',
        'static/css/style.min862f.css?v=4.1.0',
        'static/css/plugins/sweetalert/sweetalert.css',
        'static/js/plugins/layer/laydate/need/laydate.css',
        //'js/plugins/layer/laydate/skins/default/laydate.css'
        'static/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css',

    ];
    public $js = [
        'static/js/feehi.js',
        'static/js/plugins/sweetalert/sweetalert.min.js',
        'static/js/plugins/layer/laydate/laydate.js',
        'static/js/plugins/layer/layer.min.js',
        'static/js/plugins/prettyfile/bootstrap-prettyfile.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
