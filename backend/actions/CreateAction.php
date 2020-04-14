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

/**
 * backend create
 * if create occurs error, must return model or error string for display error. return true for successful create.
 * if GET request, the createResult be a null, POST request the createResult is the value of doCreate closure returns.
 *
 * Class CreateAction
 * @package backend\actions
 */
class CreateAction extends \yii\base\Action
{

    const CREATE_REFERER = "_create_referer";

    /**
     * @var string|array primary key(s) name
     */
    public $primaryKeyIdentity = null;

    /**
     * @var string primary keys(s) from (GET or POST)
     */
    public $primaryKeyFromMethod = "GET";

    /**
     * @var array|\Closure variables will assigned to view
     */
    public $data = [];

    /** @var  string|array success create redirect to url (this value will pass yii::$app->controller->redirect($this->successRedirect) to generate url), default is (GET request) referer url
     */
    public $successRedirect = null;

    /**
     * @var Closure the real create logic, usually will call service layer create method
     */
    public $doCreate;

    /**
     * @var string after success doUpdate tips message showed in page top
     */
    public $successTipsMessage = "success";

    /** @var string view template file，default is action id  */
    public $viewFile = null;


    public function init()
    {
        parent::init();
        if( $this->successTipsMessage === "success"){
            $this->successTipsMessage = Yii::t("app", "success");
        }
    }

    /**
     * create
     *
     * @return mixed
     * @throws UnprocessableEntityHttpException
     * @throws Exception
     */
    public function run()
    {
        //according assigned HTTP Method and param name to get value. will be passed to $this->doCreate closure and $this->data closure.Often there is no need to get value on create, so default value is null.
        $primaryKeys = Helper::getPrimaryKeys($this->primaryKeyIdentity, $this->primaryKeyFromMethod);

        if (Yii::$app->getRequest()->getIsPost()) {//if POST request will execute doCreate.
            if (!$this->doCreate instanceof Closure) {
                throw new Exception(__CLASS__ . "::doCreate must be closure");
            }

            $postData = Yii::$app->getRequest()->post();

            $createData = [];//doCreate closure formal parameter(translate: 传递给doCreate必包的形参)

            if( !empty($primaryKeys) ){
                foreach ($primaryKeys as $primaryKey) {
                    array_push($createData, $primaryKey);
                }
            }

            array_push($createData, $postData, $this);

            /**
             * doCreate(primaryKey1, primaryKey2 ..., $_POST, CreateAction)
             */
            $createResult = call_user_func_array($this->doCreate, $createData);//call doCreate closure

            if (Yii::$app->getRequest()->getIsAjax()) { //ajax
                if ($createResult === true) {//only $createResult is true represent create success
                    return ['code' => 0, 'msg' => 'success', 'data' => new stdClass()];
                } else {
                    throw new UnprocessableEntityHttpException(Helper::getErrorString($createResult));
                }
            } else {//not ajax
                if ($createResult === true) {//only $createResult is true represent create success
                    Yii::$app->getSession()->setFlash('success', $this->successTipsMessage);
                    if ($this->successRedirect) return $this->controller->redirect($this->successRedirect);//if $this->successRedirect not empty will redirect to this url
                    $url = Yii::$app->getSession()->get(self::CREATE_REFERER);
                    if ($url) return $this->controller->redirect($url);//get an not empty referer will redirect to this url(often, before do create page. also to say: index list page)
                    return $this->controller->redirect(["index"]);//default is redirect to current controller index action(attention: if current controller has no index action will get a HTTP 404 error)
                    //if doCreate success will terminated here!!!
                } else {//besides true, all represent create failed.
                    Yii::$app->getSession()->setFlash('error', Helper::getErrorString($createResult));//if doCreate error will set a error description string.and continue the current page.
                }
            }
        }


        //if GET request or doCreate failed, will display the create page.
        if (is_array($this->data)) {
            $data = $this->data;//this data will assigned to view
        } else if ($this->data instanceof Closure) {
            $params = [];
            if( !empty($primaryKeys) ){
                foreach ($primaryKeys as $primaryKey) {
                    array_push($params, $primaryKey);
                }
            }
            //GET request just display create page. Only POST request will get a updateResult(returned by doCreate closure)
            !isset($createResult) && $createResult = null;
            array_push($params, $createResult, $this);
            $data = call_user_func_array($this->data, $params);//this data will assigned to view
        } else {
            throw new Exception(__CLASS__ . "::data only allows array or closure (with return array)");
        }

        $this->viewFile === null && $this->viewFile = $this->id;

        Yii::$app->getRequest()->getIsGet() && Yii::$app->getSession()->set(self::CREATE_REFERER, Yii::$app->getRequest()->getReferrer());//set an referer, when success doUpdate may redirect this url

        return $this->controller->render($this->viewFile, $data);
    }
}