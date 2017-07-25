<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-11 22:32
 */

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%admin_role_user}}".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $role_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class AdminRoleUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_role_user}}';
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
            [['uid', 'role_id', 'created_at', 'updated_at'], 'integer'],
            [
                ['role_id'],
                'required',
                'message' => yii::t('app', '{attribute} cannot be blank.', ['attribute' => yii::t('app', 'Roles')])
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'uid' => Yii::t('app', 'Uid'),
            'role_id' => Yii::t('app', 'Role ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * 根据管理员id获取角色id
     *
     * @param string $id 为空则返回已登陆的管理员角色id
     * @return mixed|null 管理员id未分配角色则返回null
     */
    public static function getRoleIdByUid($id = '')
    {
        if ($id == '') {
            $id = yii::$app->getUser()->getIdentity()->getId();
        }
        $result = self::find()->where(['uid' => $id])->one();//createCommand()->getRawSql();
        return isset($result->role_id) ? $result->role_id : null;
    }
}