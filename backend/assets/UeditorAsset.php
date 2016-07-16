<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class UeditorAsset extends AssetBundle
{
    public $baseUrl = '@web/admin';
    public $sourcePath = '@backend/web/static';
    public $jsOptions = ['position'=>View::POS_BEGIN];
    public $js = [
        'plugins/ueditor/ueditor.config.js',
        'plugins/ueditor/ueditor.all.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
