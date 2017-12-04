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
            $post = yii::$app->getRequest()->post();
            if( isset( $post[yii::$app->getRequest()->csrfParam] ) ) {
                unset($post[yii::$app->getRequest()->csrfParam]);
            }
            foreach ($post as $field => $array){
                foreach ($array as $key => $value){
                    /* @var $model yii\db\ActiveRecord */
                    $model = call_user_func([$this->modelClass, 'findOne'], $key);
                    if ($model->$field != $value) {
                        $model->$field = $value;
                        $model->save(false);
                    }
                }
            }
        }
        return $this->controller->redirect(['index']);
    }

}