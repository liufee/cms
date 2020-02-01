<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 01:00
 */

namespace backend\actions;

use Yii;
use Closure;
use backend\actions\helpers\Helper;

class IndexAction extends \yii\base\Action
{
    /**
     * @var string|array primary key(s) name
     */
    public $primaryKeyIdentity = null;

    /**
     * @var string primary keys(s) from (GET or POST)
     */
    public $primaryKeyFromMethod = "GET";

    /**
     * @var array|\Closure 分配到模板中去的变量
     */
    public $data;

    /** @var $viewFile string 模板路径，默认为action id  */
    public $viewFile = null;


    public function run()
    {
        $primaryKeys = Helper::getPrimaryKeys($this->primaryKeyIdentity, $this->primaryKeyFromMethod);
        $data = $this->data;
        if( $data instanceof Closure){
            $getDataParams = [];
            if( !empty($primaryKeys) ){
                foreach ($primaryKeys as $primaryKey) {
                    array_push($getDataParams, $primaryKey);
                }
            }
            array_push($getDataParams, Yii::$app->getRequest()->getQueryParams());
            $data = call_user_func_array( $this->data, $getDataParams );
        }
        $this->viewFile === null && $this->viewFile = $this->id;
        return $this->controller->render($this->viewFile, $data);
    }

}