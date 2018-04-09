<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 01:08
 */

namespace backend\actions;

use yii;
use yii\web\BadRequestHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Response;
use yii\web\UnprocessableEntityHttpException;

class DeleteAction extends \yii\base\Action
{

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
     * delete删除
     *
     * @return array|Response
     * @throws BadRequestHttpException
     * @throws MethodNotAllowedHttpException
     * @throws UnprocessableEntityHttpException
     */
    public function run()
    {
        if (yii::$app->getRequest()->getIsPost()) {//只允许post删除
            $id = yii::$app->getRequest()->get($this->paramSign, null);
            $param = yii::$app->getRequest()->post($this->paramSign, null);
            if($param !== null){
                $id = $param;
            }

            if( yii::$app->getRequest()->getIsAjax() ){
                yii::$app->getResponse()->format = $this->ajaxResponseFormat;
            }
            if (! $id) {
                throw new BadRequestHttpException(yii::t('app', "{$this->paramSign} doesn't exist"));
            }
            $ids = explode(',', $id);
            $errors = [];
            /* @var $model yii\db\ActiveRecord */
            $model = null;
            foreach ($ids as $one) {
                $model = call_user_func([$this->modelClass, 'findOne'], $one);
                if ($model) {
                    $model->setScenario($this->scenario);
                    if (! $result = $model->delete()) {
                        $errors[$one] = $model;
                    }
                }
            }
            if (count($errors) == 0) {
                if( !yii::$app->getRequest()->getIsAjax() ) return $this->controller->redirect(yii::$app->getRequest()->headers['referer']);
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
            throw new MethodNotAllowedHttpException(yii::t('app', "Delete must be POST http method"));
        }
    }

}