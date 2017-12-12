<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-06-11 10:06
 */
namespace backend\models;

class Comment extends \common\models\Comment
{

    /**
     * @param int $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getRecentComments($limit = 10)
    {
        return self::find()->orderBy('created_at desc')->with('article')->limit($limit)->all();
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        $model = Article::findOne($this->aid);
        $model->comment_count -= 1;
        $model->save(false);
    }

}