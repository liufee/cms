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

class CreateAction extends \yii\base\Action
{

    public $modelClass;

    public $scenario = 'default';

    public $data = [];

    /** @var string 模板路径，默认为action id  */
    public $viewFile = null;

    /** @var  string|array 编辑成功后跳转地址,此参数直接传给yii::$app->controller->redirect(),默认跳转到进入到创建页前的地址 */
    public $successRedirect;

    /**
     * create创建页
     *
     * @return string|\yii\web\Response
     */
    public function run()
    {
        /* @var $model \yii\db\ActiveRecord */
        $model = new $this->modelClass;
        $model->setScenario( $this->scenario );
        if (Yii::$app->getRequest()->getIsPost()) {
            if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Success'));
                if( $this->successRedirect ) return $this->controller->redirect($this->successRedirect);
                $url = Yii::$app->getSession()->get("_create_referer");
                if( $url ) return $this->controller->redirect($url);
                return $this->controller->redirect(["index"]);
            } else {
                $errorReasons = $model->getErrors();
                $err = '';
                foreach ($errorReasons as $errorReason) {
                    $err .= $errorReason[0] . '<br>';
                }
                $err = rtrim($err, '<br>');
                Yii::$app->getSession()->setFlash('error', $err);
            }
        }
        $model->loadDefaultValues();
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

}