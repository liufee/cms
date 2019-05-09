<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 01:08
 */

namespace backend\actions;

use Yii;
use Closure;
use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Response;
use yii\web\UnprocessableEntityHttpException;

class DeleteAction extends \yii\base\Action
{
    /**
     * @var Closure 模型，要么为空使用默认的方式获取模型，要么传入必包，根据必包的参数获取模型后返回
     */
    public $model = null;

    /**
     * @var string model类名
     */
    public $modelClass;

    /**
     * @var string post过来的主键key名
     */
    public $paramSign = 'id';

    /**
     * @var string ajax请求返回数据格式
     */
    public $ajaxResponseFormat = Response::FORMAT_JSON;

    /**
     * @var string 场景
     */
    public $scenario = 'default';

    /**
     * @var string|Closure 如果传字符串则执行model的此方法,如果为必包则执行自定义逻辑排序
     */
    public $executeMethod = "delete";

    /**
     * delete删除
     *
     * @throws BadRequestHttpException
     * @throws MethodNotAllowedHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function run()
    {
        if( Yii::$app->getRequest()->getIsAjax() ){
            Yii::$app->getResponse()->format = $this->ajaxResponseFormat;
        }
        if (Yii::$app->getRequest()->getIsPost()) {//只允许post删除
            $data = Yii::$app->getRequest()->post($this->paramSign, null);
            if ($data === null) {//不在post参数，则为单个删除
                $data = Yii::$app->getRequest()->get($this->paramSign, null);
                if( $data === null ){//不是指定的标识符，默认通过主键
                    /* @var $model \yii\db\ActiveRecord */
                    $model = Yii::createObject([
                        'class' => $this->modelClass,
                    ]);
                    $primaryKeys = $model->getPrimaryKey(true);
                    $data = [];
                    foreach ($primaryKeys as $key => $abandon) {
                        $data[$key] = Yii::$app->getRequest()->get($key, null);
                        if( $data[$key] === null){
                            unset($data[$key]);
                        }
                    }
                }
            }

            if (!$data) {
                throw new BadRequestHttpException(Yii::t('app', "{$this->paramSign} doesn't exist"));
            }
            if( is_string($data) ){
                if( (strpos($data, "{") === 0 && strpos(strrev($data), "}") === 0) || (strpos($data, "[") === 0 && strpos(strrev($data), "]") === 0) ){
                    $data = json_decode($data, true);
                }else{
                    $data = [$data];
                }
            }

            !isset($data[0]) && $data = [$data];

            $errors = [];
            /* @var $model \yii\db\ActiveRecord */
            $model = null;
            foreach ($data as $one) {
                $model = $this->getModel($one);
                if( $this->executeMethod instanceof Closure){
                    $result = call_user_func($this->executeMethod, $model);
                }else{
                    if( !is_string($this->executeMethod) ) throw new InvalidArgumentException("SortAction executeMethod must be string or closure");
                    $result = $model->{$this->executeMethod}(false);
                }
                if (!$result) {
                    $errors[$one] = $model;
                }
            }
            if (count($errors) == 0) {
                if( !Yii::$app->getRequest()->getIsAjax() ) return $this->controller->redirect(Yii::$app->getRequest()->getReferrer());
                return [];
            } else {
                $err = '';
                foreach ($errors as $one => $model){
                    $err .= $one . ':';
                    $errorReasons = $model->getErrors();
                    foreach ($errorReasons as $errorReason) {
                        $err .= $errorReason[0] . ';';
                    }
                    $err = rtrim($err, ';') . '<br>';
                }
                $err = rtrim($err, '<br>');
                throw new UnprocessableEntityHttpException($err);
            }
        } else {
            throw new MethodNotAllowedHttpException(Yii::t('app', "Delete must be POST http method"));
        }
    }

    public function getModel($one)
    {
       if( $this->model ){
           if( !$this->model instanceof Closure){
               throw new InvalidArgumentException("Delete action only permit pass closure for model");
           }
           $model = call_user_func($this->model, $one);
       }else {
           if( is_string($one) && strpos($one, "{") === 0 && strpos(strrev($one), "}") === 0 ){
               $one = json_decode($one, true);
           }
           if ( is_array($one) ) {//联合主键
               /* @var $model \yii\db\ActiveRecord */
               $model = Yii::createObject([
                   'class' => $this->modelClass,
               ]);
               $primaryKeys = $model->getPrimaryKey(true);
               $condition = [];
               foreach ($primaryKeys as $key => $abandon) {
                   isset($one[$key]) && $condition[$key] = $one[$key];
               }
               $model = call_user_func([$this->modelClass, 'findOne'], $condition);
           } else {
               $model = call_user_func([$this->modelClass, 'findOne'], $one);
           }
           $model instanceof ActiveRecord && $model->setScenario($this->scenario);
       }
       return $model;
    }

}