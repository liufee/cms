<?php
/**
 * Created by PhpStorm.
 * User: lf
 * Date: 16/4/3
 * Time: 00:15
 */
namespace frontend\models;

use common\models\Menu as CommonMenu;
use yii\helpers\Url;

class Menu extends CommonMenu
{
    public static function getParentMenu()
    {
        $menusss = self::getMenuArray();
        $newMenu = [];//var_dump($menus);die;
        while(list($key, $val) = each($menusss)){
            $newMenu[$val['id']] = str_repeat("---", $val['level']).$val['name'];
        }
        return $newMenu;
    }

    public static function getMenus()
    {
        $data = self::find()->where(['is_display'=>self::DISPLAY_YES, 'type'=>SELF::FRONTEND_TYPE])->orderBy("sort asc,id asc")->asArray()->all();
        $str = '';
        foreach ($data as $v){
            $url = '';
            if($v['is_absolute_url']){
                $url = $v['url'];
            }else{
                $url = Url::to([$v['url']]);
            }
            $str .= "<a target='{$v['target']}' href='".$url."'>{$v['name']}</a>";
        }
        return $str;
    }

    public static function getMenuArray($type=1)
    {
        $model = new self();
        $menus = $model->find()->where(['type'=>$type])->orderBy("sort asc,parent_id asc")->asArray()->all();//var_dump($menus);die;
        $data = [];
        foreach ($menus as $key => $menu) {
            if ($menu['parent_id'] != 0) continue;
            $menu['level'] = 0;
            $menu['name'] = $menu['name'];
            $data[$menu['id']] = $menu;
            unset($menus[$key]);
        }
        return $data;
    }
}