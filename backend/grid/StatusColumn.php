<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-21 19:36
 */

namespace backend\grid;

use Closure;
use yii;
use InvalidArgumentException;
use common\libs\Constants;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @inheritdoc
 */
class StatusColumn extends DataColumn
{

    public $format = 'raw';

    public $attribute = 'status';

    public $headerOptions = ['width' => '25px'];

    public $url = '';

    public $text = '';

    public $aOptions = [];

    public $yesClass = "label-primary";

    public $noClass = "label-default";

    public $formName = "";

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if( empty($this->aOptions) ){
            if( $this->url !== false ){
                $this->aOptions = array_merge($this->aOptions, [
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]);
            }
        }

        if( !$this->content && $this->content !== false ) {
            $this->content = function ($model, $key, $index, $gridView) {
                /* @var $model array|yii\db\ActiveRecord */
                $field = $this->attribute;
                if ($this->text === '') {
                    $text = Constants::getYesNoItems($model[$field]);
                } else {
                    if ($this->text instanceof Closure) {
                        $text = call_user_func($this->text, $model, $key, $index, $gridView);
                    } else {
                        $text = $this->text;
                    }
                }
                if (!is_string($text)) throw new InvalidArgumentException("No status valued {$model[$field]}");
                if ($this->url === false) {
                    $url = '';
                } else {
                    if ($this->url == '') {
                        $url = Url::to(['update', 'id' => $model['id']]);
                    } else {
                        if ($this->url instanceof Closure) {
                            $url = call_user_func($this->url, $model, $key, $index, $gridView);
                        } else {
                            $url = $this->url;
                        }
                    }
                }
                $aOptions = [];
                if ($url != '') {
                    if (!isset($this->aOptions['data-params'])) {
                        $aOptions = array_merge([
                            'data-params' => [
                                $this->formName ? $this->formName : (strpos(strrev($model->formName()), 'hcraeS') === 0 ? strrev(substr(strrev($model->formName()), 6)) : $model->formName()) . "[{$field}]" => $model[$field] == Constants::YesNo_Yes ? Constants::YesNo_No : Constants::YesNo_Yes],
                        ], $this->aOptions, $aOptions);
                    }
                    if (!isset($this->aOptions['class'])) {
                        $class = $model[$field] == Constants::YesNo_Yes ? $this->yesClass : $this->noClass;
                        $aOptions = array_merge([
                            'class' => 'label ' . $class,
                        ], $this->aOptions, $aOptions);
                    }
                    if (!isset($this->aOptions['data-confirm'])) {
                        $aOptions = array_merge([
                            'data-confirm' => $model[$field] == Constants::YesNo_Yes ? Yii::t('app', 'Are you sure you want to disable this item?') : Yii::t('app', 'Are you sure you want to enable this item?'),
                        ], $this->aOptions, $aOptions);
                    }
                }
                return Html::a($text, $url, $aOptions);
            };
        }
    }
}