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

    function getCommentByAid($id)
    {
        $list = self::find()->where(['aid'=>$id, 'status'=>self::STATUS_PASSED])->asArray()->orderBy("id desc,reply_to desc")->all();
        $newList = [];
        foreach ($list as $v){
            if($v['reply_to'] == 0){
                $v['sub'] = self::getCommentChildren($list, $v['id']);
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
}