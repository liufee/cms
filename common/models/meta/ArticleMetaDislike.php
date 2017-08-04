<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-11-10 10:50
 */

namespace common\models\meta;

class ArticleMetaDislike extends \common\models\ArticleMeta
{

    public $tag = "dislike";


    /**
     * @param $aid
     * @param int $value
     */
    public function setDislike($aid, $value = 1)
    {
        $this->aid = $aid;
        $this->key = $this->tag;
        $this->value = $value;
        $this->save();
    }

    /**
     * @param $aid
     * @return int|string
     */
    public function getDislikeCount($aid)
    {
        return $this->find()->where(['aid' => $aid, 'key' => $this->tag])->count("id");
    }

}