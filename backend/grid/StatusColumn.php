<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-21 19:36
 */

namespace backend\grid;

use yii;
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

    public $label = 'label';

    public $headerOptions = ['width' => '25px'];

    public $url = '';

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

        if( $this->label == 'label' ){
            $this->label = yii::t('app', 'Status');
        }

        if( empty($this->aOptions) ){
            if( $this->url !== false ){
                $this->aOptions = array_merge($this->aOptions, [
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]);
            }
        }

        $this->content = function ($model, $key, $index, $gridView) {
            /* @var $model array|yii\db\ActiveRecord */
            $field = $this->attribute;
            $text = Constants::getYesNoItems($model[$field]);
            if( $this->url === false ){
                $url = '';
            }else {
                if( $this->url == '' ) {
                    $url = Url::to(['update', 'id' => $model['id']]);
                }else {
                    $url = $this->url;
                }
            }
            $aOptions = [];
            if( $url != ''){
                if( !isset( $this->aOptions['data-params']  ) ){
                    $aOptions = array_merge([
                        'data-params' => [
                            $this->formName ? $this->formName : $model->formName() . "[{$field}]" => $model[$field] == Constants::YesNo_Yes ? Constants::YesNo_No : Constants::YesNo_Yes],
                    ],$this->aOptions, $aOptions);
                }
                if( !isset( $this->aOptions['class'] ) ){
                    $class = $model[$field] == Constants::YesNo_Yes ? $this->yesClass : $this->noClass;
                    $aOptions = array_merge([
                        'class' => 'label ' . $class,
                    ],$this->aOptions, $aOptions);
                }
                if( !isset( $this->aOptions['data-confirm'] ) ){
                    $aOptions = array_merge([
                        'data-confirm' => $model[$field] == Constants::YesNo_Yes ? Yii::t('app', 'Are you sure you want to disable this item?') : Yii::t('app', 'Are you sure you want to enable this item?'),
                    ],$this->aOptions, $aOptions);
                }
            }
            return Html::a($text, $url, $aOptions);
        };
    }
}