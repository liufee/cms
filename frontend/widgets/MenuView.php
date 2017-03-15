<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-06-19 10:44
 */

namespace frontend\widgets;


use yii;
use yii\helpers\Url;
use frontend\models\Menu;

class MenuView extends \yii\base\Widget
{

    public $template = "<ul class=\"down-menu nav-menu\">{lis}</ul>";
    public $liTemplate = "<li id='menu-item-{menu_id}' class='menu-item menu-item-type-taxonomy menu-item-object-category {current_menu_class} menu-item-{menu_id}'><a href='{url}' target='{target}' style='padding: 13px;'>{title}</a>{sub_menu}</li>";
    public $subTemplate = "<ul class=\"sub-menu\" style=\"padding-top: 20px;\">{lis}</ul>";
    public $subLitemplate = "<li id=\"menu-item-{menu_id}\" class=\"menu-item menu-item-type-taxonomy menu-item-object-category {current_menu_class} menu-item-{menu_id}\"><a href=\"{url}\" target='{target}' style=\"padding: 13px;\">{title}</a></li>";

    public function run()
    {
        parent::run();
        $menus = Menu::find()
            ->where(['type' => Menu::FRONTEND_TYPE, 'is_display' => Menu::DISPLAY_YES])
            ->orderBy("sort asc,parent_id asc")
            ->asArray()
            ->all();
        $content = '';
        foreach ($menus as $key => $menu) {
            if ($menu['parent_id'] == 0) {
                if (empty($menu['url'])) {
                    $url = 'javascript:void(0)';
                } else {
                    if ($menu['is_absolute_url']) {
                        $url = $menu['url'];
                    } else {
                        $url = Url::to([$menu['url']]);
                    }
                }
                $current_menu_class = '';
                if ($url == yii::$app->getRequest()->getUrl()) {
                    $current_menu_class = ' current-menu-item ';
                }
                unset($menus[$key]);
                $submenu = $this->getSubMenu($menus, $menu['id']);
                $content .= str_replace([
                    '{menu_id}',
                    '{current_menu_class}',
                    '{url}',
                    '{target}',
                    '{title}',
                    '{sub_menu}'
                ], [
                    $menu['id'],
                    $current_menu_class,
                    $url,
                    $menu['target'],
                    $menu['name'],
                    $submenu
                ], $this->liTemplate);
            }
        }
        echo str_replace('{lis}', $content, $this->template);
    }

    private function getSubMenu($menus, $cur_id)
    {
        $content = '';
        foreach ($menus as $key => $menu) {
            if ($menu['parent_id'] == $cur_id) {
                if (empty($menu['url'])) {
                    $url = 'javascript:void(0)';
                } else {
                    if ($menu['is_absolute_url']) {
                        $url = $menu['url'];
                    } else {
                        $url = Url::to([$menu['url']]);
                    }
                }
                $current_menu_class = '';
                if ($menu['url'] == Yii::$app->controller->id . '/' . Yii::$app->controller->action->id) {
                    $current_menu_class = ' current-menu-item ';
                } else {
                    if (yii::$app->request->getPathInfo() == $menu['url']) {
                        $current_menu_class = ' current-menu-item ';
                    }
                }
                $content .= str_replace([
                    '{menu_id}',
                    '{current_menu_class}',
                    '{url}',
                    '{target}',
                    '{title}'
                ], [$menu['id'], $current_menu_class, $url, $menu['target'], $menu['name']], $this->subLitemplate);
                unset($menus[$key]);
            }
        }
        if ($content != '') {
            return str_replace('{lis}', $content, $this->subTemplate);
        } else {
            return '';
        }
    }


}