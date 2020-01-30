<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 10:00
 */

namespace backend\actions;

use Yii;
use Closure;
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
            $error = call_user_func_array($this->sort, [$condition, $value]);
            if ($error == "") {
                if (Yii::$app->getRequest()->getIsAjax()) {
                    return [];
                } else {
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Success'));
                    return $this->controller->goBack();
                }
            } else {
                if (Yii::$app->getRequest()->getIsAjax()) {

                } else {
                    Yii::$app->getSession()->setFlash('error', $error);
                    return $this->controller->goBack();
                }
            }
        }else{
            throw new MethodNotAllowedHttpException(Yii::t('app', "Sort must be POST http method"));
        }
    }
}