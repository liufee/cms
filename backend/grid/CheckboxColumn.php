<?php
/**
 * Created by PhpStorm.
 * User: lf
 * Date: 16/4/14
 * Time: 21:53
 */

namespace backend\grid;


class CheckboxColumn extends  \yii\grid\CheckboxColumn
{

    public $width = '10px';

    public function init()
    {
        parent::init();
        
        if (! isset($this->headerOptions['width']))
        {
            $this->headerOptions['width'] = $this->width;
        }
    }

}