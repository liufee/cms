<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 00:31
 */

namespace backend\actions;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\UnprocessableEntityHttpException;

class UpdateAction extends \yii\base\Action
{

    public $modelClass;

    public $scenario = 'default';

    public $paramSign = "id";

    /** @var string 模板路径，默认为action id  */
    public $viewFile = null;

    /** @var array|\Closure 分配到模板中去的变量 */
    public $data;

    /** @var  string|array 编辑成功后跳转地址,此参数直接传给yii::$app->controller->redirect(),默认跳转到进入编辑页前的地址 */
    public $successRedirect;


    /**
     * update修改
     *
     * @return array|string|\yii\web\Response
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function run()
    {
        $id = Yii::$app->getRequest()->get($this->paramSign, null);
        if (! $id) throw new BadRequestHttpException(Yii::t('app', "{$this->paramSign} doesn't exist"));
        /* @var $model \yii\db\ActiveRecord */
        $model = call_user_func([$this->modelClass, 'findOne'], $id);
        if (! $model) throw new BadRequestHttpException(Yii::t('app', "Cannot find model by $id"));
        $model->setScenario( $this->scenario );

        if (Yii::$app->getRequest()->getIsPost()) {
            if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
                if( Yii::$app->getRequest()->getIsAjax() ){
                    return [];
                }else {
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Success'));
                    if( $this->successRedirect ) return $this->controller->redirect($this->successRedirect);
                    $url = Yii::$app->getSession()->get("_update_referer");
                    if( $url ) return $this->controller->redirect($url);
                    return $this->controller->refresh();
                }
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                if( Yii::$app->getRequest()->getIsAjax() ){
                    throw new UnprocessableEntityHttpException($err);
                }else {
                    Yii::$app->getSession()->setFlash('error', $err);
                }
                $model = call_user_func([$this->modelClass, 'findOne'], $id);
            }
        }

        $this->viewFile === null && $this->viewFile = $this->id;
        $data = [
            'model' => $model,
        ];
        if( is_array($this->data) ){
            $data = array_merge($data, $this->data);
        }elseif ($this->data instanceof \Closure){
            $data = call_user_func_array($this->data, [$model, $this]);
        }
        Yii::$app->getSession()->set("_update_referer", Yii::$app->getRequest()->getReferrer());
        return $this->controller->render($this->viewFile, $data);
    }

}