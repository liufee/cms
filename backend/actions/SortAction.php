<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 10:00
 */

namespace backend\actions;

use yii;

class SortAction extends \yii\base\Action
{

    public $modelClass;

    /**
     * 排序操作
     *
     */
    public function run()
    {
        if (yii::$app->getRequest()->getIsPost()) {
            $data = yii::$app->getRequest()->post();
            if (! empty($data['sort'])) {
                foreach ($data['sort'] as $key => $value) {
                    /* @var $model yii\db\ActiveRecord */
                    $model = call_user_func([$this->modelClass, 'findOne'], $key);
                    if ($model->sort != $value) {
                        $model->sort = $value;
                        $model->save(false);
                    }
                }
            }
        }
        $this->controller->redirect(['index']);
    }

}