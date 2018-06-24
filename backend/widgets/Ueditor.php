<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\widgets;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use backend\assets\UeditorAsset;

class Ueditor extends \yii\widgets\InputWidget
{
    /**
     * 生成的ueditor对象的名称，默认为editor。
     * 主要用于同一个页面的多个editor实例的管理。
     *
     * @var string
     */
    public $name;

    /**
     * UEditor配置
     *
     * @var array
     */
    public $config = [];

    /**
     * 初始化一些配置。
     * 由于不引入config.js文件，因此需要手动配置一些东西。
     */
    public function init()
    {

        parent::init();

        //注册资源文件
        $asset = UeditorAsset::register($this->getView());

        //设置UEditor实例的名字
        if (! $this->name) {
            $this->name = $this->hasModel() ? $this->model->formName() . '_' . $this->attribute : 'ueditor_' . $this->id;
        }

        //常用配置项
        if (empty($this->config['UEDITOR_HOME_URL'])) {
            $this->config['UEDITOR_HOME_URL'] = $asset->baseUrl . '/';
        }

        if (empty($this->config['serverUrl'])) {
            $this->config['serverUrl'] = Url::to(['/assets/ueditor']);
        } elseif (is_array($this->config['serverUrl'])) {
            $this->config['serverUrl'] = Url::to($this->config['serverUrl']);
        }

        if (empty($this->config['lang'])) {
            $this->config['lang'] = 'zh-cn';
        }

        if (empty($this->config['initialFrameHeight'])) {
            $this->config['initialFrameHeight'] = 400;
        }

        if (empty($this->config['initialFrameWidth'])) {
            $this->config['initialFrameWidth'] = '100%';
        }

        if (empty($this->config['enableAutoSave'])) {
            $this->config['enableAutoSave'] = false;
        }

        //扩展默认不直接引入config.js文件，因此需要自定义配置项.
        if (empty($this->config['toolbars'])) {
            //为了避免每次使用都输入乱七八糟的按钮，这里预先定义一些常用的编辑器按钮。
            //这是一个丑陋的二维数组
            $this->config['toolbars'] = [
                [
                    'fullscreen',
                    'source',
                    'undo',
                    'redo',
                    '|',
                    'customstyle',
                    'paragraph',
                    'fontfamily',
                    'fontsize'
                ],
                [
                    'bold',
                    'italic',
                    'underline',
                    'fontborder',
                    'strikethrough',
                    'superscript',
                    'subscript',
                    'removeformat',
                    'formatmatch',
                    'autotypeset',
                    'blockquote',
                    'pasteplain',
                    '|',
                    'forecolor',
                    'backcolor',
                    'insertorderedlist',
                    'insertunorderedlist',
                    '|',
                    'rowspacingtop',
                    'rowspacingbottom',
                    'lineheight',
                    '|',
                    'directionalityltr',
                    'directionalityrtl',
                    'indent',
                    '|'
                ],
                [
                    'justifyleft',
                    'justifycenter',
                    'justifyright',
                    'justifyjustify',
                    '|',
                    'link',
                    'unlink',
                    '|',
                    'insertimage',
                    'emotion',
                    'scrawl',
                    'insertvideo',
                    'music',
                    'attachment',
                    'map',
                    'insertcode',
                    'pagebreak',
                    '|',
                    'horizontal',
                    'inserttable',
                    '|',
                    'print',
                    'preview',
                    'searchreplace',
                    'help'
                ]
            ];
        }
    }

    /**
     * 输出widget页面，注册相关JS代码。
     */
    public function run()
    {

        $id = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->id;

        $config = Json::encode($this->config);

        //ready部分代码，是为了缩略图管理。UEditor本身就很大，在后台直接加载大文件图片会很卡。
        $script = <<<UEDITOR
    var {$this->name} = UE.getEditor('{$id}',{$config});
    {$this->name}.ready(function(){
        this.addListener( "beforeInsertImage", function ( type, imgObjs ) {
            for(var i=0;i < imgObjs.length;i++){
                imgObjs[i].src = imgObjs[i].src.replace(".thumbnail","");
            }
        });
    });
UEDITOR;

        $this->getView()->registerJs($script);

        if ($this->hasModel()) {
            return Html::activeTextarea($this->model, $this->attribute);
        } else {
            return Html::textarea($this->name, $this->value, ['id' => $id]);
        }
    }
}
