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
use yii\base\Exception;

/**
 * Index list page
 *
 * Class IndexAction
 * @package backend\actions
 */
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
     * @var array|\Closure assign to view variables
     */
    public $data;

    /** @var $viewFile string template view file path, default is action id */
    public $viewFile = null;


    /**
     * index list
     *
     * @return string
     * @throws Exception
     */
    public function run()
    {
        /**
         * get primary keys, often index list page no need primary keys
         */
        $primaryKeys = Helper::getPrimaryKeys($this->primaryKeyIdentity, $this->primaryKeyFromMethod);

        $data = $this->data;
        if( $data instanceof Closure){
            $params = [];
            if( !empty($primaryKeys) ){
                foreach ($primaryKeys as $primaryKey) {
                    array_push($params, $primaryKey);
                }
            }
            array_push($params, Yii::$app->getRequest()->getQueryParams());
            array_push($params, $this);
            //execute closure then assign to view, the closure params like function($_GET, primaryKeyValue1, primaryKeyValue1 ..., IndexAction)
            $data = call_user_func_array( $this->data, $params );
            if( !is_array($data) ){
                throw new Exception("data closure must return array");
            }
        }else if (!is_array($data) ){
            throw new Exception("data must be array or closure");
        }

        //default view template is action id
        $this->viewFile === null && $this->viewFile = $this->id;

        return $this->controller->render($this->viewFile, $data);
    }

}