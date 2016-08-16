<?php
/**
 * Created by PhpStorm.
 * User: f
 * Date: 16/8/12
 * Time: 下午10:37
 */
namespace frontend\assets;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'static/js/responsiveslides.min.js',
    ];
    public $depends = [
        'frontend\assets\AppAsset'
    ];
}