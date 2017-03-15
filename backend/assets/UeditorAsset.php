<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\assets;

use yii\web\AssetBundle;

class UeditorAsset extends AssetBundle
{
    public $sourcePath = "@backend/web/static/plugins/ueditor";

    public $js = [
        'ueditor.all.min.js',
    ];

    public $css = [];


    public $publishOptions = [
        'except' => [
            'php/',
            'index.html',
            '.gitignore'
        ]
    ];

}
