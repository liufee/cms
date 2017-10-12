<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 01:00
 */

namespace backend\actions;

use \Closure;

class IndexAction extends \yii\base\Action
{

    public $data;

    public function run()
    {
        $data = $this->data;
        if( $data instanceof Closure){
            $data = call_user_func( $this->data );
        }
        return $this->controller->render('index', $data);
    }

}