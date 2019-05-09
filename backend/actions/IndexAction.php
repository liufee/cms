<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 01:00
 */

namespace backend\actions;

use Closure;

class IndexAction extends \yii\base\Action
{

    /**
     * @var array|\Closure 分配到模板中去的变量
     */
    public $data;

    /** @var $viewFile string 模板路径，默认为action id  */
    public $viewFile = null;


    public function run()
    {
        $data = $this->data;
        if( $data instanceof Closure){
            $data = call_user_func( $this->data );
        }
        $this->viewFile === null && $this->viewFile = $this->id;
        return $this->controller->render($this->viewFile, $data);
    }

}