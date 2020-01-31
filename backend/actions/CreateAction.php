<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 00:06
 */
namespace backend\actions;

use Yii;
use Closure;
use stdClass;
use backend\actions\helpers\Helper;
use yii\base\Exception;
use yii\web\UnprocessableEntityHttpException;

class CreateAction extends \yii\base\Action
{
    /**
     * @var string|array primary key(s) name
     */
    public $primaryKeyIdentity = null;

    /**
     * @var string primary keys(s) from (GET or POST)
     */
    public $primaryKeyFromMethod = "GET";

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
        $primaryKeys = Helper::getPrimaryKeys($this->primaryKeyIdentity, $this->primaryKeyFromMethod);

        if (Yii::$app->getRequest()->getIsPost()) {//POST request execute create
            if (!$this->create instanceof Closure) {
                throw new Exception(__CLASS__ . "::create must be closure");
            }

            $postData = Yii::$app->getRequest()->post();

            $createData = [];

            if( !empty($primaryKeys) ){
                array_push($updateData, $primaryKeys);
            }

            array_push($createData, $postData, $this);

            $createResult = call_user_func_array($this->create, $createData);//do create

            if (Yii::$app->getRequest()->getIsAjax()) { //ajax
                if ($createResult == true) {
                    return ['code' => 0, 'msg' => 'success', 'data' => new stdClass()];
                } else {
                    throw new UnprocessableEntityHttpException(Helper::getErrorString($createResult));
                }
            } else {
                if ($createResult === true) {//create success
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Success'));
                    if ($this->successRedirect) return $this->controller->redirect($this->successRedirect);
                    $url = Yii::$app->getSession()->get("_create_referer");
                    if ($url) return $this->controller->redirect($url);
                    return $this->controller->redirect(["index"]);
                } else {
                    Yii::$app->getSession()->setFlash('error', Helper::getErrorString($createResult));
                }
            }
        }

        if (is_array($this->data)) {
            $data = $this->data;
        } elseif ($this->data instanceof Closure) {
            $getDataParams = [];
            if( !empty($primaryKeys) ){
                array_push($getDataParams, $primaryKeys);
            }
            !isset($createResult) && $createResult = null;
            array_push($getDataParams, $createResult, $this);
            $data = call_user_func_array($this->data, $getDataParams);
        } else {
            throw new Exception("CreateAction::data only allows array or closure (with return array)");
        }

        $this->viewFile === null && $this->viewFile = $this->id;
        Yii::$app->getRequest()->getIsGet() && Yii::$app->getSession()->set("_create_referer", Yii::$app->getRequest()->getReferrer());
        return $this->controller->render($this->viewFile, $data);
    }
}