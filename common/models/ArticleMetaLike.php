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


    /**
     * @param $aid
     * @param int $value
     * @return bool
     */
    public function setLike($aid, $value = 1)
    {
        $this->aid = $aid;
        $this->key = $this->tag;
        $this->value = $value;
        return $this->save();
    }

    /**
     * @param $aid
     * @return int|string
     */
    public function getLikeCount($aid)
    {
        return $this->find()->where(['aid' => $aid, 'key' => $this->tag])->count("id");
    }

}
