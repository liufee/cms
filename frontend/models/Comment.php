<?php
/**
 * Created by PhpStorm.
 * User: f
 * Date: 16/6/10
 * Time: 下午10:58
 */
namespace frontend\models;


use frontend\models\Article;


class Comment extends \common\models\Comment
{

    public function getArticle()
    {
        return $this->hasOne(Article::className(), ['id' => 'aid']);
    }


}