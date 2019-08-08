<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-10-01 10:43
 */

namespace backend\grid;

use Closure;
use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;
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
     * @var array 主键
     */
    public $primaryKey = [];

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

        $this->action === null && $this->action = Url::to(['sort']);

        $this->headerOptions = array_merge(['action'=>$this->action, 'method'=>$this->method, 'sort-header'=>1], $this->headerOptions);

        $this->content = function ($model, $key, $index, $gridView) {
            /* @var $model \backend\models\Article */
            $pk = [];
            if( !empty( $this->primaryKey ) ){
                if( $this->primaryKey instanceof Closure){
                    $pk = call_user_func($this->primaryKey, $model);
                }else{
                    $pk = $this->primaryKey;
                }
                if( !is_array($pk) ){
                    throw new InvalidArgumentException("SortColumn primary key must be closure return array or config with array ( like ['id'=>1] )");
                }
            }else{
                if( is_object($model) && $model instanceof ActiveRecord ){
                    $primaryKeys = $model->getPrimaryKey(true);
                    foreach ($primaryKeys as $key => $abandon) {
                        $pk[$key] = $model[$key];
                    }
                }
            }
            if( empty($pk) ){
                throw new InvalidArgumentException("SortColumn must set table primary key or pass a primaryKey");
            }
            is_array($pk) && $pk = json_encode($pk);
            return Html::input('number', "{$this->attribute}[{$pk}]", $model[$this->attribute], $this->options);
        };
    }

}