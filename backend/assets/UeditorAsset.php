<?php
/**
 * UEditor Widget扩展
 *
 * @author xbzbing<xbzbing@gmail.com>
 * @link www.crazydb.com
 *
 * UEditor版本v1.4.3.1
 * Yii版本2.0+
 */
namespace backend\assets;

use yii;
use yii\web\AssetBundle;

/**
 * Class UEditorAsset
 * 负责UEditor的资源文件引入。
 * 由于bower上的源码是纯源码，需要用grunt打包后才能使用，因此扩展自带了1.4.3版本的UEditor核心文件。
 *
 * @package crazydb\ueditor
 */
class UeditorAsset extends AssetBundle {

    /**
     * UEditor路径
     * @var
     */
    public $sourcePath = "@backend/web/static/plugins/ueditor";

    /**
     * UEditor加载需要的JS文件。
     * ueditor.config.js中是默认配置项，不建议直接引入。
     * @var array
     */
    public $js = [
        'ueditor.all.min.js',
    ];

    /**
     * UEditor加载需要的CSS文件。
     * UEditor 会自动加载默认皮肤，CSS这里不必指定
     *
     * @var array
     */
    public $css = [];


    public $publishOptions = [
        'except' => [
            'php/',
            'index.html',
            '.gitignore'
        ]
    ];

}
