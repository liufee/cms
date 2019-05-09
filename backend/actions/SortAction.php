<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 10:00
 */

namespace backend\actions;

use Yii;
use Closure;
use yii\base\InvalidArgumentException;
use yii\web\UnprocessableEntityHttpException;

class SortAction extends \yii\base\Action
{

    /**
     * @var Closure|mixed 模型，如果为模型则直接使用，如果为必包则执行得到模型，为空则实例化modelClass
     */
    public $model = null;

    /**
     * @var string model类名
     */
    public $modelClass;

    /**
     * @var string 场景
     */
    public $scenario = 'default';

    /**
     * @var string field 排序的字段，如果不传则从post参数的[]内读取
     */
    public $field = null;

    /**
     * @var string|Closure 如果传字符串则执行model的此方法,如果为必包则执行自定义逻辑排序
     */
    public $executeMethod = "save";

    /**
     * 排序操作
     *
     */
    public function run()
    {
        if (Yii::$app->getRequest()->getIsPost()) {
            $post = Yii::$app->getRequest()->post();
            if( isset( $post[Yii::$app->getRequest()->csrfParam] ) ) {
                unset($post[Yii::$app->getRequest()->csrfParam]);
            }
            $field = key($post);
            reset($post);
            $temp = current($post);
            $condition = array_keys($temp)[0];
            $value = $temp[$condition];
            $condition = json_decode($condition, true);
            if( !is_array( $condition ) ) throw new InvalidArgumentException("SortColumn generate html must post data like xxx[{pk:'unique'}]=number");
            $model = $this->getModel($condition);
            $field = $this->field !== null ? $this->field : $field;
            $model->$field = $value;
            if( $this->executeMethod instanceof Closure){
                $result = call_user_func($this->executeMethod, $model);
            }else{
                if( !is_string($this->executeMethod) ) throw new InvalidArgumentException("SortAction executeMethod must be string or closure");
                $result = $model->{$this->executeMethod}(false);
            }
            if ( $result ) {
                if( Yii::$app->getRequest()->getIsAjax() ){
                    return [];
                }else {
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Success'));
                    return $this->controller->goBack();
                }
            }else{
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                if( Yii::$app->getRequest()->getIsAjax() ){
                    throw new UnprocessableEntityHttpException($err);
                }else {
                    Yii::$app->getSession()->setFlash('error', $err);
                    return $this->controller->goBack();
                }
            }
        }
    }

    public function getModel(array $where)
    {
        if( $this->model === null ) {
            /* @var $model \yii\db\ActiveRecord */
            $model = Yii::createObject([
                'class' => $this->modelClass,
            ]);
            $primaryKeys = $model->getPrimaryKey(true);
            $condition = [];
            foreach ($primaryKeys as $key => $abandon) {
                isset($where[$key]) && $condition[$key] = $where[$key];
            }
            $model = call_user_func([$this->modelClass, 'findOne'], $condition);
            $model->setScenario( $this->scenario );
        }else{
            $model = $this->model;
            if( $this->model instanceof \Closure){
                $model = call_user_func($this->model, $where);
            }
        }
        return $model;
    }

}