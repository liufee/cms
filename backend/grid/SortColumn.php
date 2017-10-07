<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-10-01 10:43
 */

namespace backend\grid;

use yii;
use yii\helpers\Html;

class SortColumn extends DataColumn
{

    public $attribute = 'sort';

    public $primaryKey = 'id';

    public $label = 'label';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if( $this->label == 'label' ){
            $this->label = yii::t('app', 'Sort');
        }

        $this->content = function ($model, $key, $index, $gridView) {
            return Html::input('number', "sort[{$model[$this->primaryKey]}]", $model['sort'], ['style' => 'width:50px']);
        };
    }

}