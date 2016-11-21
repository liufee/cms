<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/10
 * Time: 10:50
 */
namespace common\models;

class ArticleMetaDislike extends ArticleMeta
{
    public $tag = "dislike";

    public function setDislike($aid, $value=1)
    {
        $this->aid = $aid;
        $this->key = $this->tag;
        $this->value = $value;
        $this->save();
    }

    public function getDislikeCount($aid)
    {
        return $this->find()->where(['aid'=>$aid, 'key'=>$this->tag])->count("id");
    }

}