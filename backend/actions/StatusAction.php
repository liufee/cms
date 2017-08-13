<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 10:06
 */

namespace backend\actions;

use yii;
use yii\web\Response;
use yii\web\BadRequestHttpException;

class StatusAction extends \yii\base\Action
{

    public $modelClass;

    /**
     * 改变某个字段状态操作
     *
     * @param string $id
     * @param int $status
     * @param string $field
     * @return array|\yii\web\Response
     * @throws \yii\web\BadRequestHttpException
     */
    public function run($id = '', $status = 0, $field = 'status')
    {
        if (yii::$app->getRequest()->getIsAjax()) {
            yii::$app->getResponse()->format = Response::FORMAT_JSON;
        }
        if (! $id) throw new BadRequestHttpException(yii::t('app', "Id doesn't exit"));

        /* @var $model yii\db\ActiveRecord */
        $model = call_user_func([$this->modelClass, 'findOne'], $id);
        if (! $model) throw new BadRequestHttpException(yii::t("app", "Cannot find model by $id"));

        $model->$field = $status;

        if (yii::$app->getRequest()->getIsAjax()) {
            if ($model->save(false)) {
                return ['code' => 0, 'message' => yii::t('app', 'Success')];
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                return ['code' => 1, 'message' => $err];
            }
        } else {
            $model->save();
            return $this->controller->redirect(yii::$app->getRequest()->headers['referer']);
        }
    }

}