<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 01:08
 */

namespace backend\actions;

use Yii;
use Closure;
use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Response;
use yii\web\UnprocessableEntityHttpException;

class DeleteAction extends \yii\base\Action
{
    /**
     * @var Closure 模型，要么为空使用默认的方式获取模型，要么传入必包，根据必包的参数获取模型后返回
     */
    public $delete;

    /**
     * @var string post过来的主键key名
     */
    public $idSign = 'id';

    /**
     * @var string ajax请求返回数据格式
     */
    public $ajaxResponseFormat = Response::FORMAT_JSON;

    /**
     * delete删除
     *
     * @throws BadRequestHttpException
     * @throws MethodNotAllowedHttpException
     * @throws UnprocessableEntityHttpException
     */
    public function run()
    {
        if( Yii::$app->getRequest()->getIsAjax() ){
            Yii::$app->getResponse()->format = $this->ajaxResponseFormat;
        }
        if (Yii::$app->getRequest()->getIsPost()) {//只允许post删除
            $data = Yii::$app->getRequest()->post($this->idSign, null);
            if ($data === null) {//不在post参数，则为单个删除
                $data = Yii::$app->getRequest()->get($this->idSign, null);
            }

            if (!$data) {
                throw new BadRequestHttpException(Yii::t('app', "{$this->idSign} doesn't exist"));
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
                $error[] = call_user_func_array($this->delete, [$id]);
            }

            if (count($errors) == 0) {
                if( !Yii::$app->getRequest()->getIsAjax() ) return $this->controller->redirect(Yii::$app->getRequest()->getReferrer());
                return [];
            } else {
                throw new UnprocessableEntityHttpException(implode("<br>", $errors));
            }

        } else {
            throw new MethodNotAllowedHttpException(Yii::t('app', "Delete must be POST http method"));
        }
    }
}