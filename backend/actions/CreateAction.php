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
use yii\base\Exception;
use yii\web\UnprocessableEntityHttpException;

class CreateAction extends \yii\base\Action
{

    /**
     * @var array|\Closure 分配到模板中去的变量
     */
    public $data = [];

    /** @var  string|array 创建成功后跳转地址,此参数直接传给yii::$app->controller->redirect(),默认跳转到index */
    public $successRedirect;

    /**
     * @var Closure 创建方法
     */
    public $create;

    /** @var string 模板路径，默认为action id  */
    public $viewFile = null;

    /**
     * create创建页
     *
     * @return mixed
     * @throws UnprocessableEntityHttpException
     * @throws Exception
     */
    public function run()
    {
        if (Yii::$app->getRequest()->getIsPost()) {
            if (!$this->create instanceof Closure) {
                throw new Exception("CreateAction::create must be closure");
            }
            $postData = Yii::$app->getRequest()->post();
            $result = call_user_func_array($this->create, [$postData]);
            if( $result=="" || $result===true) {//save success
                if (Yii::$app->getRequest()->getIsAjax()) {
                    return [];
                } else {
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Success'));
                    if ($this->successRedirect) return $this->controller->redirect($this->successRedirect);
                    $url = Yii::$app->getSession()->get("_create_referer");
                    if ($url) return $this->controller->redirect($url);
                    return $this->controller->redirect(["index"]);
                }
            }else{//save error occurs
                if( Yii::$app->getRequest()->getIsAjax() ){
                    throw new UnprocessableEntityHttpException($result);
                }
                Yii::$app->getSession()->setFlash('error', $result);
            }
        }
        if( is_array($this->data) ){
            $data = $this->data;
        }elseif ($this->data instanceof Closure){
            $data = call_user_func($this->data);
        }else{
            throw new Exception("CreateAction::data only allows array or closure (with return array)");
        }
        $this->viewFile === null && $this->viewFile = $this->id;
        Yii::$app->getRequest()->getIsGet() && Yii::$app->getSession()->set("_create_referer", Yii::$app->getRequest()->getReferrer());
        return $this->controller->render($this->viewFile, $data);
    }
}