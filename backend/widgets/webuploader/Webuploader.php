<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2019-01-06 12:47
 */

namespace backend\widgets\webuploader;

use Yii;
use backend\assets\WebuploaderAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

class Webuploader extends \yii\widgets\InputWidget
{
    public $id;
    public $chooseButtonClass = ['class' => 'btn-white'];
    public $defaultImage;
    public $defaultUploadUrl = "assets/webuploader";
    public $mode = "multi";//模式，默认多图上传，single单图上传

    private $_view;
    private $_hashVar;
    private $_config;

    public function init ()
    {
        /** @var $cdn \feehi\cdn\TargetAbstract */
        $cdn = Yii::$app->cdn;
        $baseUrl = $cdn->host;
        $this->defaultImage = $baseUrl . 'static/images/none.jpg';;
        $this->_view = $this->getView();
        empty( $this->id ) && $this->id = uniqid();
        $this->initOptions();
        $this->initConfig();
        $this->registerClientScript();
    }

    public function run ()
    {
        if ($this->hasModel()) {
            $model = $this->model;
            $attribute = $this->attribute;

            // 单图
            if ( isset($this->_config['single']) && $this->_config['single']) {
                $html = $this->renderInput($model, $attribute);
                $html .= $this->renderImage($model, $attribute);
            } else {// 多图
                $html = $this->renderMultiInput($model, $attribute);
                $html .= $this->renderMultiImage($model, $attribute);
            }
            
            return $html;
        }
    }

    public function initOptions ()
    {
        $this->_hashVar = "webuploader_" . hash('crc32', $this->id);
    }

    public function initConfig ()
    {
        $this->_config = [
            'server' => Url::toRoute($this->defaultUploadUrl),
            'modal_id' => $this->_hashVar,
            'pick' =>[
                'multiple' => [],
            ],
        ];
        if ( !isset($this->clientOptions['csrf']) || $this->clientOptions['csrf'] !== false) {
            $this->_config['formData'][Yii::$app->getRequest()->csrfParam] = Yii::$app->getRequest()->getCsrfToken();
        }
        $config = Json::htmlEncode($this->_config);
        $js = <<<JS
            var {$this->_hashVar} = {$config};
            $('#{$this->_hashVar}').webupload_fileinput({$this->_hashVar});
JS;
        $this->_view->registerJs($js);
    }

    public function registerClientScript ()
    {
        WebuploaderAsset::register($this->_view);
    }

    public function renderInput ($model, $attribute)
    {
        Html::addCssClass($this->chooseButtonClass, "btn {$this->_hashVar}");
        $eles = [];
        $eles[] = Html::tag('span', Html::button('选择图片', $this->chooseButtonClass), ['class' => 'input-group-btn']);
        $eles[] = Html::activeTextInput($model, $attribute, ['class' => 'form-control']);


        return Html::tag('div', implode("\n", $eles), ['class' => 'input-group']);
    }

    public function renderMultiInput ($model, $attribute)
    {
        $inputName = Html::getInputName($model, $attribute);
        $eles = [];
        $eles[] = Html::hiddenInput($inputName, null);
        $eles[] = Html::tag('span', Html::button('选择图片', ['class'=>'btn btn-white']), ['class' => 'input-group-btn']);
        $eles[] = Html::textInput($attribute, null, ['class' => 'form-control']);

        return Html::tag('div', implode("\n", $eles), ['class' => 'input-group ' . $this->_hashVar]);
    }

    /**
     * render html body-image
     */
    public function renderImage ($model, $attribute)
    {
        $src = $this->defaultImage;
        $eles = [];
        if (($value = $model->$attribute)) {
            $src = $this->_validateUrl($value) ? $value : Yii::$app->params['site']['url'] . rtrim($value, "/");
        }
        $eles[] = Html::img($src, ['class' => 'img-responsive img-thumbnail cus-img']);
        $eles[] = Html::tag('em', 'x', ['class' => 'close delImage', 'title' => '删除这张图片']);

        return Html::tag('div', implode("\n", $eles), ['class' => 'input-group', 'style' => 'margin-top:.5em;']);
    }

    /**
     * render html body-image-muitl
     */
    public function renderMultiImage ($model, $attribute)
    {
        /**
         * @var $srcTmp string like this: src1,src2...srcxxx
         */
        $srcTmp = $model->$attribute;
        $items = [];
        if ($srcTmp) {
            is_string($srcTmp) && $srcTmp = explode(",", $srcTmp);
            !is_array($srcTmp) && $srcTmp = [$srcTmp];
            $inputName = Html::getInputName($model, $attribute);
            foreach ($srcTmp as $k => $v) {
                $dv = $this->_validateUrl($v) ? $v : Yii::$app->params['site']['url'] . $v;
                $src = $v ? $dv : $this->defaultImage;
                $eles = [];
                $eles[] = Html::img($src, ['class' => 'img-responsive img-thumbnail cus-img']);
                $eles[] = Html::hiddenInput($inputName . "[]", $v);
                $eles[] = Html::tag('em', 'x', ['class' => 'close delMultiImage', 'title' => '删除这张图片']);
                $items[] = Html::tag('div', implode("\n", $eles), ['class' => 'multi-item']);
            }
        } 

        return Html::tag('div', implode("\n", $items), ['class' => 'input-group multi-img-details']);
    }

    private function _validateUrl ($value)
    {
        $pattern = '/^{schemes}:\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(?::\d{1,5})?(?:$|[?\/#])/i';
        $validSchemes = ['http', 'https'];
        $pattern = str_replace('{schemes}', '(' . implode('|', $validSchemes) . ')', $pattern);
        if (!preg_match($pattern, $value)) {
            return false;
        }
        return true;
    }
}