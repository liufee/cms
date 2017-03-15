<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\models;

use yii;
use yii\helpers\Url;

class Menu extends \common\models\Menu
{
    public static function getBackendMenu()
    {
        $role_id = AdminRoleUser::getRoleId();
        $model = new self();
        $menus = $model->find()->where(['is_display' => 1, 'type' => self::BACKEND_TYPE])->orderBy("sort asc")->all();
        $permissions = AdminRolePermission::getPermissionsByRoleId($role_id);
        $newMenu = [];
        if (! in_array(yii::$app->getUser()
                ->getIdentity()->username, yii::$app->rbac->getSuperAdministrators()) && yii::$app->getUser()
                ->getIdentity()
                ->getId() != 1 && $role_id != 1
        ) {
            foreach ($menus as $menu) {
                foreach ($permissions as $permission) {
                    if ($menu['url'] == $permission['url']) {
                        $newMenu[] = $menu;
                        break;
                    }
                }
            }
            $menus = $newMenu;
        }
        $lis = '';
        foreach ($menus as $menu) {
            if ($menu->parent_id != 0) {
                continue;
            }
            $subMenu = self::_getBackendSubMenu($menus, $menu->id, '2');
            $menu->icon = $menu->icon ? $menu->icon : 'fa-desktop';
            $menu->url = self::generateUrl($menu);
            $arrow = '';
            $class = 'class="J_menuItem"';
            if ($subMenu != '') {
                $arrow = ' arrow';
                $class = '';
            }
            $menu_name = yii::t('menu', $menu->name);
            $lis .= <<<EOF
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

    private static function _getBackendSubMenu($menus, $cur_id, $times)
    {
        $array = ['2' => 'second', '3' => 'third', '4' => 'fourth', '5' => 'fifth'];
        $level = $array[$times];
        $collapse = '';
        if ($times > 2) {
            $collapse = "collapse";
        }
        $subMenu = "<ul class='nav nav-{$level}-level {$collapse}'>";
        $times++;
        static $i = 1;
        foreach ($menus as $menu) {
            if ($menu->parent_id != $cur_id) {
                continue;
            }
            $subsubmenu = self::_getBackendSubMenu($menus, $menu->id, $times);
            $url = $menu->url = self::generateUrl($menu);
            if ($subsubmenu == '') {
                $arrow = '';
            } else {
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
        if ($subMenu != "<ul class='nav nav-{$level}-level {$collapse}'>") {
            return $subMenu . "</ul>";
        } else {
            return "";
        }

    }

    private static function generateUrl($menu)
    {
        if ($menu->url === '') {
            return '';
        } else {
            if ($menu->is_absolute_url == 1) {
                return $menu->url;
            } else {
                return Url::to([$menu->url]);
            }
        }
    }

    public static function getBackendMenuJson()
    {
        $adminRolePermissions = AdminRolePermission::find()->where([
            'role_id' => yii::$app->getRequest()->get('id', '')
        ])->indexBy('menu_id')->column();
        $model = new self();
        $menus = $model->find()->where(['type' => self::BACKEND_TYPE])->orderBy("sort asc")->all();
        $temp = [];
        foreach ($menus as $key => $menu) {
            if ($menu['parent_id'] == 0) {
                $m = [];
                $m['id'] = $menu['id'];
                $m['text'] = $menu['name'];
                if (isset($adminRolePermissions[$menu['id']])) {
                    $m['state'] = ['selected' => true];
                }
                $m['children'] = self::_getBackendSubMenuJson($menus, $menu['id'], $adminRolePermissions);
                if (self::_needSelected($m)) {
                    $m['state'] = ['selected' => true];
                } else {
                    $m['state'] = ['selected' => false];
                }
                array_push($temp, $m);
            }
        }
        return json_encode($temp);
    }

    private static function _getBackendSubMenuJson($menus, $cur_id, $adminRolePermissions)
    {
        $temp = [];
        foreach ($menus as $key => $menu) {
            if ($menu['parent_id'] == $cur_id) {
                $m = [];
                $m['id'] = $menu['id'];
                $m['text'] = $menu['name'];
                if (isset($adminRolePermissions[$menu['id']])) {
                    $m['state'] = ['selected' => true];
                }
                $m['children'] = self::_getBackendSubMenuJson($menus, $menu['id'], $adminRolePermissions);
                if (self::_needSelected($m)) {
                    $m['state'] = ['selected' => true];
                } else {
                    $m['state'] = ['selected' => false];
                }
                array_push($temp, $m);
            }
        }
        return $temp;
    }

    private static function _needSelected($children)
    {
        if (isset($children['children']) && empty($children['children'])) {
            if (isset($children['state']['selected']) && $children['state']['selected'] == true) {
                return true;
            } else {
                return false;
            }
        } else {
            foreach ($children as $child) {
                if (isset($child['state']['selected']) && $child['state']['selected'] == false) {
                    return true;
                } elseif (isset($child['children'])) {
                    self::_needSelected($child['children']);
                }
            }
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->removeBackendMenuCache();
    }

    public function afterDelete()
    {
        parent::afterDelete();
        $this->removeBackendMenuCache();
    }

    public function removeBackendMenuCache()
    {
        $object = yii::createObject([
            'class' => 'common\helpers\FileDependencyHelper',
            'fileName' => 'backend_menu.txt',
        ]);
        $object->updateFile();
    }

}
