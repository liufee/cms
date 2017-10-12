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
use yii\web\UnprocessableEntityHttpException;

class DeleteAction extends \yii\base\Action
{

    public $modelClass;

    /**
     * delete删除
     *
     * @param $id
     * @return array|\yii\web\Response
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function run($id)
    {
        if (yii::$app->getRequest()->getIsAjax()) {//AJAX删除
            if (! $id) {
                throw new BadRequestHttpException(yii::t('app', "Id doesn't exit"));
            }
            $ids = explode(',', $id);
            $errorIds = [];
            /* @var $model yii\db\ActiveRecord */
            $model = null;
            foreach ($ids as $one) {
                $model = call_user_func([$this->modelClass, 'findOne'], $one);
                if ($model) {
                    if (! $result = $model->delete()) {
                        $errorIds[] = $one;
                    }
                }
            }
            if (count($errorIds) == 0) {
                return [];
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0];
                }
                if ($err != '') {
                    $err = '.' . $err;
                }
                throw new UnprocessableEntityHttpException('id ' . implode(',', $errorIds) . $err);
            }
        } else {
            if (! $id) {
                throw new BadRequestHttpException(yii::t('app', "Id doesn't exit"));
            }
            $model = call_user_func([$this->modelClass, 'findOne'], $id);
            if ($model) {
                $model->delete();
            }
            return $this->controller->redirect(yii::$app->request->headers['referer']);
        }
    }

}