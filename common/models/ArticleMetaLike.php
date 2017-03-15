<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace common\models;

class ArticleMetaLike extends ArticleMeta
{
    public $tag = "like";

    public function setLike($aid, $value = 1)
    {
        $this->aid = $aid;
        $this->key = $this->tag;
        $this->value = $value;
        return $this->save();
    }

    public function getLikeCount($aid)
    {
        return $this->find()->where(['aid' => $aid, 'key' => $this->tag])->count("id");
    }

}
