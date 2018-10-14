<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-21 19:32
 */

namespace backend\grid;

use Yii;
use yii\helpers\Html;
use yii\web\View;

/**
 * @inheritdoc
 */
class DateColumn extends DataColumn
{
    public $headerOptions = ['width' => '120px'];

    public $filter = "default";

    public $format = ['datetime', 'php:Y-m-d H:i'];


    public function init()
    {
        parent::init();

        !isset($this->filterInputOptions['elem']) && $this->filterInputOptions['elem'] = 'this';
        !isset($this->filterInputOptions['type']) && $this->filterInputOptions['type'] = 'datetime';
        !isset($this->filterInputOptions['range']) && $this->filterInputOptions['range'] = '~';
        !isset($this->filterInputOptions['format']) && $this->filterInputOptions['format'] = 'yyyy-MM-dd HH:mm:ss';
        !isset($this->filterInputOptions['isInitValue']) && $this->filterInputOptions['isInitValue'] = 'false';
        !isset($this->filterInputOptions['min']) && $this->filterInputOptions['min'] = '1900-1-1';
        !isset($this->filterInputOptions['max']) && $this->filterInputOptions['max'] = '2099-12-31';
        !isset($this->filterInputOptions['trigger']) && $this->filterInputOptions['trigger'] = 'focus';
        !isset($this->filterInputOptions['show']) && $this->filterInputOptions['show'] = 'false';
        !isset($this->filterInputOptions['position']) && $this->filterInputOptions['position'] = 'absolute';
        !isset($this->filterInputOptions['zIndex']) && $this->filterInputOptions['zIndex'] = '66666666';
        !isset($this->filterInputOptions['showBottom']) && $this->filterInputOptions['showBottom'] = 'true';
        !isset($this->filterInputOptions['btns']) && $this->filterInputOptions['btns'] = "['clear', 'now', 'confirm']";
        !isset($this->filterInputOptions['lang']) && $this->filterInputOptions['lang'] = ( strpos( Yii::$app->language, 'en' ) === 0 ? 'en' : 'cn' );
        !isset($this->filterInputOptions['theme']) && $this->filterInputOptions['theme'] = 'molv';
        !isset($this->filterInputOptions['calendar']) && $this->filterInputOptions['calendar'] = 'true';
        !isset($this->filterInputOptions['mark']) && $this->filterInputOptions['mark'] = '{}';//json对象
        !isset($this->filterInputOptions['ready']) && $this->filterInputOptions['ready'] = 'function(date){}';//匿名函数
        !isset($this->filterInputOptions['change']) && $this->filterInputOptions['change'] = 'function(value, date, endDate){}';//匿名函数
        !isset($this->filterInputOptions['done']) && $this->filterInputOptions['done'] = 'function(value, date, endDate){}';//匿名函数
        $this->filterInputOptions['dateType'] = $this->filterInputOptions['type'];
        unset($this->filterInputOptions['type']);

        if (!isset($this->filterInputOptions['class'])) {
            $this->filterInputOptions['class'] = 'form-control date-time';
        }else{
            $this->filterInputOptions['class'] .= ' form-control date-time';
        }
    }

    protected function renderFilterCellContent()
    {

        if ($this->grid->filterModel->hasErrors($this->attribute)) {
            Html::addCssClass($this->filterOptions, 'has-error');
            $error = ' ' . Html::error($this->grid->filterModel, $this->attribute, $this->grid->filterErrorOptions);
        } else {
            $error = '';
        }
        return Html::activeTextInput($this->grid->filterModel, $this->attribute, $this->filterInputOptions) . $error;
    }
}