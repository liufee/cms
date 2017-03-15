<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-06-10 22:58
 */

namespace frontend\models;

use yii;

class Comment extends \common\models\Comment
{

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
                $this->ip = yii::$app->request->getUserIP();
            } else {
                $this->addError('content', yii::t('app', 'Website closed comment'));
                return false;
            }
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $model = Article::findOne($this->aid);
            $model->setScenario('article');
            $model->comment_count += 1;
            $model->save();
        }
        parent::afterSave($insert, $changedAttributes);
    }

}