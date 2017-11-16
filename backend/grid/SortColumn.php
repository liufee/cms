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

    public $options = ['style'=>'width:50px'];

    public $primaryKey = "";

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->content = function ($model, $key, $index, $gridView) {
            /* @var $model \backend\models\Article */
            if( $this->primaryKey !== '' ){
                if( $this->primaryKey instanceof \Closure){
                    $key = call_user_func($this->primaryKey, $model);
                }else{
                    $key = $this->primaryKey;
                }
            }
            return Html::input('number', "{$this->attribute}[{$key}]", $model[$this->attribute], $this->options);
        };
    }

}