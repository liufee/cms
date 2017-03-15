<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-08-12 15:25
 */

namespace frontend\assets;

use yii\web\AssetBundle;

class ViewAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'static/syntaxhighlighter/styles/shCoreDefault.css'
    ];
    public $js = [
        'static/syntaxhighlighter/scripts/shCore.js',
        'static/syntaxhighlighter/scripts/shBrushJScript.js',
        'static/syntaxhighlighter/scripts/shBrushPython.js',
        'static/syntaxhighlighter/scripts/shBrushPhp.js',
        'static/syntaxhighlighter/scripts/shBrushJava.js',
        'static/syntaxhighlighter/scripts/shBrushCss.js',
    ];
    public $depends = [
        'frontend\assets\AppAsset'
    ];
}