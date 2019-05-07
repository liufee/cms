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

class Menu extends \common\models\Menu
{

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
        $object = Yii::createObject([
            'class' => FileDependencyHelper::className(),
            'fileName' => 'backend_menu.txt',
        ]);
        $object->updateFile();
    }

}
