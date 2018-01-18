<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-10-16 17:15
 */

namespace common\models;

use feehi\cdn\DummyTarget;
use Yii;

/**
 * This is the model class for table "{{%content}}".
 *
 * @property string $id
 * @property string $aid
 * @property string $content
 * @property Article $a
 */
class ArticleContent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article_content}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['aid'], 'required'],
            [['aid'], 'integer'],
            [['content'], 'string']
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
            'content' => Yii::t('app', 'Content'),
        ];
    }

    public function beforeSave($insert)
    {
        $this->content = str_replace(yii::$app->params['site']['url'], yii::$app->params['site']['sign'], $this->content);
        return true;
    }

    public function afterFind()
    {
        /** @var $cdn \feehi\cdn\TargetInterface */
        $cdn = yii::$app->get('cdn');
        if( $cdn instanceof DummyTarget){//未使用cdn
            $baseUrl = yii::$app->params['site']['url'];
        }else{
            $baseUrl = $cdn->host;
        }
        $this->content = str_replace(yii::$app->params['site']['sign'], $baseUrl, $this->content);
    }

}
