<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%comment}}".
 *
 * @property integer $id
 * @property integer $aid
 * @property integer $uid
 * @property integer $nickname
 * @property string $content
 * @property string $email
 * @property string $website_url
 * @property integer $reply_to
 * @property string $ip
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Comment extends \yii\db\ActiveRecord
{

    const STATUS_INIT = 0;
    const STATUS_PASSED = 1;
    const STATUS_UNPASS = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%comment}}';
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
            [['aid', 'uid', 'reply_to', 'status', 'created_at', 'updated_at'], 'integer'],
            [['content'], 'required'],
            [['content', 'nickname', 'email', 'website_url'], 'string'],
            [['ip'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'aid' => yii::t('app', 'Article Id'),
            'uid' => Yii::t('app', 'User Id'),
            'nickname' => Yii::t('app', 'Nickname'),
            'content' => Yii::t('app', 'Content'),
            'reply_to' => Yii::t('app', 'Replay User Id'),
            'ip' => Yii::t('app', 'IP Address'),
            'status' => Yii::t('app', 'Status'),
            'email' => Yii::t('app', 'Email'),
            'website_url' => Yii::t('app', 'Website'),
            'admin_id' => yii::t('app', 'Admin User Id'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticle()
    {
        return $this->hasOne(Article::className(), ['id' => 'aid']);
    }

    /**
     * @param $id
     * @return array
     */
    public function getCommentByAid($id)
    {
        $list = self::find()
            ->where(['aid' => $id, 'status' => self::STATUS_PASSED])
            ->orderBy("id desc,reply_to desc")
            ->all();
        $newList = [];
        foreach ($list as $row) {
            $v = $row->getAttributes();
            if ($v['reply_to'] == 0) {
                $v['sub'] = self::getCommentChildren($list, $v['id']);
                $newList[] = $v;
            }
        }
        return $newList;
    }

    /**
     * @param $list
     * @param $curId
     * @return array
     */
    public static function getCommentChildren($list, $curId)
    {
        $subComment = [];
        foreach ($list as $v) {
            if ($v['reply_to'] == $curId) {
                $subComment[] = $v;
            }
        }
        return $subComment;
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $this->content = str_replace([
            ':mrgreen:',
            ':razz:',
            ':sad:',
            ':smile:',
            ':oops:',
            ':grin:',
            ':eek:',
            ':???:',
            ':cool:',
            ':lol:',
            ':mad:',
            ':twisted:',
            ':roll:',
            ':wink:',
            ':idea:',
            ':arrow:',
            ':neutral:',
            ':cry:',
            ':?:',
            ':evil:',
            ':shock:',
            ':!:'
        ], [
            "<img src='{%URL%}mrgreen{%EXT%}'>",
            "<img src='{%URL%}razz{%EXT%}'>",
            "<img src='{%URL%}sad{%EXT%}'>",
            "<img src='{%URL%}smile{%EXT%}'>",
            "<img src='{%URL%}redface{%EXT%}'>",
            "<img src='{%URL%}biggrin{%EXT%}'>",
            "<img src='{%URL%}surprised{%EXT%}'>",
            "<img src='{%URL%}confused{%EXT%}'>",
            "<img src='{%URL%}cool{%EXT%}'>",
            "<img src='{%URL%}lol{%EXT%}'>",
            "<img src='{%URL%}mad{%EXT%}'>",
            "<img src='{%URL%}twisted{%EXT%}'>",
            "<img src='{%URL%}rolleyes{%EXT%}'>",
            "<img src='{%URL%}wink{%EXT%}'>",
            "<img src='{%URL%}idea{%EXT%}'>",
            "<img src='{%URL%}arrow{%EXT%}'>",
            "<img src='{%URL%}neutral{%EXT%}'>",
            "<img src='{%URL%}cry{%EXT%}'>",
            "<img src='{%URL%}question{%EXT%}'>",
            "<img src='{%URL%}evil{%EXT%}'>",
            "<img src='{%URL%}eek{%EXT%}'>",
            "<img src='{%URL%}exclaim{%EXT%}'>"
        ], $this->content);
        $this->content = str_replace([
            '{%URL%}',
            '{%EXT%}'
        ], [yii::$app->params['site']['url'] . '/static/images/smilies/icon_', '.gif'], $this->content);

    }

}