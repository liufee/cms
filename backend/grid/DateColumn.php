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

    public $format = ['datetime', 'php:Y-m-d H:m'];

    public $filter = "default";

    public $layerOptions = [];

    public $filterInputOptions = ["class" => "form-control date-time"];


    public function init()
    {
        parent::init();
        !isset($this->layerOptions['type']) && $this->layerOptions['type'] = 'datetime';
        !isset($this->layerOptions['range']) && $this->layerOptions['range'] = '~';
        !isset($this->layerOptions['theme']) && $this->layerOptions['theme'] = 'molv';
        !isset($this->layerOptions['max']) && $this->layerOptions['max'] = '0';
        !isset($this->layerOptions['calendar']) && $this->layerOptions['calendar'] = 'true';
    }

    protected function renderFilterCellContent()
    {
        $laydateJs =<<<str
            lay('.date-time').each(function(){
                laydate.render({
                    elem: this,
                    type: '{$this->layerOptions['type']}',
                    range: '{$this->layerOptions['range']}',
                    theme: '{$this->layerOptions['theme']}',
                    max: {$this->layerOptions['max']},
                    //显示公历
                    calendar: {$this->layerOptions['calendar']},
                    //选择完日期确定后回调事件
                    done: function(value, date, endDate){
                    setTimeout(function(){
                        $(this).val(value);
                        var e = $.Event("keydown");
                        e.keyCode = 13;
                        $(".date-time").trigger(e);
                    },100)
                }
                });
            });
str;
        yii::$app->getView()->registerJs($laydateJs, View::POS_END);

        if ($this->grid->filterModel->hasErrors($this->attribute)) {
            Html::addCssClass($this->filterOptions, 'has-error');
            $error = ' ' . Html::error($this->grid->filterModel, $this->attribute, $this->grid->filterErrorOptions);
        } else {
            $error = '';
        }
        return Html::activeTextInput($this->grid->filterModel, $this->attribute, $this->filterInputOptions) . $error;
    }
}