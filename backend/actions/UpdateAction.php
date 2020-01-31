<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 00:31
 */

namespace backend\actions;


use Yii;
use stdClass;
use Closure;
use backend\actions\helpers\Helper;
use yii\base\Exception;
use yii\web\UnprocessableEntityHttpException;

class UpdateAction extends \yii\base\Action
{

    /**
     * @var string|array primary key(s) name
     */
    public $primaryKeyIdentity = 'id';

    /**
     * @var string primary keys(s) from (GET or POST)
     */
    public $primaryKeyFromMethod = "GET";

    /**
     * @var array|\Closure 分配到模板中去的变量
     */
    public $data;

    /**
     * @var  string|array 编辑成功后跳转地址,此参数直接传给yii::$app->controller->redirect(),默认跳转到进入编辑页前的地址
     */
    public $successRedirect;

    /**
     * @var Closure 执行修改
     */
    public $update;

    /**
     * @var string 模板路径，默认为action id
     */
    public $viewFile = null;


    /**
     * update修改
     *
     * @return array|string
     * @throws UnprocessableEntityHttpException
     * @throws Exception
     */
    public function run()
    {
        $primaryKeys = Helper::getPrimaryKeys($this->primaryKeyIdentity, $this->primaryKeyFromMethod);

        if (Yii::$app->getRequest()->getIsPost()) {
            if (!$this->update instanceof Closure) {
                throw new Exception(__CLASS__ . "::update must be closure");
            }
            $postData = Yii::$app->getRequest()->post();

            $updateData = [];

            if( !empty($primaryKeys) ){
                array_push($updateData, $primaryKeys);
            }

            array_push($updateData, $postData, $this);

            $updateResult = call_user_func_array($this->update, $updateData);

            if(  Yii::$app->getRequest()->getIsAjax() ){ //ajax
                if( $updateResult === true ){
                    return ['code'=>0, 'msg'=>'success', 'data'=>new stdClass()];
                }else{
                    throw new UnprocessableEntityHttpException(Helper::getErrorString($updateResult));
                }
            }else{
                if( $updateResult === true ){//create success
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Success'));
                    if ($this->successRedirect) return $this->controller->redirect($this->successRedirect);
                    $url = Yii::$app->getSession()->get("_update_referer");
                    if ($url) return $this->controller->redirect($url);
                    return $this->controller->redirect(["index"]);
                }else{
                    Yii::$app->getSession()->setFlash('error', Helper::getErrorString($updateResult));
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
            !isset($updateResult) && $updateResult = null;
            array_push($getDataParams, $updateResult, $this);
            $data = call_user_func_array($this->data, $getDataParams);
        } else {
            throw new Exception(__CLASS__ . "::data only allows array or closure (with return array)");
        }

        $this->viewFile === null && $this->viewFile = $this->id;

        Yii::$app->getRequest()->getIsGet() && Yii::$app->getSession()->set("_update_referer", Yii::$app->getRequest()->getReferrer());
        return $this->controller->render($this->viewFile, $data);
    }


}