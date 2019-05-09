<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 00:06
 */
namespace backend\actions;

use Closure;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\UnprocessableEntityHttpException;

class CreateAction extends \yii\base\Action
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

    /**
     * @var array|\Closure 分配到模板中去的变量
     */
    public $data = [];

    /** @var  string|array 创建成功后跳转地址,此参数直接传给yii::$app->controller->redirect(),默认跳转到index */
    public $successRedirect;

    /**
     * @var string|Closure 如果传字符串则执行model的此方法,如果为必包则执行自定义逻辑排序
     */
    public $executeMethod = "save";

    /** @var string 模板路径，默认为action id  */
    public $viewFile = null;

    /**
     * create创建页
     *
     * @return mixed
     * @throws UnprocessableEntityHttpException
     */
    public function run()
    {
        $model = $this->getModel();
        if (Yii::$app->getRequest()->getIsPost()) {
            if ( $model->load(Yii::$app->getRequest()->post()) ) {
                if( $this->executeMethod instanceof Closure){
                    $result = call_user_func($this->executeMethod, $model);
                }else{
                    if( !is_string($this->executeMethod) ) throw new InvalidArgumentException("SortAction executeMethod must be string or closure");
                    $result = $model->{$this->executeMethod}();
                }
                if( $result ) {
                    if( Yii::$app->getRequest()->getIsAjax() ){
                        return [];
                    }else {
                        Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Success'));
                        if ($this->successRedirect) return $this->controller->redirect($this->successRedirect);
                        $url = Yii::$app->getSession()->get("_create_referer");
                        if ($url) return $this->controller->redirect($url);
                        return $this->controller->redirect(["index"]);
                    }
                }
            }

            $errorReasons = $model->getErrors();
            $err = '';
            foreach ($errorReasons as $errorReason) {
                $err .= $errorReason[0] . '<br>';
            }
            $err = rtrim($err, '<br>');
            if( Yii::$app->getRequest()->getIsAjax() ){
                throw new UnprocessableEntityHttpException($err);
            }
            Yii::$app->getSession()->setFlash('error', $err);
        }
        method_exists($model, "loadDefaultValues") && $model->loadDefaultValues();
        $data = [
            'model' => $model,
        ];
        if( is_array($this->data) ){
            $data = array_merge($data, $this->data);
        }elseif ($this->data instanceof Closure){
            $data = call_user_func_array($this->data, [$model, $this]);
        }
        $this->viewFile === null && $this->viewFile = $this->id;
        Yii::$app->getRequest()->getIsGet() && Yii::$app->getSession()->set("_create_referer", Yii::$app->getRequest()->getReferrer());
        return $this->controller->render($this->viewFile, $data);
    }

    public function getModel()
    {
        if( $this->model !== null ){
            $model = $this->model;
            $this->model instanceof Closure && $model = call_user_func($this->model);
        }else {
            /* @var $model \yii\db\ActiveRecord */
            $model = Yii::createObject([
                'class' => $this->modelClass,
            ]);
            $model->setScenario($this->scenario);
        }
        return $model;
    }
}