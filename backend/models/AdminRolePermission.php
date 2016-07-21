<?php

namespace backend\models;

use Yii;
use backend\models\Menu;
use backend\models\AdminRoleUser;

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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id', 'menu_id', 'created_at', 'updated_at'], 'integer'],
            [['name', 'url', 'method'], 'string', 'max' => 255]
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

    public function beforeSave($insert)
    {
        if($insert){
            $this->created_at = time();
        }else{
            $this->updated_at = time();
        }
        return true;
    }

    public function assignPermission($data){
        $role_id =  yii::$app->request->get('id');
        $ids = [];
        $oldPermissions = self::find(['role_id'=>$role_id])->indexBy('menu_id')->asArray()->column();
        foreach ($oldPermissions as $key => $v){
            if( !isset($data[$key]) ) array_push($ids, $v);
        }
        if(!empty($ids)){
            $ids = implode(",", $ids);
            self::deleteAll("id in($ids)");
        }//echo $ids;die;
        if(!empty($data)) {
            foreach ($data as $menu_id => $value) {
                $permissions = static::_getAncestor($menu_id);//获取家谱树
                foreach ($permissions as $v) {//添加权限
                    $result = self::findOne(['role_id' => $role_id, 'menu_id' => $v['id']]);
                    if ($result != null) continue;
                    $model = new self();
                    $model->role_id = $role_id;
                    $model->menu_id = $v['id'];
                    $model->name = $v['name'];
                    $model->url = $v['url'];
                    $model->method = $v['method'];
                    $model->save();
                }
            }
        }
    }

    private static function _getAncestor($id)
    {
        $arr = Menu::getMenuArray(Menu::BACKEND_TYPE);
        $par=array();
        foreach($arr as $val){
            if($val['id'] == $id){
                $par[]=$val;
                if($val['parent_id']!=0){

                    $par=array_merge(static::_getAncestor($val['parent_id']),$par)  ;
                }
            }
        }
        return $par;
    }

    public static function getPermissionsByRoleId($role_id)
    {
        return self::find()->where(['role_id'=>$role_id])->asArray()->all();
    }

    public function checkPermission($route, $uid='')
    {
        if($uid == '') $uid = yii::$app->user->identity->id;
        $role_id = AdminRoleUser::getRoleId($uid);
        $permissions = self::getPermissionsByRoleId($role_id);//var_dump($permissions);die;
        foreach($permissions as $v){
            if( strtolower($v['url']) == strtolower($route) ) return true;
        }
        return false;
    }
}