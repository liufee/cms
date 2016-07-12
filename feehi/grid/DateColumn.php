<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/21
 * Time: 19:32
 */
namespace feehi\grid;

class DateColumn extends DataColumn
{
    public $headerOptions=['width'=>'120px'];

    public $format =['datetime', 'php:Y-m-d H:m:s'];

    public function init()
    {
        parent::init();
    }
}