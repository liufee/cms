<?php

namespace backend\models;

use Yii;
use yii\helpers\Url;
use common\models\Menu as CommonMenu;

class Menu extends CommonMenu
{
    public static function getBackendMenu(){
        $role_id = AdminRoleUser::getRoleId();
        $model = new \common\models\Menu();
        $menus = $model->find()->where(['is_display'=>1, 'type'=>\common\models\Menu::BACKEND_TYPE])->orderBy("sort asc")->all();
        $permissions = AdminRolePermission::getPermissionsByRoleId($role_id);
        $newMenu = [];
        if( !in_array(yii::$app->user->identity->username, yii::$app->rbac->getSuperAdministrators()) ){
            foreach ($menus as $menu) {
                foreach ($permissions as $permission) {
                    if ($menu['id'] == $permission['menu_id']) {
                        $newMenu[] = $menu;
                        break;
                    }
                }
            }
            $menus = $newMenu;
        }
        $lis = '';
        foreach ($menus as $menu) {
            if($menu->parent_id != 0) continue;
            $subMenu = self::_getBackendSubMenu($menus, $menu->id, '2');
            $menu->icon = $menu->icon ? $menu->icon : 'fa-home';
            $menu->url = Url::toRoute($menu->url);
            $arrow = '';
            $class = 'class="J_menuItem"';
            if($subMenu != '') {
                $arrow = ' arrow';
                $class = '';
            }
            $menu_name = yii::t('menu', $menu->name);
            $lis .=<<<EOF
                    <li>
                        <a {$class} href="{$menu->url}">
                            <i class="fa {$menu->icon}"></i>
                            <span class="nav-label">{$menu_name}</span>
                            <span class="fa {$arrow}"></span>
                        </a>
                        $subMenu
                    </li>
EOF;
        }
        return $lis;
    }

    private static function _getBackendSubMenu($menus, $cur_id, $times){
        $array = ['2'=>'second','3'=>'third','4'=>'fourth','5'=>'fifth'];
        $level = $array[$times];
        $collapse = '';
        if($times > 2) $collapse = "collapse";
        $subMenu = "<ul class='nav nav-{$level}-level {$collapse}'>";
        $times++;
        static $i = 1;
        foreach($menus as $menu){
            if($menu->parent_id != $cur_id) continue;
            $subsubmenu = self::_getBackendSubMenu($menus, $menu->id, $times);
            $url = Url::toRoute($menu->url);
            if($subsubmenu == ''){
                $arrow = '';
            }else{
                $arrow = '<span class="fa arrow"></span>';
            }
            $menu_name = yii::t('menu', $menu->name);
            $subMenu .= <<<EOF

                            <li>
                                <a class="J_menuItem" href="$url" data-index="$i">{$menu_name}{$arrow}</a>
                            $subsubmenu
                            </li>

EOF;
            $i++;
        }
        if($subMenu != "<ul class='nav nav-{$level}-level {$collapse}'>"){
            return $subMenu."</ul>";
        }else{
            return "";
        }

    }

}
