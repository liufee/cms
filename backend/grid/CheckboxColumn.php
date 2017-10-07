<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-14 21:53
 */

namespace backend\grid;

use Closure;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * @inheritdoc
 */
class CheckboxColumn extends \yii\grid\CheckboxColumn
{

    public $width = '10px';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (! isset($this->headerOptions['width'])) {
            $this->headerOptions['width'] = $this->width;
        }
    }

    /**
     * @inheritdoc
     */
    protected function renderHeaderCellContent()
    {
        if ($this->header !== null || !$this->multiple) {
            return parent::renderHeaderCellContent();
        } else {
            static $i = 1;
            $unique = uniqid() . $i;
            $i++;
            $for = 'inlineCheckbox' . $unique;
            $options['id'] = $for;
            $options['class'] = 'select-on-check-all';
            return "<span class=\"checkbox checkbox-success checkbox-inline\">" . Html::checkbox($this->getHeaderCheckBoxName(), false, $options) . "<label for='{$for}'></label></span>";
        }
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->checkboxOptions instanceof Closure) {
            $options = call_user_func($this->checkboxOptions, $model, $key, $index, $this);
        } else {
            $options = $this->checkboxOptions;
        }

        if (!isset($options['value'])) {
            $options['value'] = is_array($key) ? Json::encode($key) : $key;
        }

        if ($this->cssClass !== null) {
            Html::addCssClass($options, $this->cssClass);
        }

        static $i = 1;
        $unique = uniqid() . $i;
        $i++;
        $for = 'inlineCheckbox' . $unique;
        $options['id'] = $for;
        return "<span class=\"checkbox checkbox-success checkbox-inline\">" . Html::checkbox($this->name, !empty($options['checked']), $options) . "<label for='{$for}'></label></span>";
    }

}