<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 10:00
 */

namespace backend\actions;

use Yii;
use stdClass;
use Closure;
use backend\actions\helpers\Helper;
use yii\base\InvalidArgumentException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\UnprocessableEntityHttpException;

class SortAction extends \yii\base\Action
{

    /**
     * @var Closure
     */
    public $sort = null;

    /**
     * @var string 场景
     */
    public $scenario = 'default';

    /**
     * 排序操作
     *
     */
    public function run()
    {
        if (Yii::$app->getRequest()->getIsPost()) {
            $post = Yii::$app->getRequest()->post();
            if (isset($post[Yii::$app->getRequest()->csrfParam])) {
                unset($post[Yii::$app->getRequest()->csrfParam]);
            }
            reset($post);
            $temp = current($post);
            $condition = array_keys($temp)[0];
            $value = $temp[$condition];
            $condition = json_decode($condition, true);
            if (!is_array($condition)) throw new InvalidArgumentException("SortColumn generate html must post data like xxx[{pk:'unique'}]=number");
            $result = call_user_func_array($this->sort, [$condition, $value, $this]);

            if (Yii::$app->getRequest()->getIsAjax()) {
                if( $result === true ){
                    return ['code'=>0, 'msg'=>'success', 'data'=>new stdClass()];
                }else{
                    throw new UnprocessableEntityHttpException(Helper::getErrorString($result));
                }
            }else {
                if ($result === true) {
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Success'));
                } else {
                    Yii::$app->getSession()->setFlash('error', Helper::getErrorString($result));
                }
                return $this->controller->goBack();
            }

        }else{
            throw new MethodNotAllowedHttpException(Yii::t('app', "Sort must be POST http method"));
        }
    }
}