<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-22 10:38
 */

namespace common\services;

use Yii;
use backend\models\search\MenuSearch;
use common\helpers\FileDependencyHelper;
use common\models\Menu;
use yii\caching\FileDependency;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

class MenuService extends Service  implements MenuServiceInterface
{

    public function getSearchModel(array $options=[])
    {
        return new MenuSearch();
    }

    public function getModel($id, array $options = [])
    {
        return Menu::findOne($id);
    }

    public function newModel(array $options = [])
    {
        $menu = new Menu();
        $menu->type = Menu::TYPE_BACKEND;
        if($options['type'] === Menu::TYPE_FRONTEND){
            $menu->type = Menu::TYPE_FRONTEND;
        }
        $menu->loadDefaultValues();
        return $menu;
    }

    public function getList(array $query = [], array $options = [])
    {
        return [
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $this->getLevelMenusWithPrefixLevelCharacters($options['type']),
                'pagination' => [
                    'pageSize' => -1,
                ],
            ])
        ];
    }

    /**
     * get authorized backend menus by admin user id
     *
     * @param $userId
     * @return array|mixed|\yii\db\ActiveRecord[]
     * @throws \yii\base\InvalidConfigException
     */
    public function getAuthorizedBackendMenusByUserId($userId)
    {
        $menus = $this->getMenus(Menu::TYPE_BACKEND, Menu::DISPLAY_YES);
        $permissions = Yii::$app->getAuthManager()->getPermissionsByUser($userId);
        $permissions = array_keys($permissions);
        if (in_array(Yii::$app->getUser()->getId(), Yii::$app->getBehavior('access')->superAdminUserIds)) {
            return $menus;//config user ids own all permissions
        }

        $tempMenus = [];
        foreach ($menus as $menu) {
            /** @var Menu $menu */
            $url = $menu->url;
            $temp = @json_decode($menu->url, true);
            if ($temp !== null) {//menu url store json format
                $url = $temp[0];
            }
            if (strpos($url, '/') !== 0) $url = '/' . $url;//ensure url must start with '/'
            $url = $url . ':GET';
            if (in_array($url, $permissions)) {
                $menu = $menu->getAncestors($menu->id) + [$menu];
                $tempMenus = array_merge($tempMenus, $menu);
            }
        }

        $existMenuIds = [];
        $hasPermissionMenus = [];
        foreach ($tempMenus as $v) {
            /** @var Menu $v */
            if( in_array($v->id, $existMenuIds) ) {
                continue;
            }
            $hasPermissionMenus[] = $v;
            $existMenuIds[] = $v->id;
        }
        ArrayHelper::multisort($hasPermissionMenus, 'sort', SORT_ASC);
        return $hasPermissionMenus;
    }

    /**
     * set menu name with prefix level characters
     *
     * @param $menuType
     * @param $isDisplay
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getLevelMenusWithPrefixLevelCharacters($menuType = Menu::TYPE_BACKEND)
    {
        $model = $this->newModel(['type' => $menuType]);
        $menus = $model->getDescendants(0);
        foreach ($menus as $k => $menu) {
            /** @var Menu $menu */
            if (isset($menus[$k + 1]['level']) && $menus[$k + 1]['level'] == $menu['level']) {
                $name = ' ├' . $menu['name'];
            } else {
                $name = ' └' . $menu['name'];
            }
            if (end($menus)->id == $menu->id) {
                $sign = ' └';
            } else {
                $sign = ' │';
            }
            $menu->prefix_level_name = str_repeat($sign, $menu['level'] - 1) . $name;
        }
        return ArrayHelper::index($menus, 'id');
    }

    /**
     * get menus from cache, if cache not exist then get from storage and set to cache
     *
     * @param $menuType
     * @param $isDisplay
     * @return array|mixed|\yii\db\ActiveRecord[]
     * @throws \yii\base\InvalidConfigException
     */
    public function getMenus($menuType=null, $isDisplay=null){
        $cacheKey = "menu_" . (string)$menuType . "_" . (string)$isDisplay;
        //echo $cacheKey;exit;
        $cache = Yii::$app->getCache();
        $menus = $cache->get($cacheKey);
        if( $menus === false || !is_array($menus) ){
            $cacheDependencyObject = Yii::createObject([
                'class' => FileDependencyHelper::className(),
                'fileName' => Menu::MENU_CACHE_DEPENDENCY_FILE,
            ]);
            $dependency = [
                'class' => FileDependency::className(),
                'fileName' => $cacheDependencyObject->createFileIfNotExists(),
            ];
            $menus = $this->getMenusFromStorage($menuType, $isDisplay);
            if ( $cache->set($cacheKey, $menus, 60*60, Yii::createObject($dependency)) === false ){
                Yii::error(__METHOD__ . " save menu cache error");
            }
        }
        return $menus;
    }

    /**
     * get menus from storage
     *
     * @param $menuType
     * @param $isDisplay
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getMenusFromStorage($menuType=null, $isDisplay=null){
        return Menu::getMenus($menuType, $isDisplay);
    }
}