<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

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
        'static/css/base.css',
        'static/css/index.css',
        'static/css/search.css',
        'static/css/view.css',
    ];
    public $js = [
        'static/js/jquery.min.js',
        'static/js/sliders.js',
        'static/js/nav.js',
        'static/js/up/jquery.js',
        'static/js/up/js.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
