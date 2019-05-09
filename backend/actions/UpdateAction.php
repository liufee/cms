<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 00:31
 */

namespace backend\actions;

use Yii;
use Closure;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\UnprocessableEntityHttpException;

class UpdateAction extends \yii\base\Action
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
    public $data;

    /**
     * @var  string|array 编辑成功后跳转地址,此参数直接传给yii::$app->controller->redirect(),默认跳转到进入编辑页前的地址
     */
    public $successRedirect;

    /**
     * @var string|Closure 如果传字符串则执行model的此方法,如果为必包则执行自定义逻辑排序
     */
    public $executeMethod = "save";

    /**
     * @var string 模板路径，默认为action id
     */
    public $viewFile = null;


    /**
     * update修改
     *
     * @return array|string
     * @throws BadRequestHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        $model = $this->getModel();
        if (! $model) throw new BadRequestHttpException(Yii::t('app', "Cannot find model"));

        $result = false;
        if (Yii::$app->getRequest()->getIsPost()) {
            if( $model->load( Yii::$app->getRequest()->post() ) ) {
                if ($this->executeMethod instanceof Closure) {
                    $result = call_user_func($this->executeMethod, $model);
                } else {
                    if (!is_string($this->executeMethod)) throw new InvalidArgumentException("SortAction executeMethod must be string or closure");
                    $result = $model->{$this->executeMethod}();
                }
            }
            if( $result ){
                if( Yii::$app->getRequest()->getIsAjax() ){
                    return [];
                }else {
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Success'));
                    if( $this->successRedirect ) return $this->controller->redirect($this->successRedirect);
                    $url = Yii::$app->getSession()->get("_update_referer");
                    if( $url ) return $this->controller->redirect($url);
                    return $this->controller->refresh();
                }
            }else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                if (Yii::$app->getRequest()->getIsAjax()) {
                    throw new UnprocessableEntityHttpException($err);
                } else {
                    Yii::$app->getSession()->setFlash('error', $err);
                }
            }
        }

        $this->viewFile === null && $this->viewFile = $this->id;
        $data = [
            'model' => $model,
        ];
        if( is_array($this->data) ){
            $data = array_merge($data, $this->data);
        }elseif ($this->data instanceof Closure){
            $data = call_user_func_array($this->data, [$model, $this]);
        }
        Yii::$app->getRequest()->getIsGet() && Yii::$app->getSession()->set("_update_referer", Yii::$app->getRequest()->getReferrer());
        return $this->controller->render($this->viewFile, $data);
    }

    /**
     * @return \Closure|mixed|\yii\db\ActiveRecord
     * @throws \yii\base\InvalidConfigException
     */
    private function getModel()
    {
        if($this->model === null) {
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