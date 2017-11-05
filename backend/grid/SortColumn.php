<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-10-01 10:43
 */

namespace backend\grid;

use yii\helpers\Html;

class SortColumn extends DataColumn
{

    public $attribute = 'sort';

    public $primaryKey = '';

    public $options = ['style'=>'width:50px'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->content = function ($model, $key, $index, $gridView) {
            /* @var $model \backend\models\Article */
            if( $this->primaryKey == '' ) $this->primaryKey = $model->getPrimaryKey(false);
            return Html::input('number', "{$this->attribute}[{$this->primaryKey}]", $model[$this->attribute], $this->options);
        };
    }

}