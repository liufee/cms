<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 10:00
 */

namespace backend\actions;

use yii;
use yii\web\Response;
use yii\web\UnprocessableEntityHttpException;

class SortAction extends \yii\base\Action
{

    public $modelClass;

    public $scenario = 'default';

    /**
     * 排序操作
     *
     */
    public function run()
    {
        if (yii::$app->getRequest()->getIsPost()) {
            $post = yii::$app->getRequest()->post();
            if( isset( $post[yii::$app->getRequest()->csrfParam] ) ) {
                unset($post[yii::$app->getRequest()->csrfParam]);
            }
            $err = '';
            foreach ($post as $field => $array) {
                foreach ($array as $key => $value) {
                    /* @var $model yii\db\ActiveRecord */
                    $model = call_user_func([$this->modelClass, 'findOne'], $key);
                    $model->setScenario($this->scenario);
                    if ($model->$field != $value) {
                        $model->$field = $value;
                        if (!$model->save()) {
                            if( $err == '' ){
                                $err .= $key . ' : ';
                            }else{
                                $err .= '<br>' . $key . ' : ';
                            }
                            foreach ($model->getErrors() as $errorReason) {
                                $err .= $errorReason[0] . ';';
                            }
                        }
                    }
                }
            }
            $err = rtrim($err, ';');
            if (yii::$app->getRequest()->getIsAjax()) {
                yii::$app->getResponse()->format = Response::FORMAT_JSON;
                if( !empty($err) ){
                    throw new UnprocessableEntityHttpException($err);
                }else{
                    return [];
                }
            } else {
                if( !empty($err) ){
                    yii::$app->getSession()->setFlash('error', $err);
                }
                return $this->controller->goBack();
            }
        }
    }

}