<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-11-10 10:50
 */

namespace common\models;

class ArticleMetaDislike extends ArticleMeta
{
    public $tag = "dislike";

    public function setDislike($aid, $value = 1)
    {
        $this->aid = $aid;
        $this->key = $this->tag;
        $this->value = $value;
        $this->save();
    }

    public function getDislikeCount($aid)
    {
        return $this->find()->where(['aid' => $aid, 'key' => $this->tag])->count("id");
    }

}