<?php
/**
 * Created by PhpStorm.
 * User: f
 * Date: 2016/12/25
 * Time: 下午9:58
 */
namespace feehi\components;

use feehi\assets\JqueryAsset;

class View extends \yii\web\View
{

    public function registerJs($js, $position = self::POS_READY, $key = null)
    {
        $key = $key ?: md5($js);
        $this->js[$position][$key] = $js;
        if ($position === self::POS_READY || $position === self::POS_LOAD) {
            JqueryAsset::register($this);
        }
    }

}