<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-10-16 17:15
 */

namespace common\models;

use Yii;
use feehi\cdn\DummyTarget;

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
        /** @var $cdn \feehi\cdn\TargetInterface */
        $cdn = Yii::$app->get('cdn');
        if( $cdn instanceof DummyTarget){//未使用cdn
            $baseUrl = Yii::$app->params['site']['url'];
        }else{
            $baseUrl = $cdn->host;
        }
        $this->content = str_replace($baseUrl, Yii::$app->params['site']['sign'], $this->content);
        return true;
    }

    public function afterFind()
    {
        /** @var $cdn \feehi\cdn\TargetInterface */
        $cdn = Yii::$app->get('cdn');
        if( $cdn instanceof DummyTarget){//未使用cdn
            $baseUrl = Yii::$app->params['site']['url'];
        }else{
            $baseUrl = $cdn->host;
        }
        $this->content = str_replace(Yii::$app->params['site']['sign'], $baseUrl, $this->content);

        if (! isset(Yii::$app->params['cdnUrl']) || Yii::$app->params['cdnUrl'] == '') {
            return;
        }
        if (strpos($this->content, 'src="/uploads"')) {
            $pattern = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
            preg_match_all($pattern, $this->content, $matches);
            $matches[1] = array_unique($matches[1]);
            foreach ($matches[1] as $v) {
                $this->content = str_replace($v, Yii::$app->params['cdnUrl'] . $v, $this->content);
            }
        } else {
            $this->content = str_replace(Yii::$app->params['site']['url'], Yii::$app->params['cdnUrl'], $this->content);
        }
    }
}
