<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%friend_link}}".
 *
 * @property string $id
 * @property string $name
 * @property string $image
 * @property string $url
 * @property string $target
 * @property string $sort
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class FriendlyLink extends \yii\db\ActiveRecord
{

    const DISPLAY_YES = 1;
    const DISPLAY_NO = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%friendly_link}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['target'], 'string'],
            [['sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['sort'], 'compare', 'compareValue' => 0, 'operator' => '>='],
            [['name', 'url'], 'string', 'max' => 255],
            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, webp'],
            [['name', 'url'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'image' => Yii::t('app', 'Image'),
            'url' => Yii::t('app', 'Url'),
            'target' => Yii::t('app', 'Target'),
            'sort' => Yii::t('app', 'Sort'),
            'status' => Yii::t('app', 'Is Display'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function afterFind()
    {
        /** @var $cdn \feehi\cdn\TargetAbstract $cdn */
        $cdn = yii::$app->get('cdn');
        $this->image = $cdn->getCdnUrl($this->image);
        parent::afterFind();
    }
}
