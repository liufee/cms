<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/21
 * Time: 19:36
 */
namespace feehi\grid;

use yii\helpers\ArrayHelper;
use feehi\libs\Constants;
class StatusColumn extends DataColumn
{

    public $attribute = 'status';
    public $headerOptions=['width'=>'25px'];

    public function init()
    {
        parent::init();
        $this->contentOptions=['class'=>'align-center'];
        $this->content = function($model,$key,$index,$gridView){
            return Constants::getDisplayItems($model->is_display);
        };
    }
}