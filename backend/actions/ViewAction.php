<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 10:10
 */

namespace backend\actions;


class ViewAction extends \yii\base\Action
{

    public $modelClass;


    /**
     * view详情页
     *
     * @param $id
     * @return string
     */
    public function run($id)
    {
        /* @var $model \yii\db\ActiveRecord */
        $model = call_user_func([$this->modelClass, 'findOne'], $id);
        return $this->controller->render('view', [
            'model' => $model,
        ]);
    }

}