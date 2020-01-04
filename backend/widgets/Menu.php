<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2019-05-07 11:21
 */

namespace backend\widgets;

use Yii;
use yii\base\InvalidArgumentException;

class Menu extends \yii\base\Widget
{

    /**
     * @var array menus
     */
    public $menus;

    /**
     * @var string first level li template
     */
    public $firstLevelLiTemplate = '<li><a class="{class}" href="{url}"><i class="fa {icon}"></i><span class="nav-label">{name}</span><span class="fa {arrow}"></span></a>{submenu}</li>';

    /**
     * @var string li template
     */
    public $liTemplate = '<li><a class="J_menuItem" href="{url}" data-index="{data-index}">{name}<span class="fa {arrow}"></span></a>{lis}</li>';

    /**
     * @var string ul template
     */
    public $ulTemplate = '<ul class="nav nav-{level}-level {collapse}">{lis}</ul>';

    /** @var array level names */
    private $_levelsName = ['2' => 'second', '3' => 'third', '4' => 'fourth', '5' => 'fifth', '6' => 'six'];

    public function run()
    {
        $menus = $this->menus;
        $lis = "";
        foreach ($menus as $menu){
            /** @var $menu \backend\models\Menu */
            if( intval($menu->parent_id) !== 0 ) continue;//parent_id equal 0 means first level menu
            $url = $menu->getMenuUrl();//get sub menus(recursive sub menus)
            $arrow = '';
            $class = "J_menuItem";
            $submenu = $this->getSubMenu($menu['id']);
            if ($submenu) {//first level menu has sub menus
                $arrow = ' arrow';//only has sub menus menu has a arrow icon
                $class = '';
            }
            $lis .= str_replace(["{class}", "{url}", "{icon}", "{name}", "{arrow}", "{submenu}"], [$class, $url, $menu['icon'], Yii::t('menu', $menu['name']), $arrow, $submenu], $this->firstLevelLiTemplate);
        }
        return $lis;
    }

    /**
     * get sub menu html
     *
     * @param int $menuId menu id
     * @param int $level menu level
     * @return mixed|string
     */
    private function getSubMenu($menuId, $level=2)
    {

        if( !isset($this->_levelsName[$level]) ) throw new InvalidArgumentException("Backend menu support max 6 levels");
        $levelName = $this->_levelsName[$level];
        $collapse = '';
        if ($level > 2) {
            $collapse = "collapse";
        }
        $level++;
        $lis = "";
        $menus = $this->menus;
        foreach ($menus as $menu) {
            /** @var \backend\models\Menu $menu */
            if ($menu->parent_id != $menuId) {//find menu id sub menus(parent id equal menu id)
                continue;//not the given menu's sub menu
            }
            $subSubmenu = $this->getSubMenu($menu->id, $level);//recursive get sub menus
            $arrow = 'arrow';
            $url = '';
            if ($subSubmenu == '') {
                $arrow = '';
                $url = $menu->getMenuUrl();
            }
            $lis .= str_replace(['{url}', '{data-index}', '{name}', '{arrow}', "{lis}"], [$url, $this->id, Yii::t('menu', $menu['name']), $arrow, $subSubmenu], $this->liTemplate);
        }
        $subMenu = "";
        if ($lis !== "") {
            $subMenu = str_replace(["{level}", "{collapse}", "{lis}"], [$levelName, $collapse, $lis], $this->ulTemplate);
        }
        return $subMenu;
    }
}