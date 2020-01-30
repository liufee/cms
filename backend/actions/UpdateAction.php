<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 00:31
 */

namespace backend\actions;

use Yii;
use Closure;
use yii\base\Exception;
use yii\web\UnprocessableEntityHttpException;

class UpdateAction extends \yii\base\Action
{

    /**
     * @var string 主键key名
     */
    public $idSign = 'id';

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
        $id = Yii::$app->getRequest()->get($this->idSign, null);

        if (Yii::$app->getRequest()->getIsPost()) {
            if (!$this->update instanceof Closure) {
                throw new Exception("CreateAction::save must be closure");
            }
            $postData = Yii::$app->getRequest()->post();
            $result = call_user_func_array($this->update, [$id, $postData]);
            if ($result == "" || $result) {//save success
                if (Yii::$app->getRequest()->getIsAjax()) {
                    return [];
                } else {
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Success'));
                    if ($this->successRedirect) return $this->controller->redirect($this->successRedirect);
                    $url = Yii::$app->getSession()->get("_update_referer");
                    if ($url) return $this->controller->redirect($url);
                    return $this->controller->redirect(["index"]);
                }
            } else {//save error occurs
                if (Yii::$app->getRequest()->getIsAjax()) {
                    throw new UnprocessableEntityHttpException(implode("<br>", $result));
                }
                Yii::$app->getSession()->setFlash('error', implode("<br>", $result));
            }
        }

        if (is_array($this->data)) {
            $data = $this->data;
        } elseif ($this->data instanceof Closure) {
            $data = call_user_func_array($this->data, [$id]);
        } else {
            throw new Exception("UpdateAction::data only allows array or closure (with return array)");
        }
        $this->viewFile === null && $this->viewFile = $this->id;
        Yii::$app->getRequest()->getIsGet() && Yii::$app->getSession()->set("_update_referer", Yii::$app->getRequest()->getReferrer());
        return $this->controller->render($this->viewFile, $data);
    }
}