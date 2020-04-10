<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-04-03 09:50
 */
namespace backend\actions;

use Yii;
use Closure;
use stdClass;
use backend\actions\helpers\Helper;
use yii\base\Exception;
use yii\web\UnprocessableEntityHttpException;

/**
 * backend execute action
 * Often use to for none page display, and only execute action.
 *
 * Class DoAction
 * @package backend\actions
 */
class DoAction extends \yii\base\Action
{
    /**
     * @var string|array primary key(s) name
     */
    public $primaryKeyIdentity = null;

    /**
     * @var string primary keys(s) from (GET or POST)
     */
    public $primaryKeyFromMethod = "GET";

    /** @var  string|array success do redirect to url (this value will pass yii::$app->controller->redirect($this->successRedirect) to generate url), default is referer url
     */
    public $successRedirect = null;

    /**
     * @var Closure the real do logic
     */
    public $do;

    /**
     * @var string after success doUpdate tips message showed in page top
     */
    public $successTipsMessage = "success";


    public function init()
    {
        parent::init();
        if( $this->successTipsMessage === "success"){
            $this->successTipsMessage = Yii::t("app", "success");
        }
    }

    /**
     * do
     *
     * @return mixed
     * @throws UnprocessableEntityHttpException
     * @throws Exception
     */
    public function run()
    {
        //according assigned HTTP Method and param name to get value. will be passed to $this->doUpdate closure and $this->data closure.Often use for get value of primary key.
        $primaryKeys = Helper::getPrimaryKeys($this->primaryKeyIdentity, $this->primaryKeyFromMethod);

        if (!$this->do instanceof Closure) {
            throw new Exception(__CLASS__ . "::do must be closure");
        }

        $postData = Yii::$app->getRequest()->post();

        $doData = [];

        if (!empty($primaryKeys)) {
            foreach ($primaryKeys as $primaryKey) {
                array_push($doData, $primaryKey);
            }
        }

        array_push($doData, $postData, $this);

        /**
         * do action, function(primaryKey1, primaryKey2 ..., $_POST, DoAction)
         */
        $doResult = call_user_func_array($this->do, $doData);//call do closure

        if (Yii::$app->getRequest()->getIsAjax()) { //ajax
            if ($doResult == true) {//only $doResult is true represent create success
                return ['code' => 0, 'msg' => 'success', 'data' => new stdClass()];
            } else {//not ajax
                throw new UnprocessableEntityHttpException(Helper::getErrorString($doResult));
            }
        } else {
            if ($doResult === true) {//only $doResult is true represent create success
                Yii::$app->getSession()->setFlash('success', $this->successTipsMessage);
                if ($this->successRedirect) return $this->controller->redirect($this->successRedirect);//if $this->successRedirect not empty will redirect to this url
                $url = Yii::$app->getRequest()->getReferrer();
                if ($url) return $this->controller->redirect($url);//get an not empty referer will redirect to this url(often, before do page.)
                return $this->controller->redirect(["index"]);//default is redirect to current controller index action(attention: if current controller has no index action will get a HTTP 404 error)
            } else {
                Yii::$app->getSession()->setFlash('error', Helper::getErrorString($doResult));
            }
        }
    }
}