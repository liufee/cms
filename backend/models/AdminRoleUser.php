<?php
/**
 * Created by PhpStorm.
 * User: lf
 * Date: 16/4/11
 * Time: 22:32
 */

namespace backend\models;

use Yii;

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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'role_id', 'created_at', 'updated_at'], 'integer']
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

    public function beforeSave($insert)
    {
        if($insert){
            $this->created_at = time();
        }else{
            $this->updated_at = time();
        }
        return true;
    }

    public static function getRoleId($id = '')
    {
        if($id == '') $id = yii::$app->user->identity->getId();
        $result = self::find()->where(['uid'=>$id])->one();//createCommand()->getRawSql();
        return isset($result->role_id) ? $result->role_id : null;
    }
}