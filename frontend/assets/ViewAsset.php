<?php
/**
 * Ahthor: lf
 * Email: job@feehi.com
 * Blog: http://blog.feehi.com
 * Date: 2016/8/1215:25
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