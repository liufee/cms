<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/21
 * Time: 18:46
 */
namespace feehi\grid;

class DataColumn extends \yii\grid\DataColumn
{

    public $headerOptions = [];

    public $width = '60px';

    public function init()
    {
        parent::init();

        if (! isset($this->headerOptions['width']))
        {
            $this->headerOptions['width'] = $this->width;
        }
        $this->contentOptions=['style'=>'word-wrap: break-word; word-break: break-all;'];
    }
}