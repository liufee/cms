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

    const UPDATE_REFERER = "_update_referer";

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
     * @var string after success doUpdate tips message showed in page top
     */
    public $successTipsMessage = "success";

    /**
     * @var string view template path，default is action id
     */
    public $viewFile = null;


    public function init()
    {
        parent::init();
        if( $this->successTipsMessage === "success"){
            $this->successTipsMessage = Yii::t("app", "success");
        }
    }


    /**
     * update
     *
     * @return array|string
     * @throws UnprocessableEntityHttpException
     * @throws Exception
     */
    public function run()
    {
        //according assigned HTTP Method and param name to get value. will be passed to $this->doUpdate closure and $this->data closure.Often use for get value of primary key.
        $primaryKeys = Helper::getPrimaryKeys($this->primaryKeyIdentity, $this->primaryKeyFromMethod);

        if (Yii::$app->getRequest()->getIsPost()) {//if POST request will execute doUpdate.
            if (!$this->doUpdate instanceof Closure) {
                throw new Exception(__CLASS__ . "::doUpdate must be closure");
            }
            $postData = Yii::$app->getRequest()->post();

            $updateData = [];//doUpdate closure formal parameter(translate: 传递给doUpdate必包的形参)

            if( !empty($primaryKeys) ){
                foreach ($primaryKeys as $primaryKey) {
                    array_push($updateData, $primaryKey);
                }
            }

            array_push($updateData, $postData, $this);

            /**
             * doUpdate(primaryKey1, primaryKey2 ..., $_POST, UpdateAction)
             */
            $updateResult = call_user_func_array($this->doUpdate, $updateData);//call doUpdate closure

            if(  Yii::$app->getRequest()->getIsAjax() ){ //ajax
                if( $updateResult === true ){//only $updateResult is true represent update success
                    return ['code'=>0, 'msg'=>'success', 'data'=>new stdClass()];
                }else{
                    throw new UnprocessableEntityHttpException(Helper::getErrorString($updateResult));
                }
            }else{//not ajax
                if( $updateResult === true ){//only $updateResult is true represent update success
                    Yii::$app->getSession()->setFlash('success', $this->successTipsMessage);
                    if ($this->successRedirect) return $this->controller->redirect($this->successRedirect);//if $this->successRedirect not empty will redirect to this url
                    $url = Yii::$app->getSession()->get(self::UPDATE_REFERER);
                    if ($url) return $this->controller->redirect($url);//get an not empty referer will redirect to this url(often, before do update page. also to say: update page)
                    return $this->controller->redirect(["index"]);//default is redirect to current controller index action(attention: if current controller has no index action will get a HTTP 404 error)
                    //if doUpdate success will terminated here!!!
                }else{//besides true, all represent update failed.
                    Yii::$app->getSession()->setFlash('error', Helper::getErrorString($updateResult));//if doUpdate error will set a error description string.and continue the current page.
                }
            }

        }

        //if GET request or doUpdate failed, will display the update page.
        if (is_array($this->data)) {
            $data = $this->data;//this data will assigned to view
        } elseif ($this->data instanceof Closure) {
            $params = [];
            if( !empty($primaryKeys) ){
                foreach ($primaryKeys as $primaryKey) {
                    array_push($params, $primaryKey);
                }
            }
            //GET request just display update page. Only POST request will get a updateResult(returned by doUpdate closure)
            !isset($updateResult) && $updateResult = null;
            array_push($params, $updateResult, $this);
            $data = call_user_func_array($this->data, $params);//this data will assigned to view
        } else {
            throw new Exception(__CLASS__ . "::data only allows array or closure (with return array)");
        }

        $this->viewFile === null && $this->viewFile = $this->id;

        Yii::$app->getRequest()->getIsGet() && Yii::$app->getSession()->set(self::UPDATE_REFERER, Yii::$app->getRequest()->getReferrer());//set an referer, when success doUpdate may redirect this url

        return $this->controller->render($this->viewFile, $data);
    }


}