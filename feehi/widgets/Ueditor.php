<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/31
 * Time: 15:32
 */
namespace feehi\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

class Ueditor extends Widget
{
    public $name = '';
    public $content = '';
    public $ueConfig = '{"initialFrameHeight":400,"overflow":"scroll"}';

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $view = $this->getView();
        $view->registerjSFile("@web/static/plugins/ueditor/ueditor.config.js", ['position'=>View::POS_BEGIN]);
        $view->registerJsFile("@web/static/plugins/ueditor/ueditor.all.js", ['position'=>View::POS_BEGIN]);
        $html =<<<EOF
             <script type="text/javascript">
                 var ue = UE.getEditor('{$this->name}',$this->ueConfig);
             </script>
            <script id="{$this->name}" name="{$this->name}" type="text/plain">{$this->content}</script>
EOF;
        return $html;
    }
}