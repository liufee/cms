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

/**
 * backend view single record
 *
 * Class ViewAction
 * @package backend\actions
 */
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

    /** @var array|Closure variables will assigned to view */
    public $data;

    /**
     * @var string view template file path, default is action id
     */
    public $viewFile = 'view';


    /**
     * view detail page
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
            if( !is_array($data) ){
                throw new Exception("data closure must return array");
            }
        }else{
            throw new Exception(__CLASS__ . "::data only allows array or closure (with return array)");
        }

        return $this->controller->render($this->viewFile, $data);
    }
}