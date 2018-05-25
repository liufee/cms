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
    public function init()
    {
        parent::init();
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'beforeSaveEvent']);
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'beforeSaveEvent']);
    }

    public function beforeSaveEvent($event)
    {
        $event->sender->type = self::FRONTEND_TYPE;
    }
}