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
     * do
     *
     * @return mixed
     * @throws UnprocessableEntityHttpException
     * @throws Exception
     */
    public function run()
    {
        /**
         * get primary keys, often index list page no need primary keys
         */
        $primaryKeys = Helper::getPrimaryKeys($this->primaryKeyIdentity, $this->primaryKeyFromMethod);

        //request do
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
        $doResult = call_user_func_array($this->do, $doData);//do create

        if (Yii::$app->getRequest()->getIsAjax()) { //ajax
            if ($doResult == true) {
                return ['code' => 0, 'msg' => 'success', 'data' => new stdClass()];
            } else {
                throw new UnprocessableEntityHttpException(Helper::getErrorString($doResult));
            }
        } else {
            if ($doResult === true) {//create success
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Success'));
                if ($this->successRedirect) return $this->controller->redirect($this->successRedirect);
                $url = Yii::$app->getRequest()->getReferrer();
                if ($url) return $this->controller->redirect($url);
                return $this->controller->redirect(["index"]);
            } else {
                Yii::$app->getSession()->setFlash('error', Helper::getErrorString($doResult));
            }
        }
    }
}