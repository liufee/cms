<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\models;

use Yii;
use common\helpers\FileDependencyHelper;
use common\helpers\FamilyTree;
use yii\helpers\ArrayHelper;

class Menu extends \common\models\Menu
{

    CONST BACKEND_MENU_CACHE_DEPENDENCY_FILE = "backend_menu.txt";

    /**
     * get authencated menus by user id
     *
     * @param $userId
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAuthencatedMenus($userId){
        $menus = self::find()->where(['is_display' => self::DISPLAY_YES, 'type' => self::BACKEND_TYPE])->orderBy("sort asc")->all();
        $permissions = Yii::$app->getAuthManager()->getPermissionsByUser($userId);
        $permissions = array_keys($permissions);

        if (in_array(Yii::$app->getUser()->getId(), Yii::$app->getBehavior('access')->superAdminUserIds)){
            return $menus;//config user ids own all permissions
        }

        $tempMenus = [];
        foreach ($menus as $menu) {
            /** @var self $menu */
            $url = $menu->url;
            $temp = @json_decode($menu->url, true);
            if ($temp !== null) {
                $url = $temp[0];
            }
            if (strpos($url, '/') !== 0) $url = '/' . $url;
            $url = $url . ':GET';
            if (in_array($url, $permissions)) {
                $menu = self::getAncectorsByMenuId($menu->id) + [$menu];
                $tempMenus = array_merge($tempMenus, $menu);
            }
        }

        $hasPermissionMenus = [];
        foreach ($tempMenus as $v) {
            $hasPermissionMenus[] = $v;
        }
        ArrayHelper::multisort($hasPermissionMenus, 'sort', SORT_ASC);
        return $hasPermissionMenus;

    }

    /**
     * 根据menu id获取祖先菜单
     *
     * @param string $id 菜单id
     * @return array
     */
    public static function getAncectorsByMenuId($id)
    {
        $menus = self::_getMenus(self::BACKEND_TYPE);
        $familyTree = new FamilyTree($menus);
        return $familyTree->getAncectors($id);
    }

    /**
     * 根据menu id获取子孙菜单
     *
     * @param string $id 菜单id
     * @return array
     */
    public static function getDescendantsByMenuId($id)
    {
        $menus = self::_getMenus(self::BACKEND_TYPE);
        $familyTree = new FamilyTree($menus);
        return $familyTree->getDescendants($id);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->removeBackendMenuCache();
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete()
    {
        $this->removeBackendMenuCache();
        parent::afterDelete();
    }

    public function removeBackendMenuCache()
    {
        /** @var FileDependencyHelper $object */
        $object = Yii::createObject([
            'class' => FileDependencyHelper::className(),
            'fileName' => self::BACKEND_MENU_CACHE_DEPENDENCY_FILE,
        ]);
        $object->updateFile();
    }

}
