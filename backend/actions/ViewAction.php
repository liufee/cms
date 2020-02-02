<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 10:10
 */

namespace backend\actions;

use Closure;
use backend\actions\helpers\Helper;
use yii\base\Exception;

class ViewAction extends \yii\base\Action
{

    /**
     * @var string|array primary key(s) name
     */
    public $primaryKeyIdentity = 'id';

    /**
     * @var string primary keys(s) from (GET or POST)
     */
    public $primaryKeyFromMethod = "GET";

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
            $primaryKeys = Helper::getPrimaryKeys($this->primaryKeyIdentity, $this->primaryKeyFromMethod);
            $getDataParams = $primaryKeys;
            array_push($getDataParams, $this);
            $data = call_user_func_array($this->data, $getDataParams);
        }else{
            throw new Exception(__CLASS__ . "::data only allows array or closure (with return array)");
        }

        return $this->controller->render($this->viewFile, $data);
    }
}