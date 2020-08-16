<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 01:08
 */

namespace backend\actions;

use Yii;
use stdClass;
use Closure;
use backend\actions\helpers\Helper;
use yii\base\Exception;
use yii\web\BadRequestHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Response;
use yii\web\UnprocessableEntityHttpException;

/**
 * backend delete
 * only permit POST request, but can assign value throw query or body for need delete record.
 *
 * Class DeleteAction
 * @package backend\actions
 */
class DeleteAction extends \yii\base\Action
{
    /**
     * @var string|array primary key(s) name
     */
    public $primaryKeyIdentity = "id";

    /**
     * @var Closure the real delete logic, usually will call service layer delete method
     */
    public $doDelete;

    /**
     * delete
     *
     * @throws BadRequestHttpException
     * @throws MethodNotAllowedHttpException
     * @throws UnprocessableEntityHttpException
     * @throws Exception
     */
    public function run()
    {
        if (Yii::$app->getRequest()->getIsPost()) {//for safety, delete need POST
            if( !is_string($this->primaryKeyIdentity) ){
                throw new Exception(__CLASS__ . "::primaryKeyIdentity only permit string");
            }
            $data = Yii::$app->getRequest()->post($this->primaryKeyIdentity, null);
            if ($data === null) {//不在post参数，则为单个删除
                $data = Yii::$app->getRequest()->get($this->primaryKeyIdentity, null);
            }

            if (!$data) {
                throw new BadRequestHttpException(Yii::t('app', "{$this->primaryKeyIdentity} doesn't exist"));
            }
            if( is_string($data) ){
                if( (strpos($data, "{") === 0 && strpos(strrev($data), "}") === 0) || (strpos($data, "[") === 0 && strpos(strrev($data), "]") === 0) ){
                    $data = json_decode($data, true);
                }else{
                    $data = [$data];
                }
            }
            !isset($data[0]) && $data = [$data];

            $errors = [];
            foreach ($data as $id){
                $deleteResult = call_user_func_array($this->doDelete, [$id, $this]);
                if($deleteResult !== true && $deleteResult !== "" && $deleteResult !== null){
                    $errors[]= Helper::getErrorString($deleteResult);
                }
            }

            if (count($errors) == 0) {
                if( Yii::$app->getRequest()->getIsAjax() ) {
                    Yii::$app->getResponse()->format = Response::FORMAT_JSON;
                    return ['code'=>0, 'msg'=>'success', 'data'=>new stdClass()];
                }else {
                    return $this->controller->redirect(Yii::$app->getRequest()->getReferrer());
                }
            } else {
                if( Yii::$app->getRequest()->getIsAjax() ){
                    Yii::$app->getResponse()->format = Response::FORMAT_JSON;
                    throw new UnprocessableEntityHttpException(implode("<br>", $errors));
                }else {
                    Yii::$app->getSession()->setFlash('error', implode("<br>", $errors));
                }
            }

        } else {
            throw new MethodNotAllowedHttpException(Yii::t('app', "Delete must be POST http method"));
        }
    }
}