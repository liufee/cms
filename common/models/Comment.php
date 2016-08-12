<?php

namespace common\models;

use frontend\models\Article as ArticleModel;
use Yii;

/**
 * This is the model class for table "{{%comment}}".
 *
 * @property integer $id
 * @property integer $aid
 * @property integer $uid
 * @property integer $nickname
 * @property string $content
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
            'aid' => Yii::t('app', 'Aid'),
            'uid' => Yii::t('app', 'Uid'),
            'nickname' => Yii::t('app', '昵称'),
            'content' => Yii::t('app', '内容'),
            'reply_to' => Yii::t('app', '回复给'),
            'ip' => Yii::t('app', 'Ip'),
            'status' => Yii::t('app', 'Status'),
            'email' => Yii::t('app', '邮箱'),
            'website_url' => Yii::t('app', '网址'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function getArticle()
    {
        return $this->hasOne(ArticleModel::className(), ['id' => 'aid']);
    }

    public function beforeSave($insert)
    {
        if($insert){
            $this->created_at = time();
            $this->ip = yii::$app->request->getUserIP();
            if(yii::$app->feehi->website_comment){
                if(yii::$app->feehi->website_comment_need_verify){
                    $this->status = self::STATUS_INIT;
                }else{
                    $this->status = self::STATUS_PASSED;
                }
            }else{
                $this->addError('content', 'Comment closed');
                return false;
            }
        }else{
            $this->updated_at = time();
        }
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if($insert){
            $model = ArticleModel::findOne($this->aid);
            $model->setScenario('article');
            $model->comment_count += 1;
            $model->save();
        }
        return true;
    }

    public function getCommentByAid($id)
    {
        $list = self::find()->where(['aid'=>$id, 'status'=>self::STATUS_PASSED])->asArray()->orderBy("id desc,reply_to desc")->all();
        $newList = [];
        foreach ($list as $v){
            if($v['reply_to'] == 0){
                $v['sub'] = self::getCommentChildren($list, $v['id']);
                $v['content'] = str_replace([':mrgreen:',':razz:',':sad:',':smile:',':oops:',':grin:',':eek:',':???:',':cool:',':lol:',':mad:',':twisted:',':roll:',':wink:',':idea:',':arrow:',':neutral:',':cry:',':?:',':evil:',':shock:',':!:'],
                    ["<img src='{%URL%}mrgreen{%EXT%}'>","<img src='{%URL%}razz{%EXT%}'>","<img src='{%URL%}sad{%EXT%}'>","<img src='{%URL%}smile{%EXT%}'>","<img src='{%URL%}redface{%EXT%}'>","<img src='{%URL%}biggrin{%EXT%}'>","<img src='{%URL%}surprised{%EXT%}'>","<img src='{%URL%}confused{%EXT%}'>","<img src='{%URL%}cool{%EXT%}'>","<img src='{%URL%}lol{%EXT%}'>","<img src='{%URL%}mad{%EXT%}'>","<img src='{%URL%}twisted{%EXT%}'>","<img src='{%URL%}rolleyes{%EXT%}'>","<img src='{%URL%}wink{%EXT%}'>","<img src='{%URL%}idea{%EXT%}'>","<img src='{%URL%}arrow{%EXT%}'>","<img src='{%URL%}neutral{%EXT%}'>","<img src='{%URL%}cry{%EXT%}'>","<img src='{%URL%}question{%EXT%}'>","<img src='{%URL%}evil{%EXT%}'>","<img src='{%URL%}eek{%EXT%}'>","<img src='{%URL%}exclaim{%EXT%}'>"],$v['content']);
                $v['content'] = str_replace(['{%URL%}', '{%EXT%}'], [yii::$app->homeUrl.'static/images/smilies/icon_', '.gif'], $v['content']);
                $newList[] = $v;
            }
        }
        return $newList;
    }

    public static function getCommentChildren($list, $cur_id)
    {
        $subComment = [];
        foreach ($list as $v){
            if($v['reply_to'] == $cur_id){
                $subComment[] = $v;
            }
        }
        return $subComment;
    }

    public function afterFind()
    {
        $this->content = str_replace([':mrgreen:',':razz:',':sad:',':smile:',':oops:',':grin:',':eek:',':???:',':cool:',':lol:',':mad:',':twisted:',':roll:',':wink:',':idea:',':arrow:',':neutral:',':cry:',':?:',':evil:',':shock:',':!:'],
            ["<img src='{%URL%}mrgreen{%EXT%}'>","<img src='{%URL%}razz{%EXT%}'>","<img src='{%URL%}sad{%EXT%}'>","<img src='{%URL%}smile{%EXT%}'>","<img src='{%URL%}redface{%EXT%}'>","<img src='{%URL%}biggrin{%EXT%}'>","<img src='{%URL%}surprised{%EXT%}'>","<img src='{%URL%}confused{%EXT%}'>","<img src='{%URL%}cool{%EXT%}'>","<img src='{%URL%}lol{%EXT%}'>","<img src='{%URL%}mad{%EXT%}'>","<img src='{%URL%}twisted{%EXT%}'>","<img src='{%URL%}rolleyes{%EXT%}'>","<img src='{%URL%}wink{%EXT%}'>","<img src='{%URL%}idea{%EXT%}'>","<img src='{%URL%}arrow{%EXT%}'>","<img src='{%URL%}neutral{%EXT%}'>","<img src='{%URL%}cry{%EXT%}'>","<img src='{%URL%}question{%EXT%}'>","<img src='{%URL%}evil{%EXT%}'>","<img src='{%URL%}eek{%EXT%}'>","<img src='{%URL%}exclaim{%EXT%}'>"],$this->content);
        $this->content = str_replace(['{%URL%}', '{%EXT%}'], [yii::$app->params['site']['url'].'/static/images/smilies/icon_', '.gif'], $this->content);

    }
}