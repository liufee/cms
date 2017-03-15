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

    public static function getRecentComments($limit = 10)
    {
        return self::find()->orderBy('created_at desc')->limit($limit)->all();
    }

}