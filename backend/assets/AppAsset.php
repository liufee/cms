<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\assets;


/**
 * 重要提示：启用配置后，修改此处的js/css将不会生效
 * 需要在backend/config/main.php中assetManager.bundles处修改配置
 * 主要用于测试环境走本地文件,正式环境配置成cdn
 *
 * Class AppAsset
 * @package backend\assets
 */
class AppAsset extends \yii\web\AssetBundle
{

    public $css = [
        'static/css/bootstrap.min14ed.css?v=3.3.6',
        //'//cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.min.css',
        'static/css/font-awesome.min93e3.css?v=4.4.0',
        'static/css/animate.min.css',
        'static/css/style.min862f.css?v=4.1.0',
        'static/js/plugins/layer/laydate/theme/default/laydate.css',
        //'js/plugins/layer/laydate/skins/default/laydate.css'
        'static/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css',
        'static/css/plugins/toastr/toastr.min.css',
        'static/css/plugins/chosen/chosen.css',
        'static/css/feehi.css',

    ];

    public $js = [
        'static/js/feehi.js',
        'static/js/plugins/layer/laydate/laydate.js',
        'static/js/plugins/layer/layer.min.js',
        'static/js/plugins/prettyfile/bootstrap-prettyfile.js',
        'static/js/plugins/toastr/toastr.min.js',
        'static/js/plugins/chosen/chosen.jquery.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
