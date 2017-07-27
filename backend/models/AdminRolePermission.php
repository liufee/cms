<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%admin_role_permission}}".
 *
 * @property integer $id
 * @property integer $role_id
 * @property integer $menu_id
 * @property string $name
 * @property string $url
 * @property integer $created_at
 * @property integer $updated_at
 */
class AdminRolePermission extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_role_permission}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id', 'menu_id', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'role_id' => Yii::t('app', 'Role Id'),
            'menu_id' => Yii::t('app', 'Menu Id'),
            'name' => Yii::t('app', 'Name'),
            'url' => Yii::t('app', 'Url'),
            'method' => yii::t('app', 'Method'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * 为角色赋予权限
     *
     * @param integer $roleId 角色id
     * @param array $ids 需要授权的菜单id数组
     */
    public function assignPermission($roleId, $ids)
    {
        $oldPermissionIds = self::find()->where(['role_id' => $roleId])->indexBy('menu_id')->column();
        $needAddIds = array_diff($ids, $oldPermissionIds);
        if (! empty($needAddIds)) {
            foreach ($needAddIds as $menuId) {//新增
                $permissions = Menu::getAncectorsByMenuId($menuId);
                $permissions[] = Menu::findOne($menuId)->toArray();
                foreach ($permissions as $v) {
                    $result = self::findOne(['role_id' => $roleId, 'menu_id' => $v['id']]);
                    if ($result != null) {
                        continue;
                    }
                    $model = new self();
                    $model->role_id = $roleId;
                    $model->menu_id = $v['id'];
                    $model->save();
                }
            }
        }
        $needRemoveIds = array_diff(array_keys($oldPermissionIds), $ids);//删除
        if (! empty($needRemoveIds)) {
            $removeIdsStr = implode(",", $needRemoveIds);
            self::deleteAll("menu_id in($removeIdsStr) && role_id=$roleId");
        }
    }

    /**
     * 根据角色id获取所有权限路由
     *
     * @param $role_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getPermissionsByRoleId($role_id)
    {
        return self::find()->leftJoin(Menu::tableName(), 'menu.id=' . self::tableName() . '.menu_id')->where(['role_id' => $role_id])->select('*')->asArray()->all();
    }

    /**
     * 检查管理员是否有权限访问此路由
     *
     * @param string $route 路由
     * @param string $uid 管理员id
     * @return bool
     */
    public function checkPermission($route, $uid = '')
    {
        if ($uid == '') {
            $uid = yii::$app->getUser()->getIdentity()->getId();
        }
        $role_id = AdminRoleUser::getRoleIdByUid($uid);
        $permissions = self::getPermissionsByRoleId($role_id);//var_dump($permissions);die;
        foreach ($permissions as $v) {
            if (strtolower($v['url']) == strtolower($route)) {
                return true;
            }
        }
        return false;
    }
}