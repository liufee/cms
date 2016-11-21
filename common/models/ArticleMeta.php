<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%article_meta}}".
 *
 * @property string $id
 * @property string $aid
 * @property string $key
 * @property string $value
 * @property string $ip
 * @property string $created_at
 */
class ArticleMeta extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    'createdAtAttribute'
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article_meta}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['aid'], 'required'],
            [['aid', 'created_at'], 'integer'],
            [['key'], 'string', 'max' => 255],
            [['ip'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'aid' => Yii::t('app', 'Aid'),
            'key' => Yii::t('app', 'Key'),
            'value' => Yii::t('app', 'Value'),
            'ip' => Yii::t('app', 'Ip'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    public function beforeSave($insert)
    {
        $this->ip = yii::$app->getRequest()->userIP;
        return parent::beforeSave($insert);
    }
}
