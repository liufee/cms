<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-03 00:15
 */

namespace frontend\models;

use common\models\Menu as CommonMenu;

class Menu extends CommonMenu
{

    public function beforeSave($insert)
    {
        $this->type = self::FRONTEND_TYPE;
        return parent::beforeSave($insert);
    }
}