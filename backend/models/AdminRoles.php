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
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "{{%admin_roles}}".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $role_name
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $remark
 */
class AdminRoles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_roles}}';
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
            [['parent_id', 'created_at', 'updated_at'], 'integer'],
            [['role_name', 'remark'], 'string', 'max' => 255],
            [['role_name'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'role_name' => Yii::t('app', 'Role'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'remark' => Yii::t('app', 'Remark'),
        ];
    }

    public static function getRolesNames()
    {
        $roles = self::find()->asArray()->all();
        $data = [];
        foreach ($roles as $role){
            $data[$role['id']] = $role['name'];
        }
        return $data;
    }

    /**
     * 根据管理员id获取角色名
     *
     * @param string $uid 管理员id
     * @return null|string 角色名字
     */
    public static function getRoleNameByUid($uid = '')
    {
        if ($uid == '') {
            $uid = yii::$app->getUser()->getIdentity()->getId();
        }
        $role_id = AdminRoleUser::getRoleIdByUid($uid);
        $data = self::findOne(['id' => $role_id]);
        return isset($data->role_name) ? $data->role_name : null;
    }

    public function beforeDelete()
    {
        if ($this->id == 1) {//不允许删除1号管理员用户
            throw new BadRequestHttpException(yii::t('app', 'Not allowed to delete {attribute}', ['attribute' => yii::t('app', 'super administrator roles')]));
        }
        return true;
    }
}