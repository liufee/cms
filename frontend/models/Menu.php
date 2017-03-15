<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-03 00:15
 */
namespace frontend\models;

use common\models\Menu as CommonMenu;
use yii\helpers\Url;

class Menu extends CommonMenu
{

    public static function getMenus()
    {
        $data = self::find()
            ->where(['is_display' => self::DISPLAY_YES, 'type' => SELF::FRONTEND_TYPE])
            ->orderBy("sort asc,id asc")
            ->asArray()
            ->all();
        $str = '';
        foreach ($data as $v) {
            $url = '';
            if ($v['is_absolute_url']) {
                $url = $v['url'];
            } else {
                $url = Url::to([$v['url']]);
            }
            $str .= "<a target='{$v['target']}' href='" . $url . "'>{$v['name']}</a>";
        }
        return $str;
    }

}