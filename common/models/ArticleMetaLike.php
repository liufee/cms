<?php

namespace common\models;

class ArticleMetaLike extends ArticleMeta
{
    public $tag = "like";

    public function setLike($aid, $value=1)
    {
        $this->aid = $aid;
        $this->key = $this->tag;
        $this->value = $value;
        return $this->save();
    }

    public function getLikeCount($aid)
    {
        return $this->find()->where(['aid'=>$aid, 'key'=>$this->tag])->count("id");
    }

}
