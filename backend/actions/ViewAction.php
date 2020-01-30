<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 10:10
 */

namespace backend\actions;


use Yii;
use Closure;
use yii\base\Exception;

class ViewAction extends \yii\base\Action
{

    /**
     * @var string
     */
   public $idSign = "id";

    /** @var array|Closure 分配到模板中去的变量 */
    public $data;

    /**
     * @var string 模板路径，默认为action id
     */
    public $viewFile = 'view';


    /**
     * view详情页
     *
     * @return string
     * @throws Exception
     */
    public function run()
    {
        if( is_array($this->data) ){
            $data = $this->data;
        }else if ($this->data instanceof Closure){
            $id = Yii::$app->getRequest()->get($this->idSign, null);
            $data = call_user_func_array($this->data, [$id]);
        }else{
            throw new Exception("ViewAction::data only allows array or closure (with return array)");
        }
        return $this->controller->render($this->viewFile, $data);
    }
}