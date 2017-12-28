<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-10-01 10:43
 */

namespace backend\grid;

use yii\helpers\Html;
use yii\helpers\Url;

class SortColumn extends DataColumn
{

    public $attribute = 'sort';

    public $options = ['style'=>'width:50px', 'class'=>'sort'];

    /**
     * @var string 在input onBlur时提交的地址,默认为当前控制器下的actionSort方法
     */
    public $action = null;

    /**
     * @var string 在input onBlur时ajax提交的方法
     */
    public $method = 'post';

    /**
     * @var string 主键
     */
    public $primaryKey = "";

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if( !isset($this->options['class']) ){
            $this->options['class'] = 'sort';
        }else if(strpos($this->options['class'], 'sort') === false){
            $this->options['class'] .= ' sort';
        }

        if($this->action === null) $this->action = Url::to(['sort']);

        $this->headerOptions = array_merge($this->headerOptions, ['action'=>$this->action, 'method'=>$this->method, 'sort-header'=>1]);

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