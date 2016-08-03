<?php
/**
 * Created by PhpStorm.
 * User: f
 * Date: 16/6/11
 * Time: 下午10:06
 */
namespace backend\models;

class Comment extends \common\models\Comment
{

    public static function getRecentComments($limit=10)
    {
        return self::find()->orderBy('created_at desc')->limit($limit)->all();
    }

}