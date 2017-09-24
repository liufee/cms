<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-09-24 12:23
 */

namespace backend\form;


class Model extends \yii\base\Model
{
    private $oldModel = [];

    public function setOldModel($model)
    {
        $this->oldModel = $model;
    }

    public function getOldModel()
    {
        return $this->oldModel;
    }
}