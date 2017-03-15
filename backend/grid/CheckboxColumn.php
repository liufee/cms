<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-14 21:53
 */

namespace backend\grid;

class CheckboxColumn extends \yii\grid\CheckboxColumn
{

    public $width = '10px';

    public function init()
    {
        parent::init();

        if (! isset($this->headerOptions['width'])) {
            $this->headerOptions['width'] = $this->width;
        }
    }

}