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
 * This is the model class for table "{{%admin_log}}".
 *
 * @property integer $id
 * @property string $route
 * @property string $description
 * @property integer $created_at
 * @property integer $user_id
 */
class AdminLog extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_log}}';
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
            [['description'], 'string'],
            [['created_at', 'user_id'], 'integer'],
            [['route'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'route' => Yii::t('app', 'Route'),
            'description' => Yii::t('app', 'Description'),
            'created_at' => Yii::t('app', 'Created At'),
            'user_id' => Yii::t('app', 'Admin User Id'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function afterFind()
    {
        $this->description = str_replace([
            '{{%ADMIN_USER%}}',
            '{{%BY%}}',
            '{{%CREATED%}}',
            '{{%UPDATED%}}',
            '{{%DELETED%}}',
            '{{%ID%}}',
            '{{%RECORD%}}'
        ], [
            Yii::t('app', 'Admin user'),
            Yii::t('app', 'through'),
            Yii::t('app', 'created'),
            Yii::t('app', 'updated'),
            Yii::t('app', 'deleted'),
            Yii::t('app', 'id'),
            Yii::t('app', 'record')
        ], $this->description);
        $this->description = preg_replace_callback('/\(created_at\) : (\d{1,10})=>(\d{1,10})/', function ($matches) {
            return str_replace([$matches[1], $matches[2]], [Yii::$app->getFormatter()->asDate((int)$matches[1]), Yii::$app->getFormatter()->asDate((int)$matches[2])], $matches[0]);
        }, $this->description);
        $this->description = preg_replace_callback('/\(updated_at\) : (\d{1,10})=>(\d{1,10})/', function ($matches) {
            return str_replace([$matches[1], $matches[2]], [Yii::$app->getFormatter()->asDate((int)$matches[1]), Yii::$app->getFormatter()->asDate((int)$matches[2])], $matches[0]);
        }, $this->description);
        parent::afterFind();
    }

    /**
     * 删除日志不计入操作日志
     *
     * @return bool
     */
    public function afterDelete()
    {
        return false;
    }
}