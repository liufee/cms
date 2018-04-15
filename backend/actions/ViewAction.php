<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 10:10
 */

namespace backend\actions;


use yii;
use yii\web\BadRequestHttpException;

class ViewAction extends \yii\base\Action
{

    public $modelClass;

    public $scenario = 'default';

    /** @var string 模板路径，默认为action id  */
    public $viewFile = 'view';


    /**
     * view详情页
     *
     * @param $id
     * @return string
     * @throws \yii\web\BadRequestHttpException
     */
    public function run($id)
    {
        /* @var $model \yii\db\ActiveRecord */
        $model = call_user_func([$this->modelClass, 'findOne'], $id);
        if (! $model) throw new BadRequestHttpException(yii::t('app', "Cannot find model by $id"));
        $model->setScenario( $this->scenario );
        return $this->controller->render($this->viewFile, [
            'model' => $model,
        ]);
    }

}