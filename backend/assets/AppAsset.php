<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\assets;

use yii;

class AppAsset extends \yii\web\AssetBundle
{

    public function init()
    {
        parent::init();
        if( yii::$app->getRequest()->getBaseUrl() !== "" ){
            $this->sourcePath = '@backend/web';
        }
    }

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
        'static/css/plugins/toastr/toastr.min.css',

    ];

    public $js = [
        'static/js/feehi.js',
        'static/js/plugins/sweetalert/sweetalert.min.js',
        'static/js/plugins/layer/laydate/laydate.js',
        'static/js/plugins/layer/layer.min.js',
        'static/js/plugins/prettyfile/bootstrap-prettyfile.js',
        'static/js/plugins/toastr/toastr.min.js',
    ];

    public $depends = [
        'feehi\assets\YiiAsset',
        'feehi\assets\BootstrapAsset',
    ];
}
