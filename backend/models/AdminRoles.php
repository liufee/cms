<?php

namespace backend\models;

use Yii;

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

    public function beforeSave($insert)
    {
        if($insert){
            $this->created_at = time();
        }else{
            $this->updated_at = time();
        }
        return true;
    }

    public static function getRoleNameByUid($uid = '')
    {
        if( $uid == '' ) $uid = yii::$app->user->identity->getId();
        $role_id = AdminRoleUser::getRoleId($uid);
        $data = self::findOne(['id'=>$role_id]);
        return isset($data->role_name) ? $data->role_name : null;
    }

    public function beforeDelete()
    {
        if($this->id == 1) throw new \yii\web\ForbiddenHttpException(yii::t('app', 'Not allowed to delete {attribute}', ['attribute'=>yii::t('app', 'super administrator roles')]));
        return true;
    }
}