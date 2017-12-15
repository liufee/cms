<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-06-10 22:58
 */

namespace frontend\models;

use yii;
use yii\helpers\Html;

class Comment extends \common\models\Comment
{

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            if (yii::$app->feehi->website_comment) {
                if (! Article::find()->where(['id' => $this->aid])->one()['can_comment']) {
                    $this->addError('content', yii::t('frontend', 'This article is not allowed to comment'));
                    return false;
                }
                if (yii::$app->feehi->website_comment_need_verify) {
                    $this->status = self::STATUS_INIT;
                } else {
                    $this->status = self::STATUS_PASSED;
                }
                $this->ip = yii::$app->getRequest()->getUserIP();
                $this->uid = yii::$app->getUser()->getIsGuest() ? 0 : yii::$app->getUser()->getId();
            } else {
                $this->addError('content', yii::t('app', 'Website closed comment'));
                return false;
            }
        }
        $this->nickname = Html::encode($this->nickname);
        $this->email = Html::encode($this->email);
        if (stripos($this->website_url, 'http://') !== 0 && stripos($this->website_url, 'https://') !== 0) {
            $this->website_url = "http://" . $this->website_url;
        }
        $this->website_url = Html::encode($this->website_url);
        $this->content = Html::encode($this->content);
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $model = Article::findOne($this->aid);
            $model->comment_count += 1;
            $model->save(false);
        }
        parent::afterSave($insert, $changedAttributes);
    }

}