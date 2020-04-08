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

/**
 * backend update
 * if update occurs error, must return model or error string for display error. return true for successful update.
 * if GET request, the updateResult be a null, POST request the createResult is the value of doUpdate closure returns.
 *
 * Class UpdateAction
 * @package backend\actions
 */
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
     * @var array|\Closure variables will assigned to view
     */
    public $data;

    /**
     * @var  string|array success update redirect to url (this value will pass yii::$app->controller->redirect($this->successRedirect) to generate url), default is (GET request) referer url
     */
    public $successRedirect;

    /**
     * @var Closure the real update logic, usually will call service layer update method
     */
    public $doUpdate;

    /**
     * @var string view template path，default is action id
     */
    public $viewFile = null;


    /**
     * update
     *
     * @return array|string
     * @throws UnprocessableEntityHttpException
     * @throws Exception
     */
    public function run()
    {
        $primaryKeys = Helper::getPrimaryKeys($this->primaryKeyIdentity, $this->primaryKeyFromMethod);

        if (Yii::$app->getRequest()->getIsPost()) {
            if (!$this->doUpdate instanceof Closure) {
                throw new Exception(__CLASS__ . "::update must be closure");
            }
            $postData = Yii::$app->getRequest()->post();

            $updateData = [];

            if( !empty($primaryKeys) ){
                foreach ($primaryKeys as $primaryKey) {
                    array_push($updateData, $primaryKey);
                }
            }

            array_push($updateData, $postData, $this);

            $updateResult = call_user_func_array($this->doUpdate, $updateData);

            if(  Yii::$app->getRequest()->getIsAjax() ){ //ajax
                if( $updateResult === true ){
                    return ['code'=>0, 'msg'=>'success', 'data'=>new stdClass()];
                }else{
                    throw new UnprocessableEntityHttpException(Helper::getErrorString($updateResult));
                }
            }else{
                if( $updateResult === true ){//update success
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
            $params = [];
            if( !empty($primaryKeys) ){
                foreach ($primaryKeys as $primaryKey) {
                    array_push($params, $primaryKey);
                }
            }
            //get request just display update page. only post request will get a updateResult(returned by doUpdate)
            !isset($updateResult) && $updateResult = null;
            array_push($params, $updateResult, $this);
            $data = call_user_func_array($this->data, $params);
        } else {
            throw new Exception(__CLASS__ . "::data only allows array or closure (with return array)");
        }

        $this->viewFile === null && $this->viewFile = $this->id;

        Yii::$app->getRequest()->getIsGet() && Yii::$app->getSession()->set("_update_referer", Yii::$app->getRequest()->getReferrer());
        return $this->controller->render($this->viewFile, $data);
    }


}