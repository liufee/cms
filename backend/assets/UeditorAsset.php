<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\assets;

use yii;

class UeditorAsset extends yii\web\AssetBundle
{

    public $basePath = "@web";

    public $sourcePath = '@backend/web/static/js/plugins/ueditor/';

    public $js = [
        'ueditor.all.min.js',
    ];

    public $publishOptions = [
        'except' => [
            'php/',
            'index.html',
            '.gitignore'
        ]
    ];

}
