<?php
/**
 * Created by PhpStorm.
 * User: f
 * Date: 16/6/19
 * Time: 上午10:44
 */
namespace frontend\widgets;


use yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use frontend\models\Menu;

class MenuView extends \yii\base\Widget
{

    public $template = "<ul class=\"down-menu nav-menu\">{lis}</ul>";
    public $liTemplate = "<li id='menu-item-{menu_id}' class='menu-item menu-item-type-taxonomy menu-item-object-category {current_menu_class} menu-item-{menu_id}'><a href='{url}' target='{target}' style='padding: 13px;'>{title}</a></li>";
    public $subTemplate = "<ul class=\"sub-menu\" style=\"padding-top: 20px;\">{lis}</ul>";
    public $subLitemplate = "<li id=\"menu-item-{menu_id}\" class=\"menu-item menu-item-type-taxonomy menu-item-object-category {current_menu_class} menu-item-{menu_id}\"><a href=\"{url}\" style=\"padding: 13px;\">{title}</a></li>";

    public function run()
    {
        parent::run();
        $menus = Menu::getMenuArray(Menu::FRONTEND_TYPE);
        $content = '';
        $prevLevel = 0;
        $subLi = '';
        foreach ($menus as $menu){
            if($menu['is_absolute_url']) {
                $url = $menu['url'];
            }else{
                $url = Url::to([$menu['url']]);
            }
            $current_menu_class = '';
            if($menu['url'] == Yii::$app->controller->id.'/'.Yii::$app->controller->action->id) {
                $current_menu_class = ' current-menu-item ';
            }else if(yii::$app->request->getPathInfo() == $menu['url']){
                $current_menu_class = ' current-menu-item ';
            }
            $content .= str_replace(['{menu_id}', '{current_menu_class}', '{url}', '{target}', '{title}'], [$menu['id'], $current_menu_class, $url, $menu['target'], $menu['name']], $this->liTemplate);
        }
        echo str_replace('{lis}', $content, $this->template);
    }


}