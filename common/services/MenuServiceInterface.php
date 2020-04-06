<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-22 10:38
 */

namespace common\services;


use common\models\Menu;

interface MenuServiceInterface extends ServiceInterface {

    const ServiceName = 'menuService';

    public function getLevelMenusWithPrefixLevelCharacters($menuType = Menu::TYPE_BACKEND);
}