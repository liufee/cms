<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 10:00
 */

namespace backend\actions;

use Yii;
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
        if (Yii::$app->getRequest()->getIsPost()) {
            $post = Yii::$app->getRequest()->post();
            if( isset( $post[Yii::$app->getRequest()->csrfParam] ) ) {
                unset($post[Yii::$app->getRequest()->csrfParam]);
            }
            $err = '';
            foreach ($post as $field => $array) {
                foreach ($array as $key => $value) {
                    /* @var $model \yii\db\ActiveRecord */
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
            if (Yii::$app->getRequest()->getIsAjax()) {
                Yii::$app->getResponse()->format = Response::FORMAT_JSON;
                if( !empty($err) ){
                    throw new UnprocessableEntityHttpException($err);
                }else{
                    return [];
                }
            } else {
                if( !empty($err) ){
                    Yii::$app->getSession()->setFlash('error', $err);
                }
                return $this->controller->goBack();
            }
        }
    }

}