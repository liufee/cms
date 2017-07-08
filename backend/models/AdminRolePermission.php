<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\models;

use backend\models\Menu;
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

    public function assignPermission($role_id, $ids)
    {
        $oldPermissionIds = self::find()->where(['role_id' => $role_id])->indexBy('menu_id')->column();
        $needAddIds = array_diff($ids, $oldPermissionIds);
        if (! empty($needAddIds)) {
            foreach ($needAddIds as $menuId) {//新增
                $permissions = self::_getAncestor($menuId);
                foreach ($permissions as $v) {
                    $result = self::findOne(['role_id' => $role_id, 'menu_id' => $v['id']]);
                    if ($result != null) {
                        continue;
                    }
                    $model = new self();
                    $model->role_id = $role_id;
                    $model->menu_id = $v['id'];
                    $model->save();
                }
            }
        }
        $needRemoveIds = array_diff(array_keys($oldPermissionIds), $ids);//删除
        if (! empty($needRemoveIds)) {
            $removeIdsStr = implode(",", $needRemoveIds);
            self::deleteAll("menu_id in($removeIdsStr) && role_id=$role_id");
        }
    }

    private static function _getAncestor($id)
    {
        $arr = Menu::getMenuArray(Menu::BACKEND_TYPE);
        $par = array();
        foreach ($arr as $val) {
            if ($val['id'] == $id) {
                $par[] = $val;
                if ($val['parent_id'] != 0) {

                    $par = array_merge(static::_getAncestor($val['parent_id']), $par);
                }
            }
        }
        return $par;
    }

    /**
     * @param $role_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getPermissionsByRoleId($role_id)
    {
        return self::find()->leftJoin(Menu::tableName(), 'menu.id=' . self::tableName() . '.menu_id')->where(['role_id' => $role_id])->select('*')->asArray()->all();
    }

    public function checkPermission($route, $uid = '')
    {
        if ($uid == '') {
            $uid = yii::$app->getUser()->getIdentity()->getId();
        }
        $role_id = AdminRoleUser::getRoleId($uid);
        $permissions = self::getPermissionsByRoleId($role_id);//var_dump($permissions);die;
        foreach ($permissions as $v) {
            if (strtolower($v['url']) == strtolower($route)) {
                return true;
            }
        }
        return false;
    }
}