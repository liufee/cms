<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 10:10
 */

namespace backend\actions;


use Yii;
use Closure;
use yii\web\BadRequestHttpException;

class ViewAction extends \yii\base\Action
{

    /**
     * @var Closure|mixed 模型，如果为模型则直接使用，如果为必包则执行得到模型，为空则实例化modelClass
     */
    public $model = null;

    /**
     * @var string model类名
     */
    public $modelClass;

    /**
     * @var string 场景
     */
    public $scenario = 'default';

    /** @var array|Closure 分配到模板中去的变量 */
    public $data;

    /**
     * @var string 模板路径，默认为action id
     */
    public $viewFile = 'view';


    /**
     * view详情页
     *
     * @return string
     * @throws BadRequestHttpException
     */
    public function run()
    {
        $model = $this->getModel();
        if (! $model) throw new BadRequestHttpException(Yii::t('app', "Cannot find model"));
        $data = [
            'model' => $model,
        ];
        if( is_array($this->data) ){
            $data = array_merge($data, $this->data);
        }else if ($this->data instanceof Closure){
            $data = call_user_func_array($this->data, [$model, $this]);
        }
        return $this->controller->render($this->viewFile, $data);
    }

    public function getModel()
    {
        if( $this->model === null ) {
            /* @var $model \yii\db\ActiveRecord */
            $model = Yii::createObject([
                'class' => $this->modelClass,
            ]);
            $primaryKeys = $model->getPrimaryKey(true);
            $condition = [];
            foreach ($primaryKeys as $key => $abandon) {
                $condition[$key] = Yii::$app->getRequest()->get($key, null);
                if( $condition[$key] === null ){
                    unset($condition[$key]);
                }
            }
            $model = call_user_func([$this->modelClass, 'findOne'], $condition);
            $model->setScenario( $this->scenario );
        }else{
            $model = $this->model;
            if( $this->model instanceof Closure){
                $model = call_user_func($this->model);
            }
        }
        return $model;
    }

}